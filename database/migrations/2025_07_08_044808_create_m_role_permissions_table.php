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
        Schema::create('m_role_permissions', function (Blueprint $table) {
            $table->bigInteger('role_id')->unsigned();
            $table->bigInteger('permission_id')->unsigned();

            // Foreign keys
            $table->foreign('role_id')->references('role_id')->on('m_roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('permission_id')->on('m_permissions')->onDelete('cascade');

            // Primary key gabungan untuk mencegah duplikasi
            $table->primary(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_role_permissions');
    }
};
