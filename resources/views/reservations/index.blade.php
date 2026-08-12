<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#800020] leading-tight">Réservations</h2>
            <a href="{{ route('reservations.create') }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#800020] text-white px-4 py-2 text-sm font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#5C0018] hover:shadow-md hover:scale-[1.02] active:scale-[0.98]">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Nouvelle réservation
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 rounded-md bg-[#800020]/10 border border-[#800020]/20 text-[#800020] px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('reservations.index') }}" class="mb-4 flex flex-wrap gap-2 items-end">
                <div>
                    <label class="block text-xs text-[#4D4D4D] mb-1">Compartiment</label>
                    <select name="compartiment_id" class="rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020] text-sm">
                        <option value="">Tous</option>
                        @foreach($compartiments as $c)
                            <option value="{{ $c->id }}" @selected($compartimentSelected == $c->id)>{{ $c->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-[#4D4D4D] mb-1">Statut</label>
                    <select name="statut" class="rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020] text-sm">
                        <option value="">Tous</option>
                        @foreach(['liste_attente' => "Liste d'attente", 'deja_paye' => 'Déjà payé', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'annule' => 'Annulé'] as $val => $lib)
                            <option value="{{ $val }}" @selected($statutSelected === $val)>{{ $lib }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-md bg-[#4D4D4D] text-white px-4 py-2 text-sm hover:bg-[#800020] transition-colors duration-200">
                    Filtrer
                </button>
                @if($statutSelected || $compartimentSelected)
                    <a href="{{ route('reservations.index') }}" class="rounded-md bg-white text-[#4D4D4D] px-4 py-2 text-sm border border-[#E0E0E0] hover:bg-[#F5F5F5]">Réinitialiser</a>
                @endif
            </form>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg">
                <table class="min-w-full divide-y divide-[#E0E0E0]">
                    <thead class="bg-[#F5F5F5]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Option</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Pers.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Montant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Traité par</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E0E0E0]">
                        @php
                            $statutLabels = ['liste_attente' => "Liste d'attente", 'deja_paye' => 'Déjà payé', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'annule' => 'Annulé'];
                            $statutColors = ['liste_attente' => 'bg-[#F5F5F5] text-[#4D4D4D]', 'deja_paye' => 'bg-[#800020]/10 text-[#800020]', 'en_cours' => 'bg-[#800020] text-white', 'termine' => 'bg-[#5C0018] text-white', 'annule' => 'bg-white text-[#4D4D4D] line-through'];
                        @endphp
                        @forelse ($reservations as $r)
                            <tr class="hover:bg-[#F5F5F5]/50 transition-colors duration-150">
                                <td class="px-4 py-3 text-sm"><a href="{{ route('reservations.show', $r) }}" class="text-[#800020] hover:underline font-medium">#{{ $r->id }}</a></td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if($r->estMultiJours())
                                        {{ $r->date_reservation?->format('d/m/Y') }} → {{ $r->dateFinEffective()->format('d/m/Y') }}
                                        <div class="text-xs text-[#800020]">{{ $r->nombreJours() }} jours</div>
                                    @else
                                        {{ $r->date_reservation?->format('d/m/Y') }}
                                        @if($r->type_creneau === 'plage_horaire')
                                            <div class="text-xs text-[#4D4D4D]">{{ substr($r->heure_debut, 0, 5) }} → {{ substr($r->heure_fin, 0, 5) }}</div>
                                        @else
                                            <div class="text-xs text-[#4D4D4D] italic">Journée</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $r->client->nom }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $r->option->libelle }}
                                    <div class="text-xs text-[#4D4D4D]">{{ $r->option->compartiment->nom }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $r->nombre_personnes }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ number_format($r->montant, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $tempsRestant = null;
                                        if ($r->statut === 'en_cours') {
                                            if ($r->type_creneau === 'plage_horaire' && $r->heure_fin) {
                                                $fin = \Carbon\Carbon::parse($r->date_reservation->format('Y-m-d') . ' ' . $r->heure_fin);
                                                if ($fin->isFuture()) {
                                                    $diff = now()->diff($fin);
                                                    $tempsRestant = ($diff->h > 0 ? $diff->h . 'h ' : '') . $diff->i . 'min restants';
                                                } else {
                                                    $tempsRestant = 'Créneau terminé (bascule imminente)';
                                                }
                                            } else {
                                                $fin = $r->dateFinEffective()->copy()->endOfDay();
                                                if ($fin->isFuture()) {
                                                    $diff = now()->diff($fin);
                                                    if ($r->estMultiJours()) {
                                                        $tempsRestant = 'Se termine le ' . $fin->format('d/m/Y') . ' à minuit (' . $diff->days . 'j ' . $diff->h . 'h restants)';
                                                    } else {
                                                        $tempsRestant = 'Journée entière — fin à minuit (' . $diff->h . 'h ' . $diff->i . 'min restants)';
                                                    }
                                                } else {
                                                    $tempsRestant = 'Terminé (bascule imminente)';
                                                }
                                            }
                                        }
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium {{ $statutColors[$r->statut] ?? 'bg-gray-100' }}"
                                          @if($tempsRestant) title="{{ $tempsRestant }}" @endif>
                                        {{ $statutLabels[$r->statut] ?? $r->statut }}
                                        @if($tempsRestant)
                                            <svg class="ml-1 w-3 h-3 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        @endif
                                    </span>
                                    @if($r->modifie_manuellement)
                                        <span class="ml-1 text-[10px] text-[#4D4D4D]/60" title="Modifié manuellement">✏</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-[#4D4D4D]">{{ $r->utilisateur->name }}</td>
                                <td class="px-4 py-3 text-right text-sm space-x-1 whitespace-nowrap">
                                    @php
                                        $docValide       = ! is_null($r->date_validation_preuve);
                                        $docPresent      = ! empty($r->chemin_document_justificatif);
                                        $statutsPourCeLigne = $docValide
                                            ? ['liste_attente', 'en_cours', 'termine', 'annule']
                                            : ['liste_attente', 'annule'];

                                        // Bouton contextuel selon l'état de la preuve
                                        if ($r->statut === 'annule') {
                                            $btnLibelle = 'Détail';
                                            $btnClasse  = 'bg-white text-[#4D4D4D] border border-[#E0E0E0] hover:bg-[#F5F5F5]';
                                            $btnIcone   = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
                                        } elseif (! $docPresent) {
                                            $btnLibelle = 'Téléverser preuve';
                                            $btnClasse  = 'bg-[#800020] text-white hover:bg-[#5C0018] shadow-sm';
                                            $btnIcone   = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>';
                                        } elseif (! $docValide) {
                                            $btnLibelle = 'Valider preuve';
                                            $btnClasse  = 'bg-[#800020] text-white hover:bg-[#5C0018] shadow-sm';
                                            $btnIcone   = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>';
                                        } else {
                                            $btnLibelle = 'Détail';
                                            $btnClasse  = 'bg-white text-[#800020] border border-[#800020] hover:bg-[#800020]/5';
                                            $btnIcone   = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
                                        }
                                    @endphp

                                    <a href="{{ route('reservations.show', $r) }}"
                                       class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-xs font-semibold transition-all duration-200 ease-in-out active:scale-[0.98] {{ $btnClasse }}">
                                        {!! $btnIcone !!}
                                        {{ $btnLibelle }}
                                    </a>

                                    <form method="POST" action="{{ route('reservations.update', $r) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="statut" onchange="this.form.submit()"
                                                title="{{ $docValide ? 'Document validé — tous les statuts sauf « Déjà payé » sont ajustables manuellement.' : 'Tant que le document n\'est pas validé, seuls « Liste d\'attente » et « Annulé » sont disponibles.' }}"
                                                class="text-xs rounded border-[#E0E0E0] focus:border-[#800020] focus:ring-[#800020]">
                                            @foreach($statutLabels as $val => $lib)
                                                @if($val === 'deja_paye')
                                                    <option value="{{ $val }}" @selected($r->statut === $val) disabled>{{ $lib }} (via document)</option>
                                                @elseif(in_array($val, $statutsPourCeLigne, true))
                                                    <option value="{{ $val }}" @selected($r->statut === $val)>{{ $lib }}</option>
                                                @else
                                                    <option value="{{ $val }}" @selected($r->statut === $val) disabled>{{ $lib }} (verrouillé)</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </form>

                                    @if(auth()->user()->isAdministrateur() && $r->statut !== 'annule')
                                        <form method="POST" action="{{ route('reservations.destroy', $r) }}" class="inline" onsubmit="return confirm('Annuler cette réservation ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-[#800020] hover:text-[#5C0018] ml-1">Annuler</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-4 py-8 text-center text-sm text-[#4D4D4D]">Aucune réservation.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reservations->hasPages())<div class="mt-4">{{ $reservations->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
