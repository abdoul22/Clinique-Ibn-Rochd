import requests

BASE_URL = "http://localhost:8000"
TIMEOUT = 30
AUTH = ("abdoullah@gmail.com", "12345678")

def test_verify_caisse_doctor_share_calculation():
    headers = {
        "Accept": "application/json",
        "Content-Type": "application/json",
    }

    # Step 1: Create an examen with a known part_medecin value
    examen_payload = {
        "libelle": "Examen test part_medecin",
        "prix": 1000,
        "part_medecin": 150,  # physician share per unit
        "part_cabinet": 850,
        "unite": "examen",
        "description": "Test examen for part_medecin calculation",
        "categorie": "Radiologie"
    }
    examen = None
    caisse = None
    try:
        r = requests.post(
            f"{BASE_URL}/api/examens",
            json=examen_payload,
            headers=headers,
            auth=AUTH,
            timeout=TIMEOUT,
        )
        r.raise_for_status()
        examen = r.json()
        examen_id = examen.get("id")
        assert examen_id is not None

        quantity = 3  # quantity for the caisse transaction

        # Step 2: Create a caisse transaction with one examen line with quantity
        caisse_payload = {
            "medecin_id": 1,  # We use a doctor ID 1 (assuming exists)
            "numero_entree": None,
            "total": examen_payload["prix"] * quantity,
            "examens": [
                {
                    "examen_id": examen_id,
                    "quantity": quantity,
                    "prix": examen_payload["prix"],
                }
            ],
            "part_medecin": examen_payload["part_medecin"] * quantity,
            "part_cabinet": examen_payload["part_cabinet"] * quantity,
            "patient_id": None,
            "mode_paiement_id": None,
            "assurance_id": None
        }

        r2 = requests.post(
            f"{BASE_URL}/api/caisses",
            json=caisse_payload,
            headers=headers,
            auth=AUTH,
            timeout=TIMEOUT,
        )
        r2.raise_for_status()
        caisse = r2.json()
        caisse_id = caisse.get("id")
        assert caisse_id is not None

        # Validate part_medecin correctness in the response
        returned_part_medecin = caisse.get("part_medecin")
        expected_part_medecin = examen_payload["part_medecin"] * quantity

        assert returned_part_medecin == expected_part_medecin, \
            f"Expected part_medecin {expected_part_medecin}, got {returned_part_medecin}"

    finally:
        # Cleanup: delete created caisse and examen if they exist
        if caisse and "id" in caisse:
            try:
                requests.delete(
                    f"{BASE_URL}/api/caisses/{caisse['id']}",
                    auth=AUTH,
                    timeout=TIMEOUT,
                )
            except Exception:
                pass

        if examen and "id" in examen:
            try:
                requests.delete(
                    f"{BASE_URL}/api/examens/{examen['id']}",
                    auth=AUTH,
                    timeout=TIMEOUT,
                )
            except Exception:
                pass

test_verify_caisse_doctor_share_calculation()
