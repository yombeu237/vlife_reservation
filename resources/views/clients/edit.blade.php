<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#800020] leading-tight">Modifier le client</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg p-6">
                <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="nom" class="block text-sm font-medium text-[#4D4D4D] mb-1">Nom complet</label>
                        <input id="nom" name="nom" type="text" value="{{ old('nom', $client->nom) }}" required
                               class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020] transition-colors duration-200" />
                        @error('nom') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-[#4D4D4D] mb-1">Téléphone</label>
                        <input id="telephone" name="telephone" type="text" value="{{ old('telephone', $client->telephone) }}"
                               class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020] transition-colors duration-200" />
                        @error('telephone') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('clients.index') }}" class="inline-flex items-center rounded-md bg-white text-[#4D4D4D] px-4 py-2 text-sm border border-[#E0E0E0] hover:bg-[#F5F5F5]">Annuler</a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[#800020] text-white px-4 py-2 text-sm font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#5C0018] hover:shadow-md hover:scale-[1.02] active:scale-[0.98]">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
