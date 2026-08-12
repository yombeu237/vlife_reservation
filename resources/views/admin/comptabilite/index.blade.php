<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#800020] leading-tight">Comptabilité</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Cartes de synthèse --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-[#800020] text-white rounded-lg shadow-sm p-5">
                    <div class="text-xs uppercase tracking-wider text-white/80">Aujourd'hui</div>
                    <div class="text-2xl font-bold mt-1">{{ number_format($totalJour, 0, ',', ' ') }}</div>
                    <div class="text-xs text-white/70 mt-1">FCFA</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#E0E0E0] p-5">
                    <div class="text-xs uppercase tracking-wider text-[#4D4D4D]">Cette semaine</div>
                    <div class="text-2xl font-bold text-[#800020] mt-1">{{ number_format($totalSemaine, 0, ',', ' ') }}</div>
                    <div class="text-xs text-[#4D4D4D] mt-1">FCFA</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#E0E0E0] p-5">
                    <div class="text-xs uppercase tracking-wider text-[#4D4D4D]">Ce mois</div>
                    <div class="text-2xl font-bold text-[#800020] mt-1">{{ number_format($totalMois, 0, ',', ' ') }}</div>
                    <div class="text-xs text-[#4D4D4D] mt-1">FCFA</div>
                </div>
                <div class="bg-[#5C0018] text-white rounded-lg shadow-sm p-5">
                    <div class="text-xs uppercase tracking-wider text-white/80">Total sélection</div>
                    <div class="text-2xl font-bold mt-1">{{ number_format($totalGains, 0, ',', ' ') }}</div>
                    <div class="text-xs text-white/70 mt-1">{{ $nbReservations }} réservation(s) payée(s)</div>
                </div>
            </div>

            {{-- Répartition mensuelle (6 derniers mois) --}}
            @if($repartitionMensuelle->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#E0E0E0] p-6">
                    <h3 class="text-sm font-semibold text-[#800020] uppercase tracking-wider mb-4">Répartition mensuelle (6 derniers mois)</h3>
                    @php $maxMois = max($repartitionMensuelle->values()->toArray() ?: [1]); @endphp
                    <div class="space-y-3">
                        @foreach($repartitionMensuelle as $mois => $montant)
                            @php
                                $carbon = \Carbon\Carbon::createFromFormat('Y-m', $mois);
                                $pct = $maxMois > 0 ? ($montant / $maxMois) * 100 : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs text-[#4D4D4D] mb-1">
                                    <span class="capitalize">{{ $carbon->translatedFormat('F Y') }}</span>
                                    <span class="font-semibold text-[#800020]">{{ number_format($montant, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="w-full h-3 rounded-full bg-[#F5F5F5] overflow-hidden">
                                    <div class="h-full bg-[#800020] rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Filtres --}}
            <form method="GET" action="{{ route('admin.comptabilite.index') }}" class="bg-white rounded-lg shadow-sm ring-1 ring-[#E0E0E0] p-4 flex flex-wrap gap-3 items-end">
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
                    <label class="block text-xs text-[#4D4D4D] mb-1">Du</label>
                    <input type="date" name="du" value="{{ $du }}" class="rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020] text-sm" />
                </div>
                <div>
                    <label class="block text-xs text-[#4D4D4D] mb-1">Au</label>
                    <input type="date" name="au" value="{{ $au }}" class="rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020] text-sm" />
                </div>
                <button type="submit" class="rounded-md bg-[#800020] text-white px-4 py-2 text-sm font-semibold hover:bg-[#5C0018] transition-colors">Filtrer</button>
                @if($compartimentSelected || $du || $au)
                    <a href="{{ route('admin.comptabilite.index') }}" class="rounded-md bg-white text-[#4D4D4D] px-4 py-2 text-sm border border-[#E0E0E0] hover:bg-[#F5F5F5]">Réinitialiser</a>
                @endif
            </form>

            {{-- Historique détaillé --}}
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg">
                <div class="px-6 py-3 border-b border-[#E0E0E0] bg-[#F5F5F5]">
                    <h3 class="text-sm font-semibold text-[#800020]">Historique des gains</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#E0E0E0]">
                        <thead class="bg-[#F5F5F5]">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Option</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Employé</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Statut</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#E0E0E0]">
                            @php
                                $statutLabels = ['deja_paye' => 'Déjà payé', 'en_cours' => 'En cours', 'termine' => 'Terminé'];
                            @endphp
                            @forelse($historique as $r)
                                <tr class="hover:bg-[#F5F5F5]/50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $r->date_reservation?->format('d/m/Y') }}
                                        @if($r->estMultiJours())
                                            <span class="text-xs text-[#800020]">→ {{ $r->dateFinEffective()->format('d/m/Y') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $r->client->nom }}</td>
                                    <td class="px-4 py-3 text-sm text-[#4D4D4D]">
                                        {{ $r->option->libelle }}
                                        <div class="text-xs text-[#4D4D4D]/70">{{ $r->option->compartiment->nom }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-[#4D4D4D]">{{ $r->utilisateur->name }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-md bg-[#800020]/10 text-[#800020] px-2 py-0.5 text-xs font-medium">
                                            {{ $statutLabels[$r->statut] ?? $r->statut }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ number_format($r->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-[#4D4D4D]">Aucune réservation payée sur cette sélection.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($historique->hasPages())<div>{{ $historique->links() }}</div>@endif

        </div>
    </div>
</x-app-layout>
