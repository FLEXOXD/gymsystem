<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('support_contact_label', 120)->nullable()->after('profile_photo_path');
            $table->string('support_contact_email', 120)->nullable()->after('support_contact_label');
            $table->string('support_contact_phone', 30)->nullable()->after('support_contact_email');
            $table->string('support_contact_whatsapp', 30)->nullable()->after('support_contact_phone');
            $table->string('support_contact_link', 255)->nullable()->after('support_contact_whatsapp');
            $table->string('support_contact_message', 500)->nullable()->after('support_contact_link');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'support_contact_label',
                'support_contact_email',
                'support_contact_phone',
                'support_contact_whatsapp',
                'support_contact_link',
                'support_contact_message',
            ]);
        });
    }
};
