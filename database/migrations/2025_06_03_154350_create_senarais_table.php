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
        Schema::create('senarais', function (Blueprint $table) {
            $table->id();
            $table->date('tarikh_aduan');
            $table->foreignId('ppk_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ppk_nama_snapshot')->nullable();
            $table->foreignId('cawangan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('cawangan_nama_snapshot')->nullable();
            $table->foreignId('peralatan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('peralatan_nama_snapshot')->nullable();
            $table->text('aduan');
            $table->string('no_siri')->nullable();
            $table->foreignId('modelan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('modelan_nama_snapshot')->nullable();
            $table->text('penyelesaian')->nullable();
            $table->date('tarikh_hantar_baikpulih')->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $table->string('vendor_nama_snapshot')->nullable();
            $table->date('tarikh_selesai_baikpulih')->nullable();
            $table->date('tarikh_hantar_cawangan')->nullable();
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->decimal('kos', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add indexes for snapshot columns for better performance
            $table->index('cawangan_nama_snapshot');
            $table->index('peralatan_nama_snapshot');
            $table->index('modelan_nama_snapshot');
            $table->index('vendor_nama_snapshot');
            $table->index('ppk_nama_snapshot');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senarais');
    }
};
