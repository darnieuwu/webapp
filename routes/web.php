<?php

use App\Http\Controllers\SenaraiController;
use App\Http\Controllers\SenaraiTemplateController;
use App\Http\Controllers\AduanController;
use App\Http\Controllers\CawanganController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\ModelanController;
use App\Http\Controllers\PeralatanController;
use App\Http\Controllers\PenyelesaianController;
use App\Http\Controllers\PpkController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('senarai', SenaraiController::class);

// Route untuk Import, Export dan Cetak
Route::post('senarai-import', [SenaraiController::class, 'import'])->name('senarai.import');
Route::get('senarai-export', [SenaraiController::class, 'export'])->name('senarai.export');
Route::get('senarai-print', [SenaraiController::class, 'print'])->name('senarai.print');
Route::get('senarai-print-html', [SenaraiController::class, 'printHtml'])->name('senarai.print.html');
Route::get('senarai-template', [SenaraiTemplateController::class, 'download'])->name('senarai.template.download');