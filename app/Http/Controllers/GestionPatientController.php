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
        $query = GestionPatient::query();

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

        $patients = $query->orderBy('created_at', 'desc')->paginate(6);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $patient = new GestionPatient();
        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->gender = $request->gender;
        $patient->date_of_birth = $request->date_of_birth;
        $patient->phone = $request->phone;

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
        $patient = GestionPatient::find($id);
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, GestionPatient $patient)
    {
        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->gender = $request->gender;
        $patient->date_of_birth = $request->date_of_birth;
        $patient->phone = $request->phone;
        $patient->address = $request->address;

        if ($patient->save()) {
            $role = Auth::user()->role->name;

            if ($role === 'superadmin') {
                return redirect()->route('superadmin.patients.index')->with('success', 'Patient mis à jour !');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.patients.index')->with('success', 'Patient mis à jour !');
            }

            return redirect()->route('patients.index')->with('success', 'Patient mis à jour !');
        } else {
            return back()->with('error', 'Erreur lors de la mise à jour');
        }
    }


    public function destroy(GestionPatient $patient,$id)
    {
        $patient = GestionPatient::find($id);
        if ($patient->delete()) {
            $role = Auth::user()->role->name;

            if ($role === 'superadmin') {
                return redirect()->route('superadmin.patients.index')->with('success', 'Patient supprimé !');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.patients.index')->with('success', 'Patient supprimé !');
            }

            return redirect()->route('patients.index')->with('success', 'Patient supprimé !');
        } else {
            return back()->with('error', 'Erreur lors de la suppression');
        }
    }

}
