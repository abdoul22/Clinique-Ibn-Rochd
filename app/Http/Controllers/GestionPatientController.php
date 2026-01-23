<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GestionPatient;
use Illuminate\Support\Facades\Auth;

class GestionPatientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $phoneFilter = $request->input('phone_filter');
        $genderFilter = $request->input('gender_filter');
        $nameFilter = $request->input('name_filter');
        $ageFilter = $request->input('age_filter');
        $period = $request->input('period');

        $query = GestionPatient::query();

        // Filtre de recherche générale
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('date_of_birth', 'like', "%{$search}%")
                    ->orWhere('gender', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('insurance_number', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('medical_history', 'like', "%{$search}%")
                    ->orWhere('allergies', 'like', "%{$search}%")
                    ->orWhere('current_medication', 'like', "%{$search}%")
                    ->orWhere('emergency_contact_name', 'like', "%{$search}%")
                    ->orWhere('emergency_contact_phone', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        // Filtre par numéro de téléphone
        if ($phoneFilter) {
            $query->where('phone', 'like', "%{$phoneFilter}%");
        }

        // Filtre par sexe
        if ($genderFilter) {
            $query->where('gender', $genderFilter);
        }

        // Filtre par nom/prénom
        if ($nameFilter) {
            $query->where(function ($q) use ($nameFilter) {
                $q->where('first_name', 'like', "%{$nameFilter}%")
                    ->orWhere('last_name', 'like', "%{$nameFilter}%");
            });
        }

        // Filtre par âge (intervalles)
        if ($ageFilter) {
            if ($ageFilter === '100+') {
                $query->where('age', '>', 100);
            } else {
                $ages = explode('-', $ageFilter);
                if (count($ages) === 2) {
                    $minAge = (int)$ages[0];
                    $maxAge = (int)$ages[1];
                    $query->whereBetween('age', [$minAge, $maxAge]);
                }
            }
        }

        // Filtrage par période d'inscription sur created_at
        if ($period) {
            $now = \Carbon\Carbon::now();
            
            if ($period === 'day') {
                // Aujourd'hui
                $query->whereDate('created_at', $now->toDateString());
            } elseif ($period === 'week') {
                // Cette semaine (du lundi au dimanche)
                $startOfWeek = $now->copy()->startOfWeek();
                $endOfWeek = $now->copy()->endOfWeek();
                $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            } elseif ($period === 'month') {
                // Ce mois
                $query->whereYear('created_at', $now->year)
                      ->whereMonth('created_at', $now->month);
            } elseif ($period === 'year') {
                // Cette année
                $query->whereYear('created_at', $now->year);
            }
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(6);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        // Validation pour éviter les doublons
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Homme,Femme',
            'age' => 'required|integer|min:0|max:150',
            'phone' => 'required|string|max:20|unique:gestion_patients,phone',
        ], [
            'phone.unique' => 'Le numéro de téléphone existe déjà.',
        ]);

        // Vérifier si le patient existe déjà (nom + prénom + téléphone)
        $existingPatient = GestionPatient::where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->where('phone', $request->phone)
            ->first();

        if ($existingPatient) {
            return back()->withErrors(['phone' => 'Un patient avec ces informations existe déjà.'])->withInput();
        }

        $patient = new GestionPatient();
        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->gender = $request->gender;
        $patient->age = $request->age;
        $patient->phone = $request->phone;
        $patient->address = $request->address ?? '';

        if ($patient->save()) {
            // Rediriger dynamiquement selon le rôle
            $role = Auth::user()->role->name;

            if ($role === 'superadmin') {
                return redirect()->route('superadmin.patients.index')->with('success', 'Patient ajouté !');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.patients.index')->with('success', 'Patient ajouté !');
            }

            return redirect()->route('patients.index')->with('success', 'Patient ajouté !');
        } else {
            return back()->with('error', 'Erreur lors de la sauvegarde');
        }
    }

    public function show(GestionPatient $patient, $id)
    {
        $patient = GestionPatient::find($id);
        return view('patients.show', compact('patient'));
    }

    public function edit($id)
    {
        $patient = GestionPatient::findOrFail($id);
        $page = request('page', 1); // Récupérer le paramètre page
        return view('patients.edit', compact('patient', 'page'));
    }

    public function update(Request $request, $id)
    {
        $patient = GestionPatient::findOrFail($id);
        
        // Validation pour éviter les doublons - exclure l'ID actuel
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Homme,Femme',
            'age' => 'required|integer|min:0|max:150',
            'phone' => 'required|string|max:20|unique:gestion_patients,phone,' . $id,
            'address' => 'nullable|string|max:255',
        ], [
            'phone.unique' => 'Le numéro de téléphone existe déjà.',
        ]);

        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->gender = $request->gender;
        $patient->age = $request->age;
        $patient->phone = $request->phone;
        $patient->address = $request->address ?? null;

        if ($patient->save()) {
            // Conserver le paramètre de pagination
            $page = $request->input('return_page', 1);
            $role = Auth::user()->role->name;

            if ($role === 'superadmin') {
                return redirect()->route('superadmin.patients.index', ['page' => $page])->with('success', 'Patient mis à jour !');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.patients.index', ['page' => $page])->with('success', 'Patient mis à jour !');
            } elseif ($role === 'medecin') {
                return redirect()->route('medecin.patients.show', $patient->id)->with('success', 'Patient mis à jour !');
            }

            return redirect()->route('patients.index', ['page' => $page])->with('success', 'Patient mis à jour !');
        } else {
            return back()->with('error', 'Erreur lors de la mise à jour');
        }
    }


    public function destroy(GestionPatient $patient, $id)
    {
        $patient = GestionPatient::find($id);
        if ($patient->delete()) {
            $role = Auth::user()->role->name;

            if ($role === 'superadmin') {
                return redirect()->route('superadmin.patients.index')->with('success', 'Patient supprimé !');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.patients.index')->with('success', 'Patient supprimé !');
            } elseif ($role === 'medecin') {
                return redirect()->route('medecin.patients.index')->with('success', 'Patient supprimé !');
            }

            return redirect()->route('patients.index')->with('success', 'Patient supprimé !');
        } else {
            return back()->with('error', 'Erreur lors de la suppression');
        }
    }
}
