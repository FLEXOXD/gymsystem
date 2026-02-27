<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'legal_accepted_at')) {
                $table->timestamp('legal_accepted_at')->nullable()->after('last_login_at');
            }
            if (! Schema::hasColumn('users', 'legal_accepted_version')) {
                $table->string('legal_accepted_version', 30)->nullable()->after('legal_accepted_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'legal_accepted_version')) {
                $table->dropColumn('legal_accepted_version');
            }
            if (Schema::hasColumn('users', 'legal_accepted_at')) {
                $table->dropColumn('legal_accepted_at');
            }
        });
    }
};

