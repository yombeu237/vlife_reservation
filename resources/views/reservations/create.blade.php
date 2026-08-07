<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#800020] leading-tight">Nouvelle réservation</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if($clients->isEmpty())
                <div class="mb-6 rounded-md bg-[#800020]/10 border border-[#800020]/20 text-[#800020] px-4 py-3 text-sm">
                    Aucun client n'est enregistré. <a href="{{ route('clients.create') }}" class="underline font-semibold">Créer un client</a> avant de faire une réservation.
                </div>
            @endif

            <div class="bg-white shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg p-6">
                <form method="POST" action="{{ route('reservations.store') }}" class="space-y-5"
                      x-data="{
                          compartimentId: '{{ old('compartiment_id') }}',
                          optionId: '{{ old('option_id') }}',
                          nbPersonnes: {{ (int) old('nombre_personnes', 1) }},
                          typeCreneau: '{{ old('type_creneau', 'journee') }}',
                          allOptions: {{ Illuminate\Support\Js::from(
                                $compartiments->flatMap(fn($c) => $c->optionsReservation->map(fn($o) => [
                                    'id' => $o->id,
                                    'compartiment_id' => $o->compartiment_id,
                                    'libelle' => $o->libelle,
                                    'tarif' => (int) $o->tarif,
                                    'type_calcul' => $o->type_calcul,
                                    'capacite' => (int) $o->capacite,
                                ]))->values()
                          ) }},
                          get filteredOptions() {
                              return this.allOptions.filter(o => String(o.compartiment_id) === String(this.compartimentId));
                          },
                          get selectedOption() {
                              return this.allOptions.find(o => String(o.id) === String(this.optionId));
                          },
                          get montantEstime() {
                              if (!this.selectedOption) return 0;
                              const n = Math.max(1, parseInt(this.nbPersonnes) || 1);
                              if (this.selectedOption.type_calcul === 'par_personne') return n * this.selectedOption.tarif;
                              if (this.selectedOption.type_calcul === 'fixe') return this.selectedOption.tarif;
                              return 0;
                          }
                      }">
                    @csrf

                    <div>
                        <label for="client_id" class="block text-sm font-medium text-[#4D4D4D] mb-1">Client</label>
                        <select id="client_id" name="client_id" required
                                class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020]">
                            <option value="">— Sélectionner un client —</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                    {{ $client->nom }}@if($client->telephone) — {{ $client->telephone }}@endif
                                </option>
                            @endforeach
                        </select>
                        <div class="mt-1"><a href="{{ route('clients.create') }}" target="_blank" class="text-xs text-[#800020] hover:underline">+ Créer un nouveau client</a></div>
                        @error('client_id') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="compartiment_id" class="block text-sm font-medium text-[#4D4D4D] mb-1">Compartiment</label>
                            <select id="compartiment_id" x-model="compartimentId" @change="optionId=''"
                                    class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020]">
                                <option value="">— Sélectionner —</option>
                                @foreach($compartiments as $c)
                                    <option value="{{ $c->id }}">{{ $c->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="option_id" class="block text-sm font-medium text-[#4D4D4D] mb-1">Option</label>
                            <select id="option_id" name="option_id" x-model="optionId" required :disabled="!compartimentId"
                                    class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020] disabled:bg-[#F5F5F5] disabled:text-[#4D4D4D]/60">
                                <option value="">— {{ 'Choisir un compartiment d\'abord' }} —</option>
                                <template x-for="opt in filteredOptions" :key="opt.id">
                                    <option :value="opt.id" x-text="opt.libelle + ' (' + opt.tarif.toLocaleString('fr-FR') + ' FCFA, ' + opt.type_calcul + ')'"></option>
                                </template>
                            </select>
                            @error('option_id') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="date_reservation" class="block text-sm font-medium text-[#4D4D4D] mb-1">Date</label>
                            <input id="date_reservation" name="date_reservation" type="date" value="{{ old('date_reservation') }}" required min="{{ date('Y-m-d') }}"
                                   class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020]" />
                            @error('date_reservation') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="nombre_personnes" class="block text-sm font-medium text-[#4D4D4D] mb-1">Nombre de personnes</label>
                            <input id="nombre_personnes" name="nombre_personnes" type="number" min="1" max="500" x-model.number="nbPersonnes" required
                                   class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020]" />
                            @error('nombre_personnes') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#4D4D4D] mb-1">Type de créneau</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="radio" name="type_creneau" value="journee" x-model="typeCreneau" class="text-[#800020] focus:ring-[#800020]" />
                                Journée entière
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="radio" name="type_creneau" value="plage_horaire" x-model="typeCreneau" class="text-[#800020] focus:ring-[#800020]" />
                                Plage horaire précise
                            </label>
                        </div>
                        @error('type_creneau') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="typeCreneau === 'plage_horaire'" x-transition>
                        <div>
                            <label for="heure_debut" class="block text-sm font-medium text-[#4D4D4D] mb-1">Heure début</label>
                            <input id="heure_debut" name="heure_debut" type="time" value="{{ old('heure_debut') }}"
                                   class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020]" />
                            @error('heure_debut') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="heure_fin" class="block text-sm font-medium text-[#4D4D4D] mb-1">Heure fin</label>
                            <input id="heure_fin" name="heure_fin" type="time" value="{{ old('heure_fin') }}"
                                   class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020]" />
                            @error('heure_fin') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="rounded-md bg-[#F5F5F5] border border-[#E0E0E0] p-4 space-y-3" x-show="selectedOption" x-transition>
                        <div>
                            <div class="text-xs text-[#4D4D4D] uppercase tracking-wider">Suggestion automatique</div>
                            <div class="text-lg font-semibold text-[#4D4D4D]" x-text="montantEstime.toLocaleString('fr-FR') + ' FCFA'"></div>
                            <button type="button" @click="$refs.montantInput.value = montantEstime"
                                    class="mt-1 text-xs text-[#800020] hover:underline">
                                Utiliser cette suggestion
                            </button>
                        </div>
                        <div class="pt-3 border-t border-[#E0E0E0]">
                            <label for="montant" class="block text-sm font-medium text-[#800020] mb-1">
                                Montant définitif (FCFA) <span class="text-xs text-[#4D4D4D] font-normal">(obligatoire, modifiable)</span>
                            </label>
                            <input id="montant" name="montant" type="number" min="0" required
                                   x-ref="montantInput"
                                   value="{{ old('montant') }}"
                                   placeholder="Entrer le montant..."
                                   class="block w-full rounded-md border-[#800020]/40 shadow-sm focus:border-[#800020] focus:ring-[#800020] transition-colors duration-200 text-lg font-semibold" />
                            @error('montant') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E0E0E0]">
                        <a href="{{ route('reservations.index') }}" class="inline-flex items-center rounded-md bg-white text-[#4D4D4D] px-4 py-2 text-sm border border-[#E0E0E0] hover:bg-[#F5F5F5]">Annuler</a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-md bg-[#800020] text-white px-4 py-2 text-sm font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#5C0018] hover:shadow-md hover:scale-[1.02] active:scale-[0.98]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            Créer la réservation
                        </button>
                    </div>

                    <div class="text-xs text-[#4D4D4D]/70 italic">
                        La réservation sera créée avec le statut « <strong>Liste d'attente</strong> » par défaut.
                        Elle passera à « Déjà payé » après validation du document justificatif (Bloc suivant).
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
