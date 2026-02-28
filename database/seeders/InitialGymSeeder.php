<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InitialGymSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gym = Gym::firstOrCreate(
            ['slug' => 'iron-will'],
            [
                'name' => 'IRON WILL',
                'phone' => null,
                'address' => null,
                'logo_path' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@ironwill.test'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'gym_id' => $gym->id,
                'role' => User::ROLE_OWNER,
            ]
        );

        $today = Carbon::today();
        Subscription::query()->updateOrCreate(
            ['gym_id' => $gym->id],
            [
                'plan_name' => 'Plan Mensual',
                'price' => 29.99,
                'starts_at' => $today->toDateString(),
                'ends_at' => $today->copy()->addMonthNoOverflow()->subDay()->toDateString(),
                'status' => 'active',
                'last_payment_method' => null,
                'grace_days' => 3,
            ]
        );

        User::firstOrCreate(
            ['email' => 'superadmin@gymsaas.test'],
            [
                'name' => 'SuperAdmin',
                'password' => 'password',
                'gym_id' => null,
                'role' => User::ROLE_SUPERADMIN,
            ]
        );
    }
}
