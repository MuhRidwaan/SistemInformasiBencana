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
        Schema::create('m_bencana', function (Blueprint $table) {
            $table->bigIncrements('bencana_id'); // Primary Key
            $table->bigInteger('jenis_bencana_id')->unsigned(); // Foreign Key ke Jenis Bencana
            $table->string('nama_bencana');
            $table->text('kronologis');
            $table->text('deskripsi');
            $table->dateTime('tanggal_kejadian');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->bigInteger('provinsi_id')->unsigned();
            $table->bigInteger('kota_id')->unsigned();
            $table->bigInteger('kecamatan_id')->unsigned();
            $table->bigInteger('kelurahan_id')->unsigned();

            // Kolom audit
            $table->bigInteger('create_who')->unsigned();
            $table->dateTime('create_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('change_who')->unsigned()->nullable();
            $table->dateTime('change_date')->nullable();

            // Foreign Keys
            $table->foreign('jenis_bencana_id')->references('jenis_bencana_id')->on('m_jenis_bencana')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('provinsi_id')->references('provinsi_id')->on('m_dis_provinsi')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('kota_id')->references('kota_id')->on('m_dis_kota')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('kecamatan_id')->references('kecamatan_id')->on('m_dis_kecamatan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('kelurahan_id')->references('kelurahan_id')->on('m_dis_kelurahan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('create_who')->references('user_id')->on('m_users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('change_who')->references('user_id')->on('m_users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_bencana');
    }
};
