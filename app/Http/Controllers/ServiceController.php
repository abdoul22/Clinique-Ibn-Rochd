<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Service::query();

        if ($search) {
            $query->where('nom', 'like', "%{$search}%")
                ->orWhere('observation', 'like', "%{$search}%");
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('services.index', ['services' => $services]);
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:255']);

        Service::create($request->only(['nom', 'observation']));
        return redirect('services')->with('success', 'Service ajouté avec succès.');
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return view('services.show', ['service' => $service]);
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('services.edit', ['service' => $service]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nom' => 'required|string|max:255']);
        $service = Service::findOrFail($id);

        $service->update($request->only(['nom', 'observation']));
        return redirect('services')->with('success', 'Service mis à jour.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return redirect('services')->with('success', 'Service supprimé.');
    }

    public function exportPdf()
    {
        $services = Service::all();
        $pdf = PDF::loadView('services.export_pdf', compact('services'));
        return $pdf->download('services.pdf');
    }


    public function print()
    {
        $services = Service::all();
        return view('services.print', compact('services'));
    }
}
