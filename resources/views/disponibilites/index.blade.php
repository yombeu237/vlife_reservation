<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#800020] leading-tight">Disponibilités en temps réel</h2>
            <div class="flex items-center gap-3">
                <span class="text-xs text-[#4D4D4D]">
                    <svg class="inline w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ $now->format('d/m/Y H:i:s') }}
                </span>
                <button type="button" onclick="location.reload()" class="text-xs text-[#800020] hover:underline flex items-center gap-1">
                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Rafraîchir
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ tick: 0 }" x-init="setInterval(() => tick++, 60000)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6 rounded-md bg-[#800020]/5 border border-[#800020]/20 px-4 py-3 text-sm text-[#4D4D4D]">
                Vue instantanée de l'occupation des espaces à <strong class="text-[#800020]">{{ $now->format('H:i') }}</strong>.
                Seules les réservations en statut « Déjà payé » ou « En cours » pour la date d'aujourd'hui sont comptées.
                <span class="text-xs italic">La page se rafraîchit automatiquement toutes les 60 secondes.</span>
            </div>

            <meta http-equiv="refresh" content="60" />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($etats as $e)
                    <div class="rounded-lg shadow-sm ring-1 {{ $e['disponible'] ? 'ring-[#E0E0E0] bg-white' : 'ring-[#800020]/30 bg-[#800020]/5' }} overflow-hidden transition-all duration-300">
                        <div class="px-4 py-3 flex items-center justify-between border-b {{ $e['disponible'] ? 'border-[#E0E0E0]' : 'border-[#800020]/20' }}">
                            <div class="min-w-0 flex-1">
                                <div class="text-xs uppercase tracking-wider text-[#4D4D4D]">{{ $e['compartiment'] }}</div>
                                <div class="text-sm font-semibold text-gray-900 truncate">{{ $e['option']->libelle }}</div>
                            </div>
                            @if($e['disponible'])
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#800020]/10 text-[#800020] px-2 py-1 text-xs font-semibold whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-[#800020] animate-pulse"></span>
                                    Disponible
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#800020] text-white px-2 py-1 text-xs font-semibold whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    Occupé
                                </span>
                            @endif
                        </div>

                        <div class="p-4 space-y-3">
                            <div>
                                <div class="flex items-center justify-between text-xs text-[#4D4D4D] mb-1">
                                    <span>{{ $e['exclusive'] ? 'Accès exclusif' : 'Capacité partagée' }}</span>
                                    <span class="font-medium">{{ $e['occupation'] }} / {{ $e['capacite'] }}</span>
                                </div>
                                @if(!$e['exclusive'])
                                    <div class="w-full h-2 rounded-full bg-[#E0E0E0] overflow-hidden">
                                        @php $pct = $e['capacite'] > 0 ? min(100, ($e['occupation'] / $e['capacite']) * 100) : 0; @endphp
                                        <div class="h-full bg-[#800020] transition-all duration-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="text-xs text-[#4D4D4D] mt-1">{{ $e['restant'] }} place(s) restante(s)</div>
                                @endif
                            </div>

                            @if($e['actives']->count() > 0)
                                <div class="pt-2 border-t border-[#E0E0E0]/50">
                                    <div class="text-xs text-[#4D4D4D] mb-2">Réservations actives :</div>
                                    <ul class="space-y-1">
                                        @foreach($e['actives'] as $active)
                                            <li class="text-xs text-gray-800 flex items-center gap-2">
                                                <svg class="w-3 h-3 text-[#800020]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                <a href="{{ route('reservations.show', $active) }}" class="hover:text-[#800020] hover:underline flex-1 truncate">
                                                    {{ $active->client->nom }} — {{ $active->nombre_personnes }} pers.
                                                    @if($active->type_creneau === 'plage_horaire')
                                                        ({{ substr($active->heure_debut, 0, 5) }} → {{ substr($active->heure_fin, 0, 5) }})
                                                    @else
                                                        (journée)
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($e['prochaine'])
                                <div class="pt-2 border-t border-[#E0E0E0]/50 text-xs text-[#4D4D4D]">
                                    <svg class="inline w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 2 12 6 12 12 16 14"/><circle cx="12" cy="12" r="10"/></svg>
                                    Prochaine réservation aujourd'hui : <strong>{{ substr($e['prochaine']->heure_debut, 0, 5) }}</strong> — {{ $e['prochaine']->client->nom }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
