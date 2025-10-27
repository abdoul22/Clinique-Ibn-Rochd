<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impression - Rendez-vous</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #333;
            font-size: 28px;
        }

        .print-btn {
            background: #3b82f6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .print-btn:hover {
            background: #2563eb;
        }

        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .filters-section h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }

        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-filter {
            background: #10b981;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn-filter:hover {
            background: #059669;
        }

        .btn-reset {
            background: #6b7280;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn-reset:hover {
            background: #4b5563;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .stat-card .number {
            color: #3b82f6;
            font-size: 32px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        tbody tr {
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        td {
            padding: 12px 15px;
            font-size: 14px;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-confirme {
            background: #d1fae5;
            color: #065f46;
        }

        .status-annule {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .filters-section,
            .print-btn,
            .filter-buttons {
                display: none;
            }

            .table-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .header {
                box-shadow: none;
                border-bottom: 2px solid #333;
                margin-bottom: 20px;
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                margin-bottom: 20px;
            }

            .stat-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1><i class="fas fa-calendar-check"></i> Impression des Rendez-vous</h1>
                <p style="color: #666; margin-top: 5px; font-size: 14px;">Clinique Ibn Rochd - Gestion Médicale</p>
            </div>
            <button class="print-btn" onclick="window.print()">
                <i class="fas fa-print mr-2"></i> Imprimer
            </button>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <h2><i class="fas fa-filter"></i> Filtres</h2>
            <form method="GET" action="{{ request()->url() }}" id="filterForm">
                <div class="filters">
                    <div class="filter-group">
                        <label for="medecin_id">Médecin</label>
                        <select name="medecin_id" id="medecin_id">
                            <option value="">-- Tous les médecins --</option>
                            @foreach($medecins as $medecin)
                                <option value="{{ $medecin->id }}" {{ request('medecin_id') == $medecin->id ? 'selected' : '' }}>
                                    {{ $medecin->prenom }} {{ $medecin->nom }} ({{ $medecin->specialite }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="date">Date exacte</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}">
                    </div>

                    <div class="filter-group">
                        <label for="date_start">Date début</label>
                        <input type="date" name="date_start" id="date_start" value="{{ request('date_start') }}">
                    </div>

                    <div class="filter-group">
                        <label for="date_end">Date fin</label>
                        <input type="date" name="date_end" id="date_end" value="{{ request('date_end') }}">
                    </div>

                    <div class="filter-group">
                        <label for="statut">Statut</label>
                        <select name="statut" id="statut">
                            <option value="">-- Tous les statuts --</option>
                            @foreach($statuts as $key => $label)
                                <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="patient_phone">Téléphone patient</label>
                        <input type="text" name="patient_phone" id="patient_phone" value="{{ request('patient_phone') }}" placeholder="Numéro du patient">
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ request()->url() }}" class="btn-reset" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Statistiques -->
        @if($rendezVous->count() > 0)
            <div class="stats">
                <div class="stat-card">
                    <h3>Total Rendez-vous</h3>
                    <div class="number">{{ $rendezVous->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Confirmés</h3>
                    <div class="number" style="color: #10b981;">{{ $rendezVous->where('statut', 'confirme')->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Annulés</h3>
                    <div class="number" style="color: #ef4444;">{{ $rendezVous->where('statut', 'annule')->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Taux de confirmation</h3>
                    <div class="number" style="color: #f59e0b;">
                        {{ $rendezVous->count() > 0 ? round(($rendezVous->where('statut', 'confirme')->count() / $rendezVous->count()) * 100, 1) : 0 }}%
                    </div>
                </div>
            </div>
        @endif

        <!-- Tableau -->
        <div class="table-container">
            @if($rendezVous->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Patient</th>
                            <th>Téléphone</th>
                            <th>Médecin</th>
                            <th>Spécialité</th>
                            <th>N° Entrée</th>
                            <th>Motif</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rendezVous as $rdv)
                            <tr>
                                <td>{{ $rdv->date_rdv ? \Carbon\Carbon::parse($rdv->date_rdv)->format('d/m/Y') : '—' }}</td>
                                <td>{{ $rdv->heure_rdv ?? '—' }}</td>
                                <td>
                                    <strong>{{ $rdv->patient->nom ?? '—' }} {{ $rdv->patient->prenom ?? '' }}</strong>
                                </td>
                                <td>{{ $rdv->patient->phone ?? '—' }}</td>
                                <td>
                                    <strong>{{ $rdv->medecin->prenom ?? '—' }} {{ $rdv->medecin->nom ?? '' }}</strong>
                                </td>
                                <td>{{ $rdv->medecin->specialite ?? '—' }}</td>
                                <td>
                                    <span style="background: #e0e7ff; color: #3730a3; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                                        {{ $rdv->numero_entree ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ $rdv->motif ?? '—' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $rdv->statut }}">
                                        {{ $statuts[$rdv->statut] ?? $rdv->statut }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucun rendez-vous trouvé</h3>
                    <p>Essayez d'ajuster vos filtres pour afficher les rendez-vous.</p>
                </div>
            @endif
        </div>

        <!-- Footer pour impression -->
        <div style="text-align: center; margin-top: 40px; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px;">
            <p>Clinique Ibn Rochd - Gestion Médicale</p>
            <p>Imprimé le {{ now()->format('d/m/Y à H:i:s') }} par {{ auth()->user()->name ?? 'Système' }}</p>
        </div>
    </div>

    <script>
        // Désactiver le filtre de date exacte si une plage de dates est utilisée
        document.getElementById('date_start')?.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('date').value = '';
                document.getElementById('date').disabled = true;
            }
        });

        document.getElementById('date_end')?.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('date').value = '';
                document.getElementById('date').disabled = true;
            }
        });

        document.getElementById('date')?.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('date_start').value = '';
                document.getElementById('date_end').value = '';
                document.getElementById('date_start').disabled = true;
                document.getElementById('date_end').disabled = true;
            }
        });

        // Réactiver les champs si on les efface
        function checkFields() {
            const hasDate = document.getElementById('date').value;
            const hasStart = document.getElementById('date_start').value;
            const hasEnd = document.getElementById('date_end').value;

            if (!hasStart && !hasEnd) {
                document.getElementById('date').disabled = false;
            }
            if (!hasDate) {
                document.getElementById('date_start').disabled = false;
                document.getElementById('date_end').disabled = false;
            }
        }

        document.getElementById('date_start')?.addEventListener('input', checkFields);
        document.getElementById('date_end')?.addEventListener('input', checkFields);
        document.getElementById('date')?.addEventListener('input', checkFields);
    </script>
</body>
</html>
