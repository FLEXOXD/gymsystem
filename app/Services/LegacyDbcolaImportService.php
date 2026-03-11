<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientCredential;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\SuperAdminPlanTemplate;
use App\Models\User;
use App\Support\ClientAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class LegacyDbcolaImportService
{
    public const DEFAULT_SQL_PATH = 'C:\\Users\\FLEXO\\Downloads\\gimnasio_dbcola.sql';

    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    /**
     * @param  array{
     *     basic_name:string,
     *     basic_slug:string,
     *     basic_owner_name:string,
     *     basic_owner_email:string,
     *     basic_owner_password:string,
     *     premium_name:string,
     *     premium_slug:string,
     *     premium_owner_name:string,
     *     premium_owner_email:string,
     *     premium_owner_password:string
     * }  $config
     * @return array<string, mixed>
     */
    public function import(string $sqlPath, array $config): array
    {
        if (! is_file($sqlPath)) {
            throw new InvalidArgumentException('No existe el archivo SQL indicado: '.$sqlPath);
        }

        $sql = file_get_contents($sqlPath);
        if ($sql === false || trim($sql) === '') {
            throw new InvalidArgumentException('No se pudo leer el archivo SQL indicado.');
        }

        $legacyUsers = $this->parseUsers($sql);
        if ($legacyUsers === []) {
            throw new InvalidArgumentException('El archivo SQL no contiene registros importables en la tabla usuarios.');
        }
        $legacyUsersById = [];
        foreach ($legacyUsers as $legacyUser) {
            $legacyUserId = (int) ($legacyUser['id'] ?? 0);
            if ($legacyUserId > 0) {
                $legacyUsersById[$legacyUserId] = $legacyUser;
            }
        }

        $legacyClients = array_values(array_filter(
            $legacyUsers,
            static fn (array $row): bool => strtolower((string) ($row['tipo'] ?? '')) === 'cliente'
        ));

        if ($legacyClients === []) {
            throw new InvalidArgumentException('No se encontraron clientes legacy para importar.');
        }
        $legacyMonthlyPayments = $this->parseMonthlyPayments($sql);

        SuperAdminPlanTemplate::ensureDefaultCatalog();

        $basicTemplate = $this->resolvePlanTemplate('basico');
        $premiumTemplate = $this->resolvePlanTemplate('premium');

        return DB::transaction(function () use ($config, $legacyClients, $legacyMonthlyPayments, $legacyUsersById, $basicTemplate, $premiumTemplate): array {
            $basicContext = $this->upsertGymContext(
                name: $config['basic_name'],
                slug: $config['basic_slug'],
                ownerName: $config['basic_owner_name'],
                ownerEmail: $config['basic_owner_email'],
                ownerPassword: $config['basic_owner_password'],
                planTemplate: $basicTemplate
            );

            $premiumContext = $this->upsertGymContext(
                name: $config['premium_name'],
                slug: $config['premium_slug'],
                ownerName: $config['premium_owner_name'],
                ownerEmail: $config['premium_owner_email'],
                ownerPassword: $config['premium_owner_password'],
                planTemplate: $premiumTemplate
            );

            $basicSummary = $this->syncClients($legacyClients, $basicContext['gym'], false);
            $premiumSummary = $this->syncClients($legacyClients, $premiumContext['gym'], true);
            $basicMembershipSummary = $this->syncMemberships($legacyMonthlyPayments, $legacyUsersById, $basicContext['gym']);
            $premiumMembershipSummary = $this->syncMemberships($legacyMonthlyPayments, $legacyUsersById, $premiumContext['gym']);

            return [
                'legacy_clients_total' => count($legacyClients),
                'legacy_monthly_payments_total' => count($legacyMonthlyPayments),
                'basic' => [
                    'gym' => $basicContext['gym'],
                    'owner' => $basicContext['owner'],
                    'summary' => $basicSummary,
                    'memberships' => $basicMembershipSummary,
                ],
                'premium' => [
                    'gym' => $premiumContext['gym'],
                    'owner' => $premiumContext['owner'],
                    'summary' => $premiumSummary,
                    'memberships' => $premiumMembershipSummary,
                ],
            ];
        });
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function parseUsers(string $sql): array
    {
        return $this->parseTableRows($sql, 'usuarios');
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function parseMonthlyPayments(string $sql): array
    {
        return $this->parseTableRows($sql, 'mensualidades');
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function parseTableRows(string $sql, string $table): array
    {
        preg_match_all('/INSERT INTO `'.preg_quote($table, '/').'`\s*\((.*?)\)\s*VALUES\s*(.*?);/is', $sql, $statements, PREG_SET_ORDER);

        $rows = [];
        foreach ($statements as $statement) {
            $columns = $this->parseColumnList($statement[1] ?? '');
            $tuples = $this->parseTuples($statement[2] ?? '');

            foreach ($tuples as $tuple) {
                $values = $this->parseTupleValues($tuple);
                if (count($columns) !== count($values)) {
                    continue;
                }

                /** @var array<string, string|null> $row */
                $row = array_combine($columns, $values);
                if (! is_array($row)) {
                    continue;
                }

                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * @return list<string>
     */
    private function parseColumnList(string $rawColumns): array
    {
        $parts = array_map('trim', explode(',', $rawColumns));

        return array_values(array_filter(array_map(static function (string $column): string {
            return trim($column, " \t\n\r\0\x0B`");
        }, $parts), static fn (string $column): bool => $column !== ''));
    }

    /**
     * @return list<string>
     */
    private function parseTuples(string $valuesSql): array
    {
        $tuples = [];
        $buffer = '';
        $depth = 0;
        $inString = false;
        $escaped = false;

        $length = strlen($valuesSql);
        for ($index = 0; $index < $length; $index++) {
            $character = $valuesSql[$index];

            if ($inString) {
                $buffer .= $character;
                if ($escaped) {
                    $escaped = false;
                    continue;
                }
                if ($character === '\\') {
                    $escaped = true;
                    continue;
                }
                if ($character === "'") {
                    $inString = false;
                }
                continue;
            }

            if ($character === "'") {
                $inString = true;
                $buffer .= $character;
                continue;
            }

            if ($character === '(') {
                if ($depth === 0) {
                    $buffer = '';
                }
                $depth++;
                $buffer .= $character;
                continue;
            }

            if ($character === ')') {
                $buffer .= $character;
                $depth--;
                if ($depth === 0) {
                    $tuples[] = $buffer;
                    $buffer = '';
                }
                continue;
            }

            if ($depth > 0) {
                $buffer .= $character;
            }
        }

        return $tuples;
    }

    /**
     * @return list<string|null>
     */
    private function parseTupleValues(string $tuple): array
    {
        $content = trim($tuple);
        $content = preg_replace('/^\(|\)$/', '', $content) ?? $content;

        $values = [];
        $buffer = '';
        $inString = false;
        $escaped = false;

        $length = strlen($content);
        for ($index = 0; $index < $length; $index++) {
            $character = $content[$index];

            if ($inString) {
                if ($escaped) {
                    $buffer .= $character;
                    $escaped = false;
                    continue;
                }
                if ($character === '\\') {
                    $escaped = true;
                    continue;
                }
                if ($character === "'") {
                    $inString = false;
                    continue;
                }

                $buffer .= $character;
                continue;
            }

            if ($character === "'") {
                $inString = true;
                continue;
            }

            if ($character === ',') {
                $values[] = $this->normalizeSqlValue($buffer);
                $buffer = '';
                continue;
            }

            $buffer .= $character;
        }

        $values[] = $this->normalizeSqlValue($buffer);

        return $values;
    }

    private function normalizeSqlValue(string $rawValue): ?string
    {
        $trimmed = trim($rawValue);
        if (strcasecmp($trimmed, 'NULL') === 0) {
            return null;
        }

        $value = str_replace(["\\'", '\\"', '\\\\', '\r', '\n', '\t'], ["'", '"', '\\', "\r", "\n", "\t"], $trimmed);
        $value = $this->repairLegacyText($value);

        return trim($value);
    }

    private function repairLegacyText(?string $value): string
    {
        $text = trim((string) $value);
        if ($text === '') {
            return '';
        }

        if (! preg_match('/Ã|Â|�/u', $text)) {
            return $text;
        }

        $converted = @mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        if (is_string($converted) && trim($converted) !== '') {
            return trim($converted);
        }

        return $text;
    }

    private function resolvePlanTemplate(string $planKey): SuperAdminPlanTemplate
    {
        return SuperAdminPlanTemplate::query()
            ->where('plan_key', $planKey)
            ->where('status', 'active')
            ->firstOrFail();
    }

    /**
     * @return array{gym:Gym,owner:User}
     */
    private function upsertGymContext(
        string $name,
        string $slug,
        string $ownerName,
        string $ownerEmail,
        string $ownerPassword,
        SuperAdminPlanTemplate $planTemplate
    ): array {
        $gym = Gym::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'slug' => $slug,
                'phone' => null,
                'address' => 'Importado desde DB Cola',
                'address_country_code' => 'ec',
                'address_country_name' => 'Ecuador',
                'address_state' => 'Cotopaxi',
                'address_city' => 'Latacunga',
                'address_line' => 'Importacion local DB Cola',
                'timezone' => 'America/Guayaquil',
                'currency_code' => 'USD',
                'language_code' => 'es',
            ]
        );

        $gym->forceFill([
            'name' => $name,
            'address' => 'Importado desde DB Cola',
            'address_country_code' => 'ec',
            'address_country_name' => 'Ecuador',
            'address_state' => 'Cotopaxi',
            'address_city' => 'Latacunga',
            'address_line' => 'Importacion local DB Cola',
            'timezone' => 'America/Guayaquil',
            'currency_code' => 'USD',
            'language_code' => 'es',
        ])->save();

        $owner = User::query()
            ->where('gym_id', (int) $gym->id)
            ->where('role', User::ROLE_OWNER)
            ->first();

        $emailOwner = User::query()
            ->where('email', $ownerEmail)
            ->first();

        if ($emailOwner && (! $owner || (int) $emailOwner->id !== (int) $owner->id)) {
            throw new InvalidArgumentException('El correo '.$ownerEmail.' ya existe en otra cuenta. Usa otro correo para el importador.');
        }

        if (! $owner) {
            $owner = User::query()->create([
                'gym_id' => (int) $gym->id,
                'name' => $ownerName,
                'email' => $ownerEmail,
                'country_iso' => 'EC',
                'country_name' => 'Ecuador',
                'address_state' => 'Cotopaxi',
                'address_city' => 'Latacunga',
                'address_line' => 'Importacion local DB Cola',
                'phone_country_iso' => 'EC',
                'phone_country_dial' => '+593',
                'phone_number' => null,
                'role' => User::ROLE_OWNER,
                'password' => Hash::make($ownerPassword),
                'timezone' => 'America/Guayaquil',
                'is_active' => true,
            ]);
        } else {
            $owner->forceFill([
                'name' => $ownerName,
                'email' => $ownerEmail,
                'country_iso' => 'EC',
                'country_name' => 'Ecuador',
                'address_state' => 'Cotopaxi',
                'address_city' => 'Latacunga',
                'address_line' => 'Importacion local DB Cola',
                'phone_country_iso' => 'EC',
                'phone_country_dial' => '+593',
                'role' => User::ROLE_OWNER,
                'password' => Hash::make($ownerPassword),
                'timezone' => 'America/Guayaquil',
                'is_active' => true,
            ])->save();
        }

        $this->subscriptionService->applyPlanTemplate(
            gymId: (int) $gym->id,
            planTemplate: [
                'template_id' => (int) $planTemplate->id,
                'plan_key' => (string) $planTemplate->plan_key,
                'feature_version' => (string) config('plan_features.default_feature_version', 'v1'),
                'name' => (string) $planTemplate->name,
                'price' => (float) $planTemplate->price,
                'duration_unit' => (string) $planTemplate->duration_unit,
                'duration_days' => (int) $planTemplate->duration_days,
                'duration_months' => $planTemplate->duration_months !== null ? (int) $planTemplate->duration_months : null,
            ],
            paymentMethod: null
        );

        return [
            'gym' => $gym->fresh(),
            'owner' => $owner->fresh(),
        ];
    }

    /**
     * @param  array<int, array<string, string|null>>  $legacyClients
     * @return array{created:int,updated:int,with_app_account:int,without_app_account:int,with_qr:int}
     */
    private function syncClients(array $legacyClients, Gym $gym, bool $enableClientAccounts): array
    {
        $created = 0;
        $updated = 0;
        $withAppAccount = 0;
        $withoutAppAccount = 0;
        $withQr = 0;
        $reservedUsernames = [];

        foreach ($legacyClients as $legacyClient) {
            $documentNumber = $this->normalizeDocumentNumber(
                $legacyClient['cÃ©dula'] ?? null,
                (int) ($legacyClient['id'] ?? 0)
            );
            [$firstName, $lastName] = $this->splitLegacyName($legacyClient['nombre'] ?? null);
            $phone = $this->normalizePhone($legacyClient['telÃ©fono'] ?? null);
            $hasUsablePassword = $this->isBcryptHash($legacyClient['contraseÃ±a'] ?? null);

            $appUsername = null;
            $appPassword = null;
            if ($enableClientAccounts) {
                $appUsername = $this->buildUniqueAppUsername(
                    legacyId: (int) ($legacyClient['id'] ?? 0),
                    email: $legacyClient['correo'] ?? null,
                    reserved: $reservedUsernames
                );
                $appPassword = $hasUsablePassword ? trim((string) $legacyClient['contraseÃ±a']) : null;
            }

            $client = Client::query()
                ->where('gym_id', (int) $gym->id)
                ->where('document_number', $documentNumber)
                ->first();

            $payload = [
                'gym_id' => (int) $gym->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'document_number' => $documentNumber,
                'phone' => $phone,
                'photo_path' => null,
                'status' => 'active',
                'app_username' => $appUsername,
                'app_password' => $appPassword,
            ];

            if (! $client) {
                $client = Client::query()->create(array_merge(
                    $payload,
                    ClientAudit::legacyAttributes('Importacion legacy')
                ));
                $created++;
            } else {
                $client->forceFill($payload)->save();

                if (
                    trim((string) ($client->created_by_name_snapshot ?? '')) === ''
                    || trim((string) ($client->last_managed_by_name_snapshot ?? '')) === ''
                    || $client->last_managed_at === null
                ) {
                    $client->forceFill(array_merge(
                        array_filter([
                            'created_by_name_snapshot' => trim((string) ($client->created_by_name_snapshot ?? '')) === '' ? 'Importacion legacy' : null,
                            'created_by_role_snapshot' => trim((string) ($client->created_by_role_snapshot ?? '')) === '' ? ClientAudit::ROLE_LEGACY : null,
                        ], static fn ($value): bool => $value !== null),
                        array_filter([
                            'last_managed_by_name_snapshot' => trim((string) ($client->last_managed_by_name_snapshot ?? '')) === '' ? 'Importacion legacy' : null,
                            'last_managed_by_role_snapshot' => trim((string) ($client->last_managed_by_role_snapshot ?? '')) === '' ? ClientAudit::ROLE_LEGACY : null,
                            'last_managed_at' => $client->last_managed_at === null ? now() : null,
                        ], static fn ($value): bool => $value !== null)
                    ))->save();
                }

                $updated++;
            }

            if ($enableClientAccounts && $appUsername !== null) {
                if ($appPassword !== null) {
                    $withAppAccount++;
                } else {
                    $withoutAppAccount++;
                }
            }

            $qrValue = $this->buildQrValue((int) ($legacyClient['id'] ?? 0), $legacyClient['qr_path'] ?? null);
            if ($qrValue !== null) {
                ClientCredential::query()->updateOrCreate(
                    [
                        'gym_id' => (int) $gym->id,
                        'type' => 'qr',
                        'value' => $qrValue,
                    ],
                    [
                        'client_id' => (int) $client->id,
                        'status' => 'active',
                    ]
                );
                $withQr++;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'with_app_account' => $withAppAccount,
            'without_app_account' => $withoutAppAccount,
            'with_qr' => $withQr,
        ];
    }

    /**
     * @param  array<int, array<string, string|null>>  $legacyMonthlyPayments
     * @param  array<int, array<string, string|null>>  $legacyUsersById
     * @return array{plans_created:int,plans_updated:int,memberships_created:int,memberships_updated:int,skipped:int}
     */
    private function syncMemberships(array $legacyMonthlyPayments, array $legacyUsersById, Gym $gym): array
    {
        if ($legacyMonthlyPayments === []) {
            return [
                'plans_created' => 0,
                'plans_updated' => 0,
                'memberships_created' => 0,
                'memberships_updated' => 0,
                'skipped' => 0,
            ];
        }

        $planPriceByMonths = $this->resolveMonthlyPlanPrices($legacyMonthlyPayments);
        $planMap = [];
        $plansCreated = 0;
        $plansUpdated = 0;

        foreach ($planPriceByMonths as $months => $price) {
            $planName = $this->legacyPlanName($months);
            $plan = Plan::query()
                ->where('gym_id', (int) $gym->id)
                ->where('name', $planName)
                ->first();

            $payload = [
                'gym_id' => (int) $gym->id,
                'name' => $planName,
                'duration_days' => max(1, $months * 30),
                'duration_unit' => 'months',
                'duration_months' => $months,
                'price' => $price,
                'status' => 'active',
            ];

            if (! $plan) {
                $plan = Plan::query()->create($payload);
                $plansCreated++;
            } else {
                $plan->forceFill($payload)->save();
                $plansUpdated++;
            }

            $planMap[$months] = $plan;
        }

        $membershipsCreated = 0;
        $membershipsUpdated = 0;
        $skipped = 0;

        foreach ($legacyMonthlyPayments as $payment) {
            $legacyClientId = (int) ($payment['cliente_id'] ?? 0);
            if ($legacyClientId <= 0 || ! array_key_exists($legacyClientId, $legacyUsersById)) {
                $skipped++;
                continue;
            }

            $legacyUser = $legacyUsersById[$legacyClientId];
            $documentNumber = $this->normalizeDocumentNumber($legacyUser['cÃ©dula'] ?? null, $legacyClientId);
            $client = Client::query()
                ->where('gym_id', (int) $gym->id)
                ->where('document_number', $documentNumber)
                ->first();

            if (! $client) {
                $skipped++;
                continue;
            }

            $months = max(1, (int) ($payment['meses_acumulados'] ?? 1));
            $plan = $planMap[$months] ?? null;
            $startsAt = $this->normalizeLegacyDate($payment['fecha_pago'] ?? null);
            $endsAt = $this->normalizeLegacyDate($payment['fecha_fin'] ?? null);

            if (! $plan || $startsAt === null || $endsAt === null) {
                $skipped++;
                continue;
            }

            $status = match (strtolower(trim((string) ($payment['estado'] ?? '')))) {
                'vigente' => 'active',
                'caducado', 'renovado' => 'expired',
                default => 'expired',
            };
            $price = max(0, (float) ($payment['monto'] ?? 0));

            $membership = Membership::query()
                ->where('gym_id', (int) $gym->id)
                ->where('client_id', (int) $client->id)
                ->where('plan_id', (int) $plan->id)
                ->whereDate('starts_at', $startsAt)
                ->whereDate('ends_at', $endsAt)
                ->where('price', $price)
                ->first();

            $payload = [
                'gym_id' => (int) $gym->id,
                'client_id' => (int) $client->id,
                'plan_id' => (int) $plan->id,
                'price' => $price,
                'promotion_id' => null,
                'promotion_name' => null,
                'promotion_type' => null,
                'promotion_value' => null,
                'discount_amount' => 0,
                'bonus_days' => 0,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => $status,
            ];

            if (! $membership) {
                Membership::query()->create($payload);
                $membershipsCreated++;
            } else {
                $membership->forceFill($payload)->save();
                $membershipsUpdated++;
            }

            $clientStatus = $this->resolveClientStatusFromMemberships((int) $gym->id, (int) $client->id);
            if ((string) ($client->status ?? '') !== $clientStatus) {
                $client->forceFill(['status' => $clientStatus])->save();
            }
        }

        return [
            'plans_created' => $plansCreated,
            'plans_updated' => $plansUpdated,
            'memberships_created' => $membershipsCreated,
            'memberships_updated' => $membershipsUpdated,
            'skipped' => $skipped,
        ];
    }

    /**
     * @param  array<int, array<string, string|null>>  $legacyMonthlyPayments
     * @return array<int, float>
     */
    private function resolveMonthlyPlanPrices(array $legacyMonthlyPayments): array
    {
        $buckets = [];

        foreach ($legacyMonthlyPayments as $payment) {
            $months = max(1, (int) ($payment['meses_acumulados'] ?? 1));
            $price = round(max(0, (float) ($payment['monto'] ?? 0)), 2);
            $bucketKey = number_format($price, 2, '.', '');
            $buckets[$months] ??= [];
            $buckets[$months][$bucketKey] = ($buckets[$months][$bucketKey] ?? 0) + 1;
        }

        ksort($buckets);

        $resolved = [];
        foreach ($buckets as $months => $counts) {
            arsort($counts);
            $topPrice = (string) array_key_first($counts);
            $resolved[$months] = (float) $topPrice;
        }

        return $resolved;
    }

    private function legacyPlanName(int $months): string
    {
        return $months === 1 ? 'Legacy 1 mes' : 'Legacy '.$months.' meses';
    }

    private function normalizeLegacyDate(?string $value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '' || strtoupper($raw) === 'NULL') {
            return null;
        }

        $datePart = explode(' ', $raw)[0] ?? '';

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $datePart) === 1 ? $datePart : null;
    }

    private function resolveClientStatusFromMemberships(int $gymId, int $clientId): string
    {
        $hasActiveMembership = Membership::query()
            ->where('gym_id', $gymId)
            ->where('client_id', $clientId)
            ->where('status', 'active')
            ->exists();

        return $hasActiveMembership ? 'active' : 'inactive';
    }

    private function normalizeDocumentNumber(?string $value, int $legacyId): string
    {
        $document = trim((string) $value);
        if ($document !== '') {
            return $document;
        }

        return 'LEGACY-USER-'.$legacyId;
    }

    /**
     * @return array{0:string,1:string}
     */
    private function splitLegacyName(?string $value): array
    {
        $name = preg_replace('/\s+/u', ' ', trim((string) $value)) ?? '';
        if ($name === '') {
            return ['Cliente', 'Legacy'];
        }

        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($parts) === 1) {
            return [$parts[0], 'Legacy'];
        }

        if (count($parts) === 2) {
            return [$parts[0], $parts[1]];
        }

        $lastNameParts = array_slice($parts, -2);
        $firstNameParts = array_slice($parts, 0, -2);
        if ($firstNameParts === []) {
            $firstNameParts = [array_shift($parts) ?? 'Cliente'];
            $lastNameParts = $parts !== [] ? $parts : ['Legacy'];
        }

        return [
            implode(' ', $firstNameParts),
            implode(' ', $lastNameParts),
        ];
    }

    private function normalizePhone(?string $value): ?string
    {
        $phone = trim((string) $value);

        return $phone !== '' ? $phone : null;
    }

    /**
     * @param  array<int, string>  $reserved
     */
    private function buildUniqueAppUsername(int $legacyId, ?string $email, array &$reserved): string
    {
        $base = trim(Str::lower((string) $email));
        $base = str_replace('@', '.', $base);
        $base = preg_replace('/[^a-z0-9._-]+/', '.', $base) ?? '';
        $base = preg_replace('/\.{2,}/', '.', $base) ?? '';
        $base = trim($base, '.-_');

        if ($base === '' || strlen($base) < 4) {
            $base = 'legacy.user.'.$legacyId;
        }

        $candidate = mb_substr($base, 0, 80);
        $suffix = 2;
        while (in_array($candidate, $reserved, true)) {
            $suffixText = '.'.$suffix;
            $candidate = mb_substr($base, 0, max(1, 80 - strlen($suffixText))).$suffixText;
            $suffix++;
        }

        $reserved[] = $candidate;

        return $candidate;
    }

    private function isBcryptHash(?string $value): bool
    {
        $password = trim((string) $value);

        return $password !== '' && str_starts_with($password, '$2y$');
    }

    private function buildQrValue(int $legacyId, ?string $qrPath): ?string
    {
        $legacyQr = trim((string) $qrPath);
        if ($legacyQr === '' && $legacyId <= 0) {
            return null;
        }

        return 'legacy-dbcola-qr-'.$legacyId;
    }
}
