<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#800020] leading-tight">
                Gestion des employés
            </h2>
            <a href="{{ route('admin.employes.create') }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#800020] text-white px-4 py-2 text-sm font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#5C0018] hover:shadow-md hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#800020] focus-visible:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Nouvel employé
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

            <div class="mb-6 flex justify-end">
                <a href="{{ route('admin.administrateurs.create') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-white text-[#800020] border border-[#800020] px-4 py-2 text-sm font-medium transition-all duration-200 ease-in-out hover:bg-[#F5F5F5] active:scale-[0.98]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-3-3.87"/><path d="M4 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M22 11h-6"/><path d="M19 8v6"/></svg>
                    Créer un administrateur
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg">
                <table class="min-w-full divide-y divide-[#E0E0E0]">
                    <thead class="bg-[#F5F5F5]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Créé le</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E0E0E0]">
                        @forelse ($employes as $employe)
                            <tr class="hover:bg-[#F5F5F5]/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $employe->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#4D4D4D]">{{ $employe->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#4D4D4D]">{{ $employe->created_at?->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <form method="POST" action="{{ route('admin.employes.destroy', $employe) }}" onsubmit="return confirm('Confirmer la suppression du compte de {{ $employe->name }} ?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-[#800020] hover:text-[#5C0018] transition-colors duration-150 active:scale-95">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-[#4D4D4D]">
                                    Aucun employé enregistré pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($employes->hasPages())
                <div class="mt-4">{{ $employes->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
