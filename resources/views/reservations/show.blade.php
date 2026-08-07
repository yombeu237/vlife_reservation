<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#800020] leading-tight">Réservation #{{ $reservation->id }}</h2>
            <a href="{{ route('reservations.index') }}" class="inline-flex items-center gap-1 text-sm text-[#4D4D4D] hover:text-[#800020]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded-md bg-[#800020]/10 border border-[#800020]/20 text-[#800020] px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('document'))
                <div class="rounded-md bg-[#800020] text-white border border-[#5C0018] px-4 py-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                        <div class="text-sm font-medium">{{ $errors->first('document') }}</div>
                    </div>
                </div>
            @endif

            @php
                $statutLabels = ['liste_attente' => "Liste d'attente", 'deja_paye' => 'Déjà payé', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'annule' => 'Annulé'];
                $statutColors = ['liste_attente' => 'bg-[#F5F5F5] text-[#4D4D4D]', 'deja_paye' => 'bg-[#800020]/10 text-[#800020]', 'en_cours' => 'bg-[#800020] text-white', 'termine' => 'bg-[#5C0018] text-white', 'annule' => 'bg-white text-[#4D4D4D] line-through border border-[#E0E0E0]'];
            @endphp

            <div class="bg-white shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="text-xs text-[#4D4D4D] uppercase tracking-wider">Statut actuel</div>
                        <div class="mt-1">
                            <span class="inline-flex items-center rounded-md px-3 py-1 text-sm font-medium {{ $statutColors[$reservation->statut] }}">
                                {{ $statutLabels[$reservation->statut] }}
                            </span>
                            @if($reservation->modifie_manuellement)
                                <span class="ml-2 text-xs text-[#4D4D4D]/70 italic">(modifié manuellement)</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right text-sm text-[#4D4D4D]">
                        <div>Créée le {{ $reservation->created_at?->format('d/m/Y à H:i') }}</div>
                        <div>par {{ $reservation->utilisateur->name }}</div>
                    </div>
                </div>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-[#E0E0E0]">
                    <div>
                        <dt class="text-xs text-[#4D4D4D] uppercase">Client</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reservation->client->nom }}</dd>
                        <dd class="text-xs text-[#4D4D4D]">{{ $reservation->client->telephone ?? 'Pas de téléphone' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#4D4D4D] uppercase">Compartiment / Option</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reservation->option->libelle }}</dd>
                        <dd class="text-xs text-[#4D4D4D]">{{ $reservation->option->compartiment->nom }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#4D4D4D] uppercase">Date</dt>
                        <dd class="text-sm text-gray-900">{{ $reservation->date_reservation?->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#4D4D4D] uppercase">Créneau</dt>
                        <dd class="text-sm text-gray-900">
                            @if($reservation->type_creneau === 'plage_horaire')
                                {{ substr($reservation->heure_debut, 0, 5) }} → {{ substr($reservation->heure_fin, 0, 5) }}
                            @else
                                Journée entière
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#4D4D4D] uppercase">Nombre de personnes</dt>
                        <dd class="text-sm text-gray-900">{{ $reservation->nombre_personnes }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#4D4D4D] uppercase">Montant</dt>
                        <dd class="text-lg font-bold text-[#800020]">{{ number_format($reservation->montant, 0, ',', ' ') }} FCFA</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-[#800020] mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Document justificatif
                </h3>

                @if ($reservation->chemin_document_justificatif)
                    <div class="flex items-center justify-between rounded-md bg-[#F5F5F5] border border-[#E0E0E0] px-4 py-3 mb-4">
                        <div class="text-sm">
                            <div class="text-[#4D4D4D]">Document téléversé</div>
                            @if($reservation->date_validation_preuve)
                                <div class="text-xs text-[#800020] mt-1">Validé le {{ $reservation->date_validation_preuve->format('d/m/Y à H:i') }}</div>
                            @else
                                <div class="text-xs text-[#4D4D4D]/70 mt-1 italic">En attente de validation</div>
                            @endif
                        </div>
                        <a href="{{ route('reservations.telecharger-document', $reservation) }}" class="inline-flex items-center gap-1 text-sm text-[#800020] hover:text-[#5C0018]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            Télécharger
                        </a>
                    </div>

                    @if (! $reservation->date_validation_preuve && $reservation->statut !== 'annule')
                        <form method="POST" action="{{ route('reservations.valider-document', $reservation) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-[#800020] text-white px-4 py-3 text-sm font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#5C0018] hover:shadow-md active:scale-[0.98]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                Valider le document (bascule en « Déjà payé » si capacité OK)
                            </button>
                        </form>
                    @endif
                @endif

                @if ($reservation->statut !== 'annule')
                    <form method="POST" action="{{ route('reservations.upload-document', $reservation) }}" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <label class="block text-sm font-medium text-[#4D4D4D] mb-1">
                            {{ $reservation->chemin_document_justificatif ? 'Remplacer le document' : 'Téléverser le document' }}
                        </label>
                        <div class="flex gap-2">
                            <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="flex-1 text-sm text-[#4D4D4D] file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-[#800020] file:text-white file:font-medium file:cursor-pointer hover:file:bg-[#5C0018] file:transition-colors" />
                            <button type="submit" class="rounded-md bg-[#800020] text-white px-4 py-2 text-sm font-semibold hover:bg-[#5C0018] transition-colors">
                                Envoyer
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-[#4D4D4D]">PDF, JPG, PNG — max 5 Mo</p>
                        @error('document') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                    </form>
                @endif
            </div>

            <div class="bg-white shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-[#800020] mb-1">Modifier le statut manuellement</h3>
                    @php
                        $documentValide     = ! is_null($reservation->date_validation_preuve);
                        $statutsModifiables = $documentValide
                            ? collect($statutLabels)->except('deja_paye')
                            : collect($statutLabels)->only(['liste_attente', 'annule']);
                    @endphp
                    <p class="text-xs text-[#4D4D4D] mb-3">
                        @if($documentValide)
                            Le document a été validé — les statuts « En cours » et « Terminé » sont désormais accessibles (utile si le client termine plus tôt). « Déjà payé » reste automatique (via validation du document).
                        @else
                            Tant que le document justificatif n'a pas été validé, seuls « Liste d'attente » et « Annulé » sont disponibles. Les autres statuts se géreront automatiquement après validation.
                        @endif
                    </p>
                    <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="flex gap-2 items-end">
                        @csrf
                        @method('PUT')
                        <div class="flex-1">
                            <select name="statut" class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020]">
                                @foreach($statutsModifiables as $val => $lib)
                                    <option value="{{ $val }}" @selected($reservation->statut === $val)>{{ $lib }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="rounded-md bg-[#4D4D4D] text-white px-4 py-2 text-sm hover:bg-[#800020] transition-colors">Appliquer</button>
                    </form>
                    @error('statut') <p class="mt-2 text-xs text-[#800020] font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-6 border-t border-[#E0E0E0]">
                    <h3 class="text-lg font-semibold text-[#800020] mb-3">Modifier le montant</h3>
                    <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="flex gap-2 items-end">
                        @csrf
                        @method('PUT')
                        <div class="flex-1">
                            <label for="montant_edit" class="block text-xs text-[#4D4D4D] mb-1">Montant (FCFA)</label>
                            <input id="montant_edit" type="number" name="montant" min="0" value="{{ $reservation->montant }}" required
                                   class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020]" />
                        </div>
                        <button type="submit" class="rounded-md bg-[#4D4D4D] text-white px-4 py-2 text-sm hover:bg-[#800020] transition-colors">Mettre à jour</button>
                    </form>
                </div>

                @if(auth()->user()->isAdministrateur() && $reservation->statut !== 'annule')
                    <form method="POST" action="{{ route('reservations.destroy', $reservation) }}" class="mt-4 pt-4 border-t border-[#E0E0E0]" onsubmit="return confirm('Confirmer l\'annulation de cette réservation ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 text-sm text-[#800020] hover:text-[#5C0018] border border-[#800020]/30 rounded-md px-3 py-2 hover:bg-[#800020]/5 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                            Annuler la réservation (admin uniquement)
                        </button>
                    </form>
                @endif

                @if($reservation->statut === 'liste_attente' && $reservation->client->telephone)
                    <div class="mt-4 pt-4 border-t border-[#E0E0E0]">
                        <p class="text-xs text-[#4D4D4D] mb-2">Contact liste d'attente :</p>
                        <a href="tel:{{ $reservation->client->telephone }}" class="inline-flex items-center gap-2 text-sm text-[#800020] hover:text-[#5C0018]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $reservation->client->telephone }}
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
