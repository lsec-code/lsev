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
            $table->string('payment_method')->nullable()->after('balance');
            $table->string('payment_number')->nullable()->after('payment_method');
            $table->string('payment_name')->nullable()->after('payment_number');
            $table->boolean('allow_download')->default(false)->after('payment_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_number', 'payment_name', 'allow_download']);
        });
    }
};
