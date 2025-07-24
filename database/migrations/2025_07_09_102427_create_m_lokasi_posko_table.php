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
        Schema::create('m_lokasi_posko', function (Blueprint $table) {
            $table->bigIncrements('posko_id'); // Primary Key
            $table->bigInteger('bencana_id')->unsigned(); // Foreign Key ke Bencana
            $table->string('nama_posko');
            $table->text('alamat_posko');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->bigInteger('kapasitas')->nullable(); // Kapasitas posko
            $table->string('kontak_person')->nullable(); // Kontak Person

            // Kolom audit
            $table->bigInteger('create_who')->unsigned();
            $table->dateTime('create_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('change_who')->unsigned()->nullable();
            $table->dateTime('change_date')->nullable();

            // Foreign Keys
            $table->foreign('bencana_id')->references('bencana_id')->on('m_bencana')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('create_who')->references('user_id')->on('m_users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('change_who')->references('user_id')->on('m_users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_lokasi_posko');
    }
};
