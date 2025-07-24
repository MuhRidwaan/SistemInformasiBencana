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
        Schema::table('m_users', function (Blueprint $table) {
            // Tambahkan kolom role_id
            $table->bigInteger('role_id')->unsigned()->nullable()->after('kontak');

            // Tambahkan foreign key constraint
            $table->foreign('role_id')
                  ->references('role_id')
                  ->on('m_roles')
                  ->onUpdate('restrict') // Atau 'cascade' jika ingin role terhapus otomatis saat user terhapus
                  ->onDelete('restrict'); // Atau 'set null' jika ingin role_id jadi null saat role terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_users', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['role_id']);
            // Kemudian hapus kolom
            $table->dropColumn('role_id');
        });
    }
};
