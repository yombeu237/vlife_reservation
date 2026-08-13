<?php

namespace App\Services\Documents;

use App\Models\Reservation;

/**
 * Couche de vérification des preuves de paiement VLife.
 *
 * Contexte : les preuves sont des documents papier photographiés, NON marqués « VLife »,
 * au contenu manuscrit. Deux familles :
 *   - Reçu vert « VITECH TRAINING CENTER (VTC) »  → réservations VCoworking
 *   - Facture à grille (FACTURE No / Total TTC)   → réservations VLounge-Sportbar
 *
 * Tesseract n'étant pas disponible, la vérification est « assistée » : l'employé déclare
 * le type de document (décrit visuellement) et saisit date + montant + n° facture d'après
 * le papier. Le service vérifie la cohérence type↔compartiment et l'absence de doublon
 * (empreinte fichier + signature métier). Aucune dépendance à l'OCR : 100 % fiable.
 *
 * Extension future : une méthode analyserParOcr() pourra confirmer le type automatiquement
 * quand Tesseract sera installé, sans changer ce contrat.
 */
class VerificateurDocument
{
    public const TYPE_VTC_COWORKING   = 'vtc_coworking';
    public const TYPE_FACTURE_SPORTBAR = 'facture_sportbar';

    public const LIBELLES = [
        self::TYPE_VTC_COWORKING    => 'Reçu VITECH TRAINING CENTER (VTC)',
        self::TYPE_FACTURE_SPORTBAR => 'Facture VLounge-Sportbar (à grille)',
    ];

    /** Type de document attendu selon le compartiment de la réservation. */
    public function typeAttendu(Reservation $reservation): string
    {
        $nom = mb_strtolower((string) optional(optional($reservation->option)->compartiment)->nom);

        if (str_contains($nom, 'coworking')) {
            return self::TYPE_VTC_COWORKING;
        }

        // VLounge-Sportbar (et tout le reste par défaut) → facture à grille.
        return self::TYPE_FACTURE_SPORTBAR;
    }

    public function libelle(string $type): string
    {
        return self::LIBELLES[$type] ?? $type;
    }

    /**
     * Vérifie une preuve avant stockage.
     *
     * @return array{ok: bool, code: ?string, raison: ?string}
     */
    public function verifier(
        Reservation $reservation,
        string $typeDeclare,
        string $dateDocument,
        int $montantDocument,
        ?string $numeroFacture,
        string $hashFichier
    ): array {
        $attendu = $this->typeAttendu($reservation);

        // 1) Le type déclaré doit correspondre au compartiment.
        if ($typeDeclare !== $attendu) {
            return [
                'ok'     => false,
                'code'   => 'type_incoherent',
                'raison' => sprintf(
                    'Le document fourni est un « %s », mais cette réservation (%s) exige un « %s ». Merci de fournir le bon document.',
                    $this->libelle($typeDeclare),
                    optional(optional($reservation->option)->compartiment)->nom ?? 'compartiment inconnu',
                    $this->libelle($attendu),
                ),
            ];
        }

        // 2) Anti-doublon : même fichier déjà téléversé sur une autre réservation.
        $memeFichier = Reservation::where('hash_document', $hashFichier)
            ->where('id', '!=', $reservation->id)
            ->first();

        if ($memeFichier) {
            return [
                'ok'     => false,
                'code'   => 'doublon_fichier',
                'raison' => "Ce fichier image a déjà été utilisé pour la réservation #{$memeFichier->id}. Une même preuve ne peut pas servir deux fois.",
            ];
        }

        // 3) Anti-doublon : même signature métier (type + date + montant + n°) déjà validée ailleurs.
        $signature = Reservation::where('type_document', $typeDeclare)
            ->where('date_document', $dateDocument)
            ->where('montant_document', $montantDocument)
            ->when($numeroFacture, fn ($q) => $q->where('numero_facture', $numeroFacture))
            ->whereNotNull('date_validation_preuve')
            ->where('id', '!=', $reservation->id)
            ->first();

        if ($signature) {
            return [
                'ok'     => false,
                'code'   => 'doublon_signature',
                'raison' => sprintf(
                    'Une preuve identique (même date %s, même montant %s FCFA%s) a déjà été validée pour la réservation #%d. Facture probablement déjà utilisée.',
                    $dateDocument,
                    number_format($montantDocument, 0, ',', ' '),
                    $numeroFacture ? ", n° {$numeroFacture}" : '',
                    $signature->id,
                ),
            ];
        }

        return ['ok' => true, 'code' => null, 'raison' => null];
    }
}
