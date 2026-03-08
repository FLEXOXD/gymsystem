<?php

namespace App\Console\Commands;

use App\Services\LegacyDbcolaImportService;
use Illuminate\Console\Command;
use Throwable;

class ImportLegacyDbcolaUsersCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'legacy:import-dbcola-users
        {path? : Ruta al archivo SQL legacy}
        {--basic-name=DB Cola Basico : Nombre del gym basico}
        {--basic-slug=dbcola-basico : Slug del gym basico}
        {--basic-owner-name=Owner DB Cola Basico : Nombre del owner del gym basico}
        {--basic-owner-email=dbcola.basico@gymsystem.local : Correo del owner basico}
        {--basic-owner-password=DbcolaBasico#2026 : Contrasena del owner basico}
        {--premium-name=DB Cola Premium : Nombre del gym premium}
        {--premium-slug=dbcola-premium : Slug del gym premium}
        {--premium-owner-name=Owner DB Cola Premium : Nombre del owner del gym premium}
        {--premium-owner-email=dbcola.premium@gymsystem.local : Correo del owner premium}
        {--premium-owner-password=DbcolaPremium#2026 : Contrasena del owner premium}';

    /**
     * @var string
     */
    protected $description = 'Importa clientes legacy de DB Cola a dos gyms locales: uno basico y otro premium.';

    public function __construct(
        private readonly LegacyDbcolaImportService $importService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $path = (string) ($this->argument('path') ?: LegacyDbcolaImportService::DEFAULT_SQL_PATH);

        try {
            $result = $this->importService->import($path, [
                'basic_name' => (string) $this->option('basic-name'),
                'basic_slug' => (string) $this->option('basic-slug'),
                'basic_owner_name' => (string) $this->option('basic-owner-name'),
                'basic_owner_email' => (string) $this->option('basic-owner-email'),
                'basic_owner_password' => (string) $this->option('basic-owner-password'),
                'premium_name' => (string) $this->option('premium-name'),
                'premium_slug' => (string) $this->option('premium-slug'),
                'premium_owner_name' => (string) $this->option('premium-owner-name'),
                'premium_owner_email' => (string) $this->option('premium-owner-email'),
                'premium_owner_password' => (string) $this->option('premium-owner-password'),
            ]);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Importacion DB Cola completada.');
        $this->line('Clientes legacy detectados: '.$result['legacy_clients_total']);
        $this->line('Mensualidades legacy detectadas: '.$result['legacy_monthly_payments_total']);

        foreach (['basic' => 'Basico', 'premium' => 'Premium'] as $key => $label) {
            /** @var array{gym:\App\Models\Gym,owner:\App\Models\User,summary:array<string,int>,memberships:array<string,int>} $context */
            $context = $result[$key];
            $this->newLine();
            $this->line('['.$label.'] Gym: '.$context['gym']->name.' | slug: '.$context['gym']->slug);
            $this->line('['.$label.'] Owner: '.$context['owner']->email);
            $this->line('['.$label.'] Password: '.($key === 'basic' ? $this->option('basic-owner-password') : $this->option('premium-owner-password')));
            $this->line('['.$label.'] Clientes creados: '.$context['summary']['created'].' | actualizados: '.$context['summary']['updated']);
            $this->line('['.$label.'] QR activos: '.$context['summary']['with_qr']);
            $this->line('['.$label.'] Planes legacy creados: '.$context['memberships']['plans_created'].' | actualizados: '.$context['memberships']['plans_updated']);
            $this->line('['.$label.'] Membresias creadas: '.$context['memberships']['memberships_created'].' | actualizadas: '.$context['memberships']['memberships_updated'].' | omitidas: '.$context['memberships']['skipped']);
            if ($key === 'premium') {
                $this->line('['.$label.'] Cuentas app con hash legacy: '.$context['summary']['with_app_account']);
                $this->line('['.$label.'] Clientes premium sin hash app: '.$context['summary']['without_app_account']);
            }
        }

        return self::SUCCESS;
    }
}
