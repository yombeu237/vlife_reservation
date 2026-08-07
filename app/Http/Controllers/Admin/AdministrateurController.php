<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdministrateurController extends Controller
{
    public function create(): View
    {
        return view('admin.administrateurs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'admin_key'     => ['required', 'string'],
        ]);

        $expected = (string) config('app.admin_creation_key');

        if ($expected === '' || ! hash_equals($expected, (string) $data['admin_key'])) {
            throw ValidationException::withMessages([
                'admin_key' => 'Clé administrateur invalide.',
            ]);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        $user->role = 'administrateur';
        $user->save();

        return redirect()
            ->route('admin.employes.index')
            ->with('status', "Administrateur « {$user->name} » créé avec succès.");
    }
}
