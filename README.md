# VLife Réservations

Application interne de gestion des réservations pour VLife (VLounge-Sportbar + VCoworking).

## Stack

- **Laravel** 12+ (PHP 8.3)
- **MySQL** (InnoDB obligatoire)
- **Breeze + Livewire + Volt** (auth scaffolding)
- **Alpine.js** (interactions front dynamiques)
- **Tailwind CSS** (via Vite)
- **Icônes** : Lucide (inline SVG)

## Rôles

- **Employé** : réservations, clients, upload/validation document, changement statut manuel
- **Administrateur** : tout ce que fait un employé + gestion des comptes employés + création d'autres administrateurs (avec clé secrète) + annulation de réservations

## Installation locale

Pré-requis : PHP 8.3, Composer, Node 20+, MySQL avec `default_storage_engine=InnoDB`.

```bash
# 1. Cloner et installer
composer install
npm install

# 2. Configuration
cp .env.example .env
php artisan key:generate

# 3. Renseigner .env :
#    - DB_DATABASE=vlife_reservation (créer la base au préalable dans phpMyAdmin)
#    - ADMIN_CREATION_KEY=<clé secrète pour créer des admins>
#    - Ex : ADMIN_CREATION_KEY=$(php -r "echo bin2hex(random_bytes(32));")

# 4. Base et données de référence
php artisan migrate --seed

# 5. Créer un premier admin (via tinker, car role hors mass-assignment)
php artisan tinker
# > $u = User::create(['name' => 'Admin', 'email' => 'admin@vlife.local', 'password' => 'motdepasse']);
# > $u->role = 'administrateur';
# > $u->save();

# 6. Build assets et démarrer
npm run build
php artisan serve
```

Accès : http://127.0.0.1:8000

## Statuts automatiques (cron)

Sur serveur, ajouter au crontab :
```
* * * * * cd /path/to/vlife-reservations && php artisan schedule:run >> /dev/null 2>&1
```
En local (Windows), exécuter ponctuellement : `php artisan reservations:auto-statuts`.

## Architecture — points clés

- **Pattern Stratégie** pour le calcul des tarifs : `app/Services/Calculs/` (3 classes + 1 interface). Méthode `OptionReservation::calculerMontant()` délègue selon `type_calcul`.
- **Middleware `role`** : `->middleware('role:administrateur')` sur les routes admin. Défini dans `app/Http/Middleware/EnsureRole.php`, enregistré dans `bootstrap/app.php`.
- **Colonne `users.role`** : `employe` (défaut) ou `administrateur`. **Jamais dans le `Fillable`** — assignée uniquement par les contrôleurs admin.
- **Clé secrète admin** : `config('app.admin_creation_key')`, comparée avec `hash_equals()` (anti-timing attack).
- **Documents justificatifs** : stockés dans `storage/app/documents_justificatifs/`, téléchargement via route authentifiée `reservations.telecharger-document`.
- **Aucune suppression physique** de réservation : `destroy()` passe le statut à `annule`.

## Tests

```bash
php artisan test
```

Tests unitaires disponibles : `CalculateurTarifTest` (3 stratégies, 4 cas testés).

## Ce qui n'est PAS dans le périmètre

- Réservation en ligne par le client (toujours faite par un employé)
- Paiement en ligne (uniquement preuve documentaire)
- Compartiments VCuts et Vitech Training Center
- Promotion automatique de la liste d'attente

Voir `CLAUDE_1.md` pour l'ensemble des décisions d'architecture et l'historique du projet.
