<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VLife Réservations</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo/vlife-logo.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="antialiased bg-white text-gray-800">

    <header class="sticky top-0 z-40 bg-[#800020] text-white shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <span class="inline-flex items-center justify-center bg-white rounded-md p-1.5 shadow-sm transition-transform duration-200 group-hover:scale-105">
                    <img src="{{ asset('images/logo/vlife-logo.png') }}" alt="Logo VLife" class="h-10 w-auto" />
                </span>
                <span class="text-sm font-normal text-white/80 group-hover:text-white transition-colors duration-200 hidden sm:inline">Réservations</span>
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 rounded-md bg-white text-[#800020] px-4 py-2 text-sm font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#F5F5F5] hover:shadow hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                Se connecter
            </a>
        </div>
    </header>

    <section class="bg-white border-b border-[#E0E0E0]">
        <div class="max-w-7xl mx-auto px-6 py-16 md:py-20 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-[#800020] mb-4 tracking-tight">
                Gestion centralisée des réservations
            </h1>
            <p class="text-lg text-[#4D4D4D] max-w-2xl mx-auto leading-relaxed">
                Outil interne VLife pour la gestion des espaces VCoworking et VLounge-Sportbar.
                Consultez la disponibilité, créez des réservations et suivez les statuts en temps réel.
            </p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-14">
        <div class="flex items-center gap-3 mb-8">
            <div class="p-2 rounded-md bg-[#800020]/10 text-[#800020]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-semibold text-[#800020]">VCoworking</h2>
                <p class="text-sm text-[#4D4D4D]">Bureaux individuels et salle de conférence</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <figure class="group overflow-hidden rounded-lg shadow-sm ring-1 ring-[#E0E0E0] bg-white transition-all duration-300 ease-in-out hover:shadow-md hover:ring-[#800020]/20">
                <div class="aspect-[4/3] overflow-hidden bg-[#F5F5F5]">
                    <img src="{{ asset('images/espaces/bureau-premium.jpg') }}" alt="Bureau 3 Premium" class="w-full h-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-105">
                </div>
                <figcaption class="p-4">
                    <h3 class="font-semibold text-gray-900">Bureau 3 — Premium</h3>
                    <p class="text-sm text-[#4D4D4D] mt-1">Bureau individuel équipé — 10 000 FCFA</p>
                </figcaption>
            </figure>

            <figure class="group overflow-hidden rounded-lg shadow-sm ring-1 ring-[#E0E0E0] bg-white transition-all duration-300 ease-in-out hover:shadow-md hover:ring-[#800020]/20">
                <div class="aspect-[4/3] overflow-hidden bg-[#F5F5F5]">
                    <img src="{{ asset('images/espaces/bureaux-coworking.jpg') }}" alt="Bureaux 1 et 2" class="w-full h-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-105">
                </div>
                <figcaption class="p-4">
                    <h3 class="font-semibold text-gray-900">Bureaux 1 &amp; 2</h3>
                    <p class="text-sm text-[#4D4D4D] mt-1">Postes individuels — 4 000 FCFA chacun</p>
                </figcaption>
            </figure>

            <figure class="group overflow-hidden rounded-lg shadow-sm ring-1 ring-[#E0E0E0] bg-white transition-all duration-300 ease-in-out hover:shadow-md hover:ring-[#800020]/20">
                <div class="aspect-[4/3] overflow-hidden bg-[#F5F5F5]">
                    <img src="{{ asset('images/espaces/salle-conference.jpg') }}" alt="Salle de conférence" class="w-full h-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-105">
                </div>
                <figcaption class="p-4">
                    <h3 class="font-semibold text-gray-900">Salle de conférence</h3>
                    <p class="text-sm text-[#4D4D4D] mt-1">Location à la journée — 40 000 FCFA</p>
                </figcaption>
            </figure>

            <figure class="group overflow-hidden rounded-lg shadow-sm ring-1 ring-[#E0E0E0] bg-white transition-all duration-300 ease-in-out hover:shadow-md hover:ring-[#800020]/20">
                <div class="aspect-[4/3] overflow-hidden bg-[#F5F5F5]">
                    <img src="{{ asset('images/espaces/salle-conference-2.jpg') }}" alt="Salle de conférence — autre vue" class="w-full h-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-105">
                </div>
                <figcaption class="p-4">
                    <h3 class="font-semibold text-gray-900">Salle de conférence</h3>
                    <p class="text-sm text-[#4D4D4D] mt-1">Projection, WiFi, tableau — équipement pro</p>
                </figcaption>
            </figure>
        </div>
    </section>

    <section class="bg-[#F5F5F5] border-y border-[#E0E0E0]">
        <div class="max-w-7xl mx-auto px-6 py-14">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2 rounded-md bg-[#800020]/10 text-[#800020]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 22h8"/><path d="M7 10h10"/><path d="M12 15v7"/><path d="M12 15a5 5 0 0 0 5-5c0-2-.5-4-2-8H9c-1.5 4-2 6-2 8a5 5 0 0 0 5 5Z"/></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-semibold text-[#800020]">VLounge-Sportbar</h2>
                    <p class="text-sm text-[#4D4D4D]">Tables, espaces privés et location de la salle</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $sportbarOptions = [
                        ['titre' => 'Option A — Table', 'desc' => "À partir de 3 000 FCFA/personne", 'places' => '80 places partagées'],
                        ['titre' => "Option B — Location d'espace", 'desc' => 'Consommation libre, payée sur place', 'places' => 'Espace exclusif'],
                        ['titre' => 'Option C — Forfait', 'desc' => '5 000 FCFA/personne (occupation + conso)', 'places' => '80 places partagées'],
                        ['titre' => 'Option D — Salle du Lounge', 'desc' => 'Location complète, tarif négocié', 'places' => '~200 places'],
                    ];
                @endphp

                @foreach($sportbarOptions as $opt)
                    <div class="group overflow-hidden rounded-lg shadow-sm ring-1 ring-[#E0E0E0] bg-white transition-all duration-300 ease-in-out hover:shadow-md hover:ring-[#800020]/20">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[#F5F5F5] to-[#E0E0E0] flex items-center justify-center relative">
                            <svg class="w-16 h-16 text-[#800020]/25 transition-transform duration-500 ease-in-out group-hover:scale-110" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                            <span class="absolute bottom-2 right-2 text-[10px] uppercase tracking-wider text-[#4D4D4D]/60 bg-white/60 px-2 py-0.5 rounded">Photo à venir</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900">{{ $opt['titre'] }}</h3>
                            <p class="text-sm text-[#4D4D4D] mt-1">{{ $opt['desc'] }}</p>
                            <p class="text-xs text-[#4D4D4D]/70 mt-2 italic">{{ $opt['places'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-6 py-12 text-center">
            <p class="text-[#4D4D4D] mb-6">Accédez à l'outil de gestion pour créer, valider et suivre les réservations.</p>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#800020] text-white px-6 py-3 text-base font-semibold shadow-sm transition-all duration-200 ease-in-out hover:bg-[#5C0018] hover:shadow-md hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#800020] focus-visible:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                Se connecter à l'application
            </a>
        </div>
    </section>

    <footer class="bg-[#5C0018] text-white/80">
        <div class="max-w-7xl mx-auto px-6 py-8 text-center text-sm">
            <p>© {{ date('Y') }} VLife — Application interne de gestion des réservations</p>
            <p class="text-white/60 mt-1">Yaoundé, Cameroun</p>
        </div>
    </footer>

</body>
</html>
