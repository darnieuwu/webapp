<?php

namespace App\Http\Controllers;

use App\Models\Senarai;
use App\Models\Ppk;
use App\Models\Cawangan;
use App\Models\Peralatan;
use App\Models\Modelan;
use App\Models\Vendor;
use App\Models\Status;
use App\Models\Aduan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SenaraiExport;
use App\Imports\SenaraiImport;
use PDF;

class SenaraiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Senarai::query()
            ->with(['ppk', 'cawangan', 'peralatan', 'modelan', 'vendor', 'status']);
        
        // Carian umum
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('no_siri', 'like', "%{$searchTerm}%")
                  ->orWhere('aduan', 'like', "%{$searchTerm}%")
                  ->orWhere('catatan', 'like', "%{$searchTerm}%")
                  ->orWhereHas('ppk', function($q) use ($searchTerm) {
                      $q->where('nama', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('cawangan', function($q) use ($searchTerm) {
                      $q->where('nama', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        $senarais = $query->latest('tarikh_aduan')->get();

        return view('senarai.index', compact('senarais'));
    }
    // Fungsi untuk Import
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // max 10MB
        ]);

        try {
            Excel::import(new SenaraiImport, $request->file('file'));
            return redirect()->route('senarai.index')->with('success', 'Data berjaya diimport.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ralat: ' . $e->getMessage());
        }
    }

    // Fungsi untuk Export
    public function export(Request $request)
    {
        $format = $request->format ?? 'xlsx';

        $query = Senarai::query()
            ->with(['ppk', 'cawangan', 'peralatan', 'modelan', 'vendor', 'status']);

        // Salin filter dari fungsi index
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('no_siri', 'like', "%{$searchTerm}%")
                    ->orWhere('aduan', 'like', "%{$searchTerm}%")
                    ->orWhere('catatan', 'like', "%{$searchTerm}%")
                    ->orWhereHas('modelan', function($q) use ($searchTerm) {
                        $q->where('nama', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('ppk', function($q) use ($searchTerm) {
                        $q->where('nama', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('cawangan', function($q) use ($searchTerm) {
                        $q->where('nama', 'like', "%{$searchTerm}%");
                    });
            });
        }
    
        $filename = 'senarai_aduan_' . now()->format('Ymd_His') . '.' . $format;

        return Excel::download(new SenaraiExport($query), $filename);
    }

    // Fungsi untuk Cetak PDF
    public function print(Request $request)
    {
        $query = Senarai::query()
            ->with(['ppk', 'cawangan', 'peralatan', 'modelan', 'vendor', 'status']);

        // Salin filter dari fungsi index
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('no_siri', 'like', "%{$searchTerm}%")
                    ->orWhere('aduan', 'like', "%{$searchTerm}%")
                    ->orWhere('catatan', 'like', "%{$searchTerm}%")
                    ->orWhereHas('modelan', function($q) use ($searchTerm) {
                        $q->where('nama', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('ppk', function($q) use ($searchTerm) {
                        $q->where('nama', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('cawangan', function($q) use ($searchTerm) {
                        $q->where('nama', 'like', "%{$searchTerm}%");
                    });
            });
        }
    
        $senarais = $query->latest('tarikh_aduan')->get();

        $pdf = PDF::loadView('senarai.print', compact('senarais'));

        return $pdf->stream('senarai_aduan_' . now()->format('Ymd_His') . '.pdf');
    }
    
    // Fungsi untuk Cetak HTML
    public function printHtml(Request $request)
    {
        $query = Senarai::query()
            ->with(['ppk', 'cawangan', 'peralatan', 'modelan', 'vendor', 'status']);

        // Salin filter dari fungsi index
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('no_siri', 'like', "%{$searchTerm}%")
                    ->orWhere('aduan', 'like', "%{$searchTerm}%")
                    ->orWhere('catatan', 'like', "%{$searchTerm}%")
                    ->orWhereHas('ppk', function($q) use ($searchTerm) {
                        $q->where('nama', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('cawangan', function($q) use ($searchTerm) {
                        $q->where('nama', 'like', "%{$searchTerm}%");
                    });
            });
        }
    
        $senarais = $query->latest('tarikh_aduan')->get();

        return view('senarai.print_html', compact('senarais'));
    }
}
