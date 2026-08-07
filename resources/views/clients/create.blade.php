<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#800020] leading-tight">Nouveau client</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-[#E0E0E0] sm:rounded-lg p-6">
                <form method="POST" action="{{ route('clients.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="nom" class="block text-sm font-medium text-[#4D4D4D] mb-1">Nom complet</label>
                        <input id="nom" name="nom" type="text" value="{{ old('nom') }}" required autofocus
                               class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020] transition-colors duration-200" />
                        @error('nom') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-[#4D4D4D] mb-1">Téléphone <span class="text-xs text-[#4D4D4D]/70 font-normal">(optionnel)</span></label>
                        <input id="telephone" name="telephone" type="text" value="{{ old('telephone') }}" placeholder="+237 6XX XX XX XX"
                               class="block w-full rounded-md border-[#E0E0E0] shadow-sm focus:border-[#800020] focus:ring-[#800020] transition-colors duration-200" />
                        @error('telephone') <p class="mt-1 text-xs text-[#800020]">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('clients.index') }}" class="inline-flex items-center rounded-md bg-white text-[#4D4D4D] px-4 py-2 text-sm border border-[#E0E0E0] hover:bg-[#F5F5F5]">Annuler</a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[#800020] text-white px-4 py-2 text-sm font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#5C0018] hover:shadow-md hover:scale-[1.02] active:scale-[0.98]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
