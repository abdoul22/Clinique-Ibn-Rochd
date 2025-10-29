<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\ModePaiement;
use App\Models\Credit;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SituationJournaliereController extends Controller
{
    public function index(Request $request)
    {
        // Default to today if no date specified
        $date = $request->input('date', now()->format('Y-m-d'));
        $dateCarbon = Carbon::parse($date);

        // Get caisses for the selected date with all relationships
        $caisses = Caisse::whereDate('date_examen', $date)
            ->with(['service', 'examen.service', 'medecin', 'mode_paiements'])
            ->get();

        // Build data structure grouped by actual service
        $servicesData = [];

        foreach ($caisses as $caisse) {
            // Get the actual service - PRIORITY:
            // 1. From examen->service (via idsvc) - THIS IS THE REAL SERVICE
            // 2. From caisse->service (direct relationship) - FALLBACK
            $service = null;

            // First try to get from examen (most reliable)
            if ($caisse->examen && $caisse->examen->service) {
                $service = $caisse->examen->service;
            }
            // Fallback to caisse service
            elseif ($caisse->service) {
                $service = $caisse->service;
            }

            // Skip if no service found
            if (!$service) {
                continue;
            }

            $medecin = $caisse->medecin;
            $examen = $caisse->examen;

            if (!$medecin) {
                continue;
            }

            // Check if this is a pharmaceutical product
            // Group all pharmacy items under "PHARMACIE" service
            $serviceName = $service->nom;

            // Check if service type is PHARMACIE or has pharmacy relation
            if (
                $service->type_service === 'PHARMACIE' ||
                $service->type_service === 'pharmacie' ||
                strtoupper($service->nom) === 'PHARMACIE' ||
                $service->pharmacie_id !== null
            ) {
                $serviceName = 'PHARMACIE';
                // Use a consistent service ID for all pharmacy items
                $serviceId = 'pharmacie-group';
            } else {
                $serviceId = $service->id;
            }

            $medecinId = $medecin->id;

            // Initialize service if not exists
            if (!isset($servicesData[$serviceId])) {
                $servicesData[$serviceId] = [
                    'service_name' => $serviceName,
                    'medecins' => [],
                    'total_actes' => 0,
                ];
            }

            // Initialize medecin if not exists
            if (!isset($servicesData[$serviceId]['medecins'][$medecinId])) {
                $servicesData[$serviceId]['medecins'][$medecinId] = [
                    'nom' => $medecin->nomcomplet,
                    'examens' => [],
                    'nombre_actes' => 0,
                ];
            }

            // Track examen
            $examenNom = $examen ? $examen->nom : 'Examen inconnu';
            if (!isset($servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom])) {
                $servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom] = 0;
            }
            $servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom] += 1;

            // Count acts
            $servicesData[$serviceId]['medecins'][$medecinId]['nombre_actes'] += 1;
            $servicesData[$serviceId]['total_actes'] += 1;
        }

        // Convert medecins arrays to indexed arrays for the view
        foreach ($servicesData as &$service) {
            $service['medecins'] = array_values($service['medecins']);
        }

        // Calculate only total acts
        $totalActes = array_sum(array_column($servicesData, 'total_actes'));

        // Get payment modes used for this date (only online payments: bankily, masrvi, sedad)
        $paiementsEnLigne = ModePaiement::whereHas('caisse', function ($query) use ($date) {
            $query->whereDate('date_examen', $date);
        })
            ->whereIn('type', ['bankily', 'masrvi', 'sedad'])
            ->selectRaw('type, SUM(montant) as total')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        // Check if there are any online payments
        $hasOnlinePayments = $paiementsEnLigne->isNotEmpty();

        // Get credits for this date (either linked to caisse of that date OR created on that date)
        $creditsPersonnel = Credit::where('source_type', 'App\Models\Personnel')
            ->where('status', '!=', 'payé')
            ->where(function ($query) use ($date) {
                $query->whereHas('caisse', function ($q) use ($date) {
                    $q->whereDate('date_examen', $date);
                })
                    ->orWhereDate('created_at', $date);
            })
            ->sum('montant');

        $creditsAssurance = Credit::where('source_type', 'App\Models\Assurance')
            ->where('status', '!=', 'payé')
            ->where(function ($query) use ($date) {
                $query->whereHas('caisse', function ($q) use ($date) {
                    $q->whereDate('date_examen', $date);
                })
                    ->orWhereDate('created_at', $date);
            })
            ->sum('montant');

        // Calculate total part médecin for the date
        $totalPartMedecin = $caisses->sum(function ($caisse) {
            return $caisse->mode_paiements->where('source', 'part_medecin')->sum('montant');
        });

        // Get total pharmacie for the date
        $totalPharmacie = $caisses->sum(function ($caisse) {
            $service = $caisse->examen && $caisse->examen->service
                ? $caisse->examen->service
                : $caisse->service;

            if (!$service) {
                return 0;
            }

            // Check if this is a pharmaceutical product
            if (
                $service->type_service === 'PHARMACIE' ||
                $service->type_service === 'pharmacie' ||
                strtoupper($service->nom) === 'PHARMACIE' ||
                $service->pharmacie_id !== null
            ) {
                return $caisse->total ?? 0;
            }

            return 0;
        });

        return view('situation-journaliere.index', compact(
            'date',
            'dateCarbon',
            'servicesData',
            'totalActes',
            'paiementsEnLigne',
            'hasOnlinePayments',
            'creditsPersonnel',
            'creditsAssurance',
            'totalPartMedecin',
            'totalPharmacie'
        ));
    }

    public function print(Request $request)
    {
        return $this->index($request);
    }

    public function exportPdf(Request $request)
    {
        return $this->index($request);
    }
}
