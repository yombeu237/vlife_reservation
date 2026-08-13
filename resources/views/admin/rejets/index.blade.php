<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#800020] leading-tight">Documents refusés</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($employesASurveiller->isNotEmpty())
                <div class="rounded-md bg-[#800020] text-white px-4 py-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                        <div>
                            <div class="font-semibold">Employés à surveiller (≥ 2 documents refusés sur 7 jours)</div>
                            <ul class="mt-2 space-y-1 text-sm text-white/90">
                                @foreach($employesASurveiller as $e)
                                    <li>• {{ $e->utilisateur?->name ?? 'Utilisateur #' . $e->utilisateur_id }} — <strong>{{ $e->nb }} refus</strong></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg">
                <div class="px-6 py-3 border-b border-[#E0E0E0] bg-[#F5F5F5]">
                    <h3 class="text-sm font-semibold text-[#800020]">Historique des refus de documents</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#E0E0E0]">
                        <thead class="bg-[#F5F5F5]">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Employé</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Réservation</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Motif</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Détail</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#E0E0E0]">
                            @php
                                $codeLabels = [
                                    'type_incoherent'   => 'Mauvais type de document',
                                    'doublon_signature' => 'Facture déjà utilisée',
                                    'doublon_fichier'   => 'Fichier déjà envoyé',
                                ];
                            @endphp
                            @forelse($rejets as $r)
                                <tr class="hover:bg-[#F5F5F5]/50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm text-[#4D4D4D] whitespace-nowrap">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $r->utilisateur?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($r->reservation)
                                            <a href="{{ route('reservations.show', $r->reservation) }}" class="text-[#800020] hover:underline">#{{ $r->reservation_id }}</a>
                                            <div class="text-xs text-[#4D4D4D]">{{ $r->reservation->client?->nom }}</div>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-md bg-[#800020]/10 text-[#800020] px-2 py-0.5 text-xs font-medium">
                                            {{ $codeLabels[$r->code_raison] ?? $r->code_raison }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-[#4D4D4D] max-w-md">{{ $r->raison }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-[#4D4D4D]">Aucun document refusé à ce jour.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($rejets->hasPages())<div>{{ $rejets->links() }}</div>@endif

        </div>
    </div>
</x-app-layout>
