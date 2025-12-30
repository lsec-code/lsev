<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('fa-trophy'); // FontAwesome icon class
            $table->string('color')->default('yellow'); // Badge color
            $table->enum('requirement_type', ['videos', 'views', 'followers', 'earnings'])->default('videos');
            $table->integer('requirement_value')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
