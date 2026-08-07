<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-[#800020] leading-tight">Clients</h2>
            <a href="{{ route('clients.create') }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#800020] text-white px-4 py-2 text-sm font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#5C0018] hover:shadow-md hover:scale-[1.02] active:scale-[0.98]">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Nouveau client
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

            <form method="GET" action="{{ route('clients.index') }}" class="mb-4 flex gap-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher par nom ou téléphone..."
                       class="flex-1 rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020]" />
                <button type="submit" class="rounded-md bg-[#4D4D4D] text-white px-4 py-2 text-sm hover:bg-[#800020] transition-colors duration-200">
                    Rechercher
                </button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg">
                <table class="min-w-full divide-y divide-[#E0E0E0]">
                    <thead class="bg-[#F5F5F5]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Téléphone</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-[#4D4D4D] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E0E0E0]">
                        @forelse ($clients as $client)
                            <tr class="hover:bg-[#F5F5F5]/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $client->nom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#4D4D4D]">{{ $client->telephone ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                                    <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center gap-1 text-[#4D4D4D] hover:text-[#800020] transition-colors duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        Modifier
                                    </a>
                                    <form method="POST" action="{{ route('clients.destroy', $client) }}" class="inline" onsubmit="return confirm('Supprimer « {{ $client->nom }} » ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-[#800020] hover:text-[#5C0018] transition-colors duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-[#4D4D4D]">Aucun client enregistré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($clients->hasPages())<div class="mt-4">{{ $clients->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
