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
            $table->boolean('has_logged_in')->default(false)->after('security_answer');
            $table->timestamp('first_login_at')->nullable()->after('has_logged_in');
            $table->integer('first_login_attempts')->default(0)->after('first_login_at');
            $table->timestamp('first_login_locked_until')->nullable()->after('first_login_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['has_logged_in', 'first_login_at', 'first_login_attempts', 'first_login_locked_until']);
        });
    }
};
