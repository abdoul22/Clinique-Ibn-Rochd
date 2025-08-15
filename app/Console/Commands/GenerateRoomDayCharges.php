<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hospitalisation;
use App\Models\HospitalisationCharge;
use App\Models\HospitalizationRoomStay;
use Carbon\Carbon;

class GenerateRoomDayCharges extends Command
{
    protected $signature = 'hospitalisation:generate-room-days';
    protected $description = 'Génère les charges de nuitée (room_day) toutes les 24h pour les hospitalisations en cours';

    public function handle(): int
    {
        $now = Carbon::now();

        $hospitalisations = Hospitalisation::where('statut', 'en cours')
            ->whereNotNull('next_charge_due_at')
            ->where('next_charge_due_at', '<=', $now)
            ->get();

        foreach ($hospitalisations as $h) {
            // Déterminer la chambre courante via le lit
            $chambre = optional(optional($h->lit)->chambre);
            $roomStay = HospitalizationRoomStay::where('hospitalisation_id', $h->id)
                ->whereNull('end_at')
                ->latest('start_at')
                ->first();

            if (!$roomStay && $chambre) {
                $roomStay = HospitalizationRoomStay::create([
                    'hospitalisation_id' => $h->id,
                    'chambre_id' => $chambre->id,
                    'start_at' => $h->admission_at ?? $h->date_entree,
                ]);
            }

            while (Carbon::parse($h->next_charge_due_at)->lte($now)) {
                $tarif = $chambre ? ($chambre->tarif_journalier ?? 0) : 0;
                HospitalisationCharge::create([
                    'hospitalisation_id' => $h->id,
                    'room_stay_id' => optional($roomStay)->id,
                    'type' => 'room_day',
                    'description_snapshot' => 'Nuitée chambre',
                    'unit_price' => $tarif,
                    'quantity' => 1,
                    'total_price' => $tarif,
                    'part_medecin' => 0,
                    'part_cabinet' => $tarif,
                    'is_pharmacy' => false,
                ]);

                // Prochaine échéance 24h plus tard
                $h->next_charge_due_at = Carbon::parse($h->next_charge_due_at)->addDay();
                $h->save();
            }
        }

        $this->info('Génération des nuitées terminée.');
        return self::SUCCESS;
    }
}
