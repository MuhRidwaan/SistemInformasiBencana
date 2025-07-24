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
        Schema::create('m_permission_modules', function (Blueprint $table) {
            $table->bigIncrements('module_id'); // Primary key
            $table->string('name')->unique(); // Nama modul (e.g., 'Manajemen Pengguna')
            $table->string('description')->nullable(); // Deskripsi modul
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_permission_modules');
    }
};
