<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $clients = Client::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            })
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('clients.index', compact('clients', 'search'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20', 'unique:clients,telephone'],
        ]);

        $client = Client::create($data);

        return redirect()
            ->route('clients.index')
            ->with('status', "Client « {$client->nom} » enregistré.");
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20', 'unique:clients,telephone,' . $client->id],
        ]);

        $client->update($data);

        return redirect()
            ->route('clients.index')
            ->with('status', "Client « {$client->nom} » mis à jour.");
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->reservations()->exists()) {
            return redirect()
                ->route('clients.index')
                ->with('status', "Impossible de supprimer « {$client->nom} » : réservations liées existantes.");
        }

        $nom = $client->nom;
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('status', "Client « {$nom} » supprimé.");
    }
}
