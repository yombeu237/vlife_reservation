<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#800020] leading-tight">
            Tableau de bord
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @php
                // Les réservations annulées ne sont jamais comptabilisées dans les totaux.
                $totalReservations = \App\Models\Reservation::nonAnnulees()->count();
                $enListeAttente    = \App\Models\Reservation::where('statut', 'liste_attente')->count();
                $dejaPaye          = \App\Models\Reservation::where('statut', 'deja_paye')->count();
                $enCours           = \App\Models\Reservation::where('statut', 'en_cours')->count();
                $aujourdhui        = \App\Models\Reservation::nonAnnulees()->whereDate('date_reservation', today())->count();
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#E0E0E0] p-5">
                    <div class="text-xs uppercase tracking-wider text-[#4D4D4D]">Total</div>
                    <div class="text-3xl font-bold text-[#800020] mt-1">{{ $totalReservations }}</div>
                    <div class="text-xs text-[#4D4D4D] mt-1">réservations</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#E0E0E0] p-5">
                    <div class="text-xs uppercase tracking-wider text-[#4D4D4D]">Aujourd'hui</div>
                    <div class="text-3xl font-bold text-[#800020] mt-1">{{ $aujourdhui }}</div>
                    <div class="text-xs text-[#4D4D4D] mt-1">réservations du jour</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#E0E0E0] p-5">
                    <div class="text-xs uppercase tracking-wider text-[#4D4D4D]">Liste d'attente</div>
                    <div class="text-3xl font-bold text-[#4D4D4D] mt-1">{{ $enListeAttente }}</div>
                    <div class="text-xs text-[#4D4D4D] mt-1">à traiter</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#E0E0E0] p-5">
                    <div class="text-xs uppercase tracking-wider text-[#4D4D4D]">En cours</div>
                    <div class="text-3xl font-bold text-[#800020] mt-1">{{ $enCours }}</div>
                    <div class="text-xs text-[#4D4D4D] mt-1">actives maintenant</div>
                </div>
            </div>

            <div class="bg-white shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-[#800020] mb-4">Actions rapides</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('reservations.create') }}"
                       class="inline-flex items-center gap-2 rounded-md bg-[#800020] text-white px-4 py-2 text-sm font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#5C0018] hover:shadow-md hover:scale-[1.02] active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                        Nouvelle réservation
                    </a>
                    <a href="{{ route('reservations.index') }}"
                       class="inline-flex items-center gap-2 rounded-md bg-white text-[#800020] border border-[#800020] px-4 py-2 text-sm font-semibold hover:bg-[#800020]/5 transition-all duration-200 ease-in-out active:scale-[0.98]">
                        Voir toutes les réservations
                    </a>
                    <a href="{{ route('clients.create') }}"
                       class="inline-flex items-center gap-2 rounded-md bg-white text-[#4D4D4D] border border-[#E0E0E0] px-4 py-2 text-sm font-semibold hover:bg-[#F5F5F5] transition-all duration-200 ease-in-out active:scale-[0.98]">
                        Nouveau client
                    </a>
                    @if(auth()->user()->isAdministrateur())
                        <a href="{{ route('admin.employes.index') }}"
                           class="inline-flex items-center gap-2 rounded-md bg-white text-[#4D4D4D] border border-[#E0E0E0] px-4 py-2 text-sm font-semibold hover:bg-[#F5F5F5] transition-all duration-200 ease-in-out active:scale-[0.98]">
                            Gérer les employés
                        </a>
                    @endif
                </div>

                <div class="mt-6 pt-6 border-t border-[#E0E0E0] text-sm text-[#4D4D4D]">
                    Connecté en tant que <strong class="text-gray-900">{{ auth()->user()->name }}</strong>
                    (<span class="text-[#800020]">{{ auth()->user()->isAdministrateur() ? 'Administrateur' : 'Employé' }}</span>)
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
