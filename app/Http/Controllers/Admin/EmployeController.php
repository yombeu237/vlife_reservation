<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeController extends Controller
{
    public function index(): View
    {
        $employes = User::where('role', 'employe')->orderBy('name')->paginate(15);

        return view('admin.employes.index', compact('employes'));
    }

    public function create(): View
    {
        return view('admin.employes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        $user->role = 'employe';
        $user->save();

        return redirect()
            ->route('admin.employes.index')
            ->with('status', "Employé « {$user->name} » créé avec succès.");
    }

    public function destroy(User $employe): RedirectResponse
    {
        if ($employe->role !== 'employe') {
            abort(403, 'Seuls les comptes employé peuvent être supprimés par cette action.');
        }

        if ($employe->id === auth()->id()) {
            abort(403, 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $nom = $employe->name;
        $employe->delete();

        return redirect()
            ->route('admin.employes.index')
            ->with('status', "Employé « {$nom} » supprimé.");
    }
}
