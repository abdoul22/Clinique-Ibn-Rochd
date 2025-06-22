<?php

namespace App\Http\Controllers;

use App\Models\RecapitulatifServiceJournalier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RecapitulatifServiceJournalierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = RecapitulatifServiceJournalier::with('service');

        if ($search) {
            $query->whereHas('service', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%");
            });
        }

        $recaps = $query->orderBy('date', 'desc')->paginate(10);
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
