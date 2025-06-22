<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class SuperAdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role_id', 2)->get(); // 2 = admin
        return view('superadmin.admins.index', compact('admins'));
    }

    public function approve($id)
    {
        $admin = User::findOrFail($id);
        $admin->is_approved = true;
        $admin->save();

        return redirect()->back()->with('success', 'Administrateur approuvé avec succès.');
    }

    public function reject($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->back()->with('success', 'Administrateur rejeté et supprimé.');
    }
}
