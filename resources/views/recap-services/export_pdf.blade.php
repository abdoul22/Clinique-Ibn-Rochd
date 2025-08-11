{{-- resources/views/recapitulatif_service_journiers/export_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>PDF - Récapitulatif journalier des services</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }

        .header h2 {
            color: #1f2937;
            margin: 0;
            font-size: 20px;
        }

        .resume-table {
            margin: 15px 0;
            width: 100%;
            border-collapse: collapse;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 6px;
            overflow: hidden;
        }

        .resume-table th,
        .resume-table td {
            border: none;
            padding: 8px;
            text-align: center;
            color: #1f2937;
        }

        .resume-table th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: bold;
        }

        .resume-table td {
            background: rgba(255, 255, 255, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            overflow: hidden;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }

        th {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Pas d'effet hover pour le PDF */

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-examen {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-medicament {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-consultation {
            background-color: #f3e8ff;
            color: #7c3aed;
        }

        .badge-pharmacie {
            background-color: #fce7f3;
            color: #be185d;
        }

        .badge-medecins {
            background-color: #fef3c7;
            color: #d97706;
        }

        .badge-default {
            background-color: #f3f4f6;
            color: #374151;
        }

        .service-name {
            font-weight: bold;
            color: #1f2937;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Récapitulatif Journalier des Services</h2>
        <p style="color: #6b7280; margin: 3px 0; font-size: 11px;">Clinique Ibn Rochd</p>
    </div>

    @if(isset($resume))
    <table class="resume-table">
        <tr>
            <th>Total des Actes</th>
            <th>Total des Recettes</th>
        </tr>
        <tr>
            <td>{{ number_format($resume['total_actes'], 0, ',', ' ') }}</td>
            <td>{{ number_format($resume['total_recettes'], 0, ',', ' ') }} MRU</td>
        </tr>
    </table>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Service</th>
                <th>Nombre d'actes</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recaps as $recap)
            @php
            $serviceModel = \App\Models\Service::find($recap->service_id);
            $badgeClass = 'badge-default';
            if ($serviceModel) {
            $type = $serviceModel->type_service;
            switch($type) {
            case 'LABORATOIRE': $badgeClass = 'badge-examen'; break;
            case 'PHARMACIE': $badgeClass = 'badge-pharmacie'; break;
            case 'MEDECINE DENTAIRE': $badgeClass = 'badge-medecins'; break;
            case 'IMAGERIE MEDICALE': $badgeClass = 'badge-examen'; break;
            case 'CONSULTATIONS EXTERNES': $badgeClass = 'badge-consultation'; break;
            case 'HOSPITALISATION': $badgeClass = 'badge-default'; break;
            case 'BLOC OPERATOIRE': $badgeClass = 'badge-default'; break;
            case 'INFIRMERIE': $badgeClass = 'badge-default'; break;
            case 'EXPLORATIONS FONCTIONNELLES': $badgeClass = 'badge-examen'; break;
            case 'medicament': $badgeClass = 'badge-pharmacie'; break; // compat
            case 'pharmacie': $badgeClass = 'badge-pharmacie'; break; // compat
            }
            }
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <div class="service-name">{{ $services[$recap->service_id] ?? 'Service non assigné' }}</div>
                    @if($serviceModel)
                    <span class="badge {{ $badgeClass }}">{{ $serviceModel->type_service === 'medicament' ? 'PHARMACIE'
                        : $serviceModel->type_service }}</span>
                    @endif
                </td>
                <td style="text-align: center; font-weight: bold; color: #059669;">{{ $recap->nombre }}</td>
                <td style="text-align: right; font-weight: bold; color: #dc2626;">{{ number_format($recap->total, 0,
                    ',', ' ') }} MRU</td>
                <td>{{ \Carbon\Carbon::parse($recap->jour)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>