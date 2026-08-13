<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRejection;
use App\Models\Reservation;
use Illuminate\View\View;

class RejetDocumentController extends Controller
{
    public function index(): View
    {
        $this->authorize('voirComptabilite', Reservation::class);

        // Employés à surveiller : >= 2 rejets sur les 7 derniers jours.
        $employesASurveiller = DocumentRejection::query()
            ->with('utilisateur')
            ->where('created_at', '>=', now()->subDays(7))
            ->whereHas('utilisateur', fn ($q) => $q->where('role', 'employe'))
            ->selectRaw('utilisateur_id, COUNT(*) as nb')
            ->groupBy('utilisateur_id')
            ->having('nb', '>=', 2)
            ->get();

        $rejets = DocumentRejection::query()
            ->with(['utilisateur', 'reservation.client'])
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.rejets.index', [
            'rejets'              => $rejets,
            'employesASurveiller' => $employesASurveiller,
        ]);
    }
}
