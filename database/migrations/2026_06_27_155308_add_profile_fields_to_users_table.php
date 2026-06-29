<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->nullable()->after('name');
            $table->string('nickname')->nullable()->after('surname');
            $table->string('username')->nullable()->unique()->after('nickname');
            $table->string('phone', 20)->nullable()->after('email_verified_at');
            $table->string('avatar_url')->nullable()->after('phone');
            $table->string('locale', 10)->nullable()->after('avatar_url');
            $table->string('timezone', 64)->nullable()->after('locale');
            $table->string('status', 20)->nullable()->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'surname',
                'nickname',
                'username',
                'phone',
                'avatar_url',
                'locale',
                'timezone',
                'status',
            ]);
        });
    }
};
