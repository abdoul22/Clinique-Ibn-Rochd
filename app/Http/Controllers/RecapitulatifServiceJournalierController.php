<?php

namespace App\Http\Controllers;

use App\Models\RecapitulatifServiceJournalier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\Caisse;

class RecapitulatifServiceJournalierController extends Controller
{
    public function index(Request $request)
    {
        $recaps = Caisse::with('service')
            ->select([
                'service_id',
                DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00")) as jour'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as nombre'),
            ])
            ->groupBy('service_id', 'jour')
            ->orderBy('jour', 'desc')
            ->orderBy('service_id')
            ->paginate(15);

        return view('recap-services.index', compact('recaps'));
    }

    public function show($id)
    {
        $recap = RecapitulatifServiceJournalier::with('service')->findOrFail($id);
        return view('recap-services.show', compact('recap'));
    }

    public function print()
    {
        $recaps = RecapitulatifServiceJournalier::with('service')->orderBy('date', 'desc')->get();
        return view('recap-services.print', compact('recaps'));
    }

    public function exportPdf()
    {
        $recaps = RecapitulatifServiceJournalier::with('service')->orderBy('date', 'desc')->get();
        $pdf = Pdf::loadView('recap-services.export_pdf', compact('recaps'));
        return $pdf->download('recap-services.pdf');
    }
}
