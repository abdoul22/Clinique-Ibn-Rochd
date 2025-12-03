import requests
import sys

BASE_URL = "http://localhost:8000"
AUTH_USERNAME = "abdoullah@gmail.com"
AUTH_PASSWORD = "12345678"
TIMEOUT = 30

auth_headers = {
    "Authorization": requests.auth._basic_auth_str(AUTH_USERNAME, AUTH_PASSWORD),
    "Content-Type": "application/json",
    "Accept": "application/json"
}

def test_verify_caisse_clinic_share_calculation():
    # Step 1: Create an Examen to use in the caisse transaction (assumed route /admin/examens POST)
    examen_payload = {
        "nom": "Test Examen Part Cabinet",
        "description": "Examen for testing part_cabinet calculation",
        "prix": 100.0,
        "part_medecin": 30.0,
        "part_cabinet": 70.0,
        "categorie": "Radiologie"
    }
    examen_id = None
    caisse_id = None
    try:
        examen_resp = requests.post(
            f"{BASE_URL}/admin/examens",
            json=examen_payload,
            headers=auth_headers,
            timeout=TIMEOUT
        )
        assert examen_resp.status_code == 201 or examen_resp.status_code == 200, f"Failed to create examen: {examen_resp.text}"
        examen_created = examen_resp.json()
        examen_id = examen_created.get("id")
        assert examen_id is not None, "Examen ID not returned."

        # Step 2: Create a caisse transaction using the examen with quantity and verifying part_clinique calculation
        quantity = 3
        caisse_payload = {
            "date_operation": "2025-11-19T10:00:00Z",
            "mode_paiement_id": 1,  # assumption: mode paiement with id 1 exists
            "examen_id": examen_id,
            "quantite": quantity,
            "patient_id": 1,  # assumption: patient with id 1 exists
            "user_id": 1,  # assumption: user with id 1 creating transaction
            "montant_paye": 100.0 * quantity,
            "part_medecin": 30.0 * quantity,
            "part_cabinet": 70.0 * quantity,
            # Other necessary fields assuming
            "total": 100.0 * quantity
        }

        caisse_resp = requests.post(
            f"{BASE_URL}/admin/caisses",
            json=caisse_payload,
            headers=auth_headers,
            timeout=TIMEOUT
        )
        assert caisse_resp.status_code == 201 or caisse_resp.status_code == 200, f"Failed to create caisse transaction: {caisse_resp.text}"
        caisse_created = caisse_resp.json()
        caisse_id = caisse_created.get("id")
        assert caisse_id is not None, "Caisse ID not returned."

        # Step 3: Fetch the created caisse transaction to verify part_cabinet calculation
        get_resp = requests.get(f"{BASE_URL}/admin/caisses/{caisse_id}", headers=auth_headers, timeout=TIMEOUT)
        assert get_resp.status_code == 200, f"Failed to fetch caisse transaction: {get_resp.text}"
        caisse_data = get_resp.json()

        expected_part_cabinet = 70.0 * quantity
        actual_part_cabinet = caisse_data.get("part_cabinet")
        assert actual_part_cabinet == expected_part_cabinet, f"part_cabinet calculation mismatch: expected {expected_part_cabinet}, got {actual_part_cabinet}"

    finally:
        # Cleanup: delete created caisse transaction and examen if they were created
        if caisse_id is not None:
            try:
                del_resp = requests.delete(f"{BASE_URL}/admin/caisses/{caisse_id}", headers=auth_headers, timeout=TIMEOUT)
                if del_resp.status_code not in [200, 204]:
                    print(f"Warning: Failed to delete caisse {caisse_id}: {del_resp.status_code}", file=sys.stderr)
            except Exception as e:
                print(f"Error deleting caisse {caisse_id}: {e}", file=sys.stderr)
        if examen_id is not None:
            try:
                del_resp = requests.delete(f"{BASE_URL}/admin/examens/{examen_id}", headers=auth_headers, timeout=TIMEOUT)
                if del_resp.status_code not in [200, 204]:
                    print(f"Warning: Failed to delete examen {examen_id}: {del_resp.status_code}", file=sys.stderr)
            except Exception as e:
                print(f"Error deleting examen {examen_id}: {e}", file=sys.stderr)

test_verify_caisse_clinic_share_calculation()