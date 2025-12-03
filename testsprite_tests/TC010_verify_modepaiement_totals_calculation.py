import requests

BASE_URL = "http://localhost:8000"
AUTH = ("abdoullah@gmail.com", "12345678")
HEADERS = {"Accept": "application/json"}
TIMEOUT = 30

def test_verify_modepaiement_totals_calculation():
    """
    Test that ModePaiement dashboard correctly calculates totals by payment type (espèces vs numérique) and by source (factures vs depenses vs part_medecin).
    """
    session = requests.Session()
    session.auth = AUTH
    session.headers.update(HEADERS)

    created_caisse_id = None
    created_depense_id = None
    created_part_medecin_modepaiement_id = None

    try:
        # Step 1: Create a caisse transaction with ModePaiement entries covering espèces and numérique, sources = factures and part_medecin

        # Create a caisse transaction (facture) with ModePaiement
        caisse_payload = {
            "date": "2025-11-19",
            "total": 1500.0,
            "medecin_id": 1,
            "mode_paiements": [
                {"type": "espèces", "source": "facture", "montant": 500.0},
                {"type": "numérique", "source": "facture", "montant": 1000.0}
            ]
        }
        resp_caisse = session.post(f"{BASE_URL}/superadmin/caisses", json=caisse_payload, timeout=TIMEOUT)
        assert resp_caisse.status_code == 201, f"Create caisse failed: {resp_caisse.text}"
        caisse_data = resp_caisse.json()
        created_caisse_id = caisse_data.get("id")
        assert created_caisse_id is not None, "Created caisse has no ID"

        # Step 2: Create a depense with ModePaiement entries (espèces and numérique), source=depense
        depense_payload = {
            "date": "2025-11-19",
            "montant": 800.0,
            "description": "Test depense for ModePaiement totals",
            "mode_paiement": {"type": "espèces", "source": "depense", "montant": 800.0}
        }
        resp_depense = session.post(f"{BASE_URL}/depenses", json=depense_payload, timeout=TIMEOUT)
        assert resp_depense.status_code == 201, f"Create depense failed: {resp_depense.text}"
        depense_data = resp_depense.json()
        created_depense_id = depense_data.get("id")
        assert created_depense_id is not None, "Created depense has no ID"

        # Step 3: Create a ModePaiement entry for part_medecin source separately (simulate part_medecin receipt)
        part_medecin_payload = {
            "type": "numérique",
            "source": "part_medecin",
            "montant": 300.0,
            "date": "2025-11-19",
            "description": "Part medecin payment"
        }
        resp_part_medecin = session.post(f"{BASE_URL}/modepaiements", json=part_medecin_payload, timeout=TIMEOUT)
        assert resp_part_medecin.status_code == 201, f"Create part_medecin ModePaiement failed: {resp_part_medecin.text}"
        part_medecin_data = resp_part_medecin.json()
        created_part_medecin_modepaiement_id = part_medecin_data.get("id")
        assert created_part_medecin_modepaiement_id is not None, "Created part_medecin ModePaiement has no ID"

        # Step 4: Get ModePaiement dashboard totals summary endpoint
        resp_dashboard = session.get(f"{BASE_URL}/modepaiements/dashboard-totals", timeout=TIMEOUT)
        assert resp_dashboard.status_code == 200, f"Fetching ModePaiement dashboard totals failed: {resp_dashboard.text}"
        totals = resp_dashboard.json()

        # Expected totals calculation (from created data):
        # By payment type:
        # espèces: 500.0 (facture) + 800.0 (depense) = 1300.0
        # numérique: 1000.0 (facture) + 300.0 (part_medecin) = 1300.0
        #
        # By source:
        # factures: 1500.0 (500+1000)
        # depenses: 800.0
        # part_medecin: 300.0

        # Validate totals by payment type
        espèces_total = totals.get("by_payment_type", {}).get("espèces")
        numérique_total = totals.get("by_payment_type", {}).get("numérique")

        assert espèces_total is not None, "espèces total missing in dashboard totals"
        assert numérique_total is not None, "numérique total missing in dashboard totals"
        assert abs(espèces_total - 1300.0) < 0.01, f"espèces total mismatch: expected 1300.0, got {espèces_total}"
        assert abs(numérique_total - 1300.0) < 0.01, f"numérique total mismatch: expected 1300.0, got {numérique_total}"

        # Validate totals by source
        factures_total = totals.get("by_source", {}).get("facture")
        depenses_total = totals.get("by_source", {}).get("depense")
        part_medecin_total = totals.get("by_source", {}).get("part_medecin")

        assert factures_total is not None, "facture total missing in dashboard totals"
        assert depenses_total is not None, "depense total missing in dashboard totals"
        assert part_medecin_total is not None, "part_medecin total missing in dashboard totals"

        assert abs(factures_total - 1500.0) < 0.01, f"facture total mismatch: expected 1500.0, got {factures_total}"
        assert abs(depenses_total - 800.0) < 0.01, f"depense total mismatch: expected 800.0, got {depenses_total}"
        assert abs(part_medecin_total - 300.0) < 0.01, f"part_medecin total mismatch: expected 300.0, got {part_medecin_total}"

    finally:
        # Clean up created resources
        if created_part_medecin_modepaiement_id:
            session.delete(f"{BASE_URL}/modepaiements/{created_part_medecin_modepaiement_id}", timeout=TIMEOUT)
        if created_depense_id:
            session.delete(f"{BASE_URL}/depenses/{created_depense_id}", timeout=TIMEOUT)
        if created_caisse_id:
            session.delete(f"{BASE_URL}/superadmin/caisses/{created_caisse_id}", timeout=TIMEOUT)

test_verify_modepaiement_totals_calculation()