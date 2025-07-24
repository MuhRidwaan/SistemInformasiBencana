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
        Schema::table('m_permissions', function (Blueprint $table) {
            // Tambahkan kolom module_id
            $table->bigInteger('module_id')->unsigned()->nullable()->after('description');

            // Tambahkan foreign key constraint
            $table->foreign('module_id')
                  ->references('module_id')
                  ->on('m_permission_modules')
                  ->onUpdate('cascade') // Jika nama modul berubah, update di sini
                  ->onDelete('set null'); // Jika modul dihapus, set permission.module_id menjadi null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_permissions', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['module_id']);
            // Kemudian hapus kolom
            $table->dropColumn('module_id');
        });
    }
};
