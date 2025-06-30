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
        Schema::create('settings', function (Blueprint $table) {
            $table->id('id_setting');
            $table->string('value', 255)->nullable();
            $table->string('key', 100)->unique();
            $table->string('type', 50)->default('text'); // text, number, boolean, etc.
            $table->string('description', 255)->nullable(); // Optional description for the setting
            $table->boolean('is_active')->default(true); // Indicates if the setting is active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
