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
        Schema::create('m_permissions', function (Blueprint $table) {
            $table->bigIncrements('permission_id'); // Primary key
            $table->string('name')->unique(); // Nama izin (e.g., 'view-users', 'create-roles')
            $table->string('description')->nullable(); // Deskripsi izin
            $table->timestamps(); // create_at dan updated_at (opsional, bisa disesuaikan dengan create_date/change_date jika mau)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_permissions');
    }
};
