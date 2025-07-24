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
        Schema::create('m_kebutuhan_logistik', function (Blueprint $table) {
            $table->bigIncrements('kebutuhan_id'); // Primary Key
            $table->bigInteger('bencana_id')->unsigned(); // Foreign Key ke Bencana
            $table->string('jenis_kebutuhan');
            $table->bigInteger('jumlah_dibutuhkan');
            $table->string('satuan');
            $table->bigInteger('jumlah_tersedia');
            $table->dateTime('tanggal_update');
            $table->text('deskripsi')->nullable();

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
        Schema::dropIfExists('m_kebutuhan_logistik');
    }
};
