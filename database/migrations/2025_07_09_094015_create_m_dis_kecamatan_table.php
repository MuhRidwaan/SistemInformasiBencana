<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_dis_kecamatan', function (Blueprint $table) {
            $table->bigIncrements('kecamatan_id'); // Primary Key
            $table->bigInteger('kota_id')->unsigned(); // Foreign Key ke Kota
            $table->string('kode_wilayah')->unique(); // Kode Wilayah (harus unik)
            $table->string('nama_kecamatan'); // Nama Kecamatan

            // Kolom audit
            $table->bigInteger('create_who')->unsigned();
            $table->dateTime('create_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('change_who')->unsigned()->nullable();
            $table->dateTime('change_date')->nullable();

            // Foreign Keys
            $table->foreign('kota_id')->references('kota_id')->on('m_dis_kota')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('create_who')->references('user_id')->on('m_users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('change_who')->references('user_id')->on('m_users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_dis_kecamatan');
    }
};
