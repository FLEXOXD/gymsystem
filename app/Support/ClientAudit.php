<?php

namespace App\Support;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class ClientAudit
{
    public const ROLE_LEGACY = 'legacy';

    /**
     * @return array<string, mixed>
     */
    public static function creationAttributesFromUser(User $user, ?CarbonInterface $managedAt = null): array
    {
        return array_merge([
            'created_by' => (int) $user->id,
            'created_by_name_snapshot' => self::actorName($user),
            'created_by_role_snapshot' => self::roleKey($user),
        ], self::managementAttributesFromUser($user, $managedAt));
    }

    /**
     * @return array<string, mixed>
     */
    public static function managementAttributesFromUser(User $user, ?CarbonInterface $managedAt = null): array
    {
        return self::managementAttributes(
            userId: (int) $user->id,
            nameSnapshot: self::actorName($user),
            roleSnapshot: self::roleKey($user),
            managedAt: $managedAt,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function legacyAttributes(string $label = 'Registro previo', ?CarbonInterface $managedAt = null): array
    {
        $snapshot = trim($label) !== '' ? trim($label) : 'Registro previo';

        return array_merge([
            'created_by' => null,
            'created_by_name_snapshot' => $snapshot,
            'created_by_role_snapshot' => self::ROLE_LEGACY,
        ], self::managementAttributes(
            userId: null,
            nameSnapshot: $snapshot,
            roleSnapshot: self::ROLE_LEGACY,
            managedAt: $managedAt,
        ));
    }

    public static function roleLabel(?string $roleSnapshot): string
    {
        return match (strtolower(trim((string) $roleSnapshot))) {
            User::ROLE_OWNER => 'Dueno',
            User::ROLE_CASHIER => 'Cajero',
            User::ROLE_EMPLOYEE => 'Empleado',
            User::ROLE_SUPERADMIN => 'Superadmin',
            self::ROLE_LEGACY => 'Registro previo',
            default => 'Usuario',
        };
    }

    public static function actorDisplay(?string $nameSnapshot, ?string $roleSnapshot): string
    {
        $name = trim((string) $nameSnapshot);
        $role = strtolower(trim((string) $roleSnapshot));

        if ($role === self::ROLE_LEGACY) {
            return $name !== '' ? $name : self::roleLabel($role);
        }

        if ($name === '') {
            return self::roleLabel($role);
        }

        return $name.' ('.self::roleLabel($role).')';
    }

    public static function linkedUserState(?User $linkedUser, ?string $roleSnapshot): ?string
    {
        $role = strtolower(trim((string) $roleSnapshot));

        if ($role === self::ROLE_LEGACY) {
            return 'Registro previo';
        }

        if (! $linkedUser) {
            return 'Usuario eliminado';
        }

        return $linkedUser->isActiveAccount() ? null : 'Usuario archivado';
    }

    private static function actorName(User $user): string
    {
        $name = trim((string) ($user->name ?? ''));

        return $name !== '' ? $name : 'Usuario';
    }

    private static function roleKey(User $user): string
    {
        return $user->roleKey();
    }

    /**
     * @return array<string, mixed>
     */
    private static function managementAttributes(
        ?int $userId,
        string $nameSnapshot,
        string $roleSnapshot,
        ?CarbonInterface $managedAt = null
    ): array {
        return [
            'last_managed_by' => $userId,
            'last_managed_by_name_snapshot' => $nameSnapshot,
            'last_managed_by_role_snapshot' => $roleSnapshot,
            'last_managed_at' => $managedAt ? Carbon::instance($managedAt) : now(),
        ];
    }
}
