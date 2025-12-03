import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
AUTH = HTTPBasicAuth('abdoullah@gmail.com', '12345678')
HEADERS = {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
}
TIMEOUT = 30

def test_verify_depense_part_medecin_source():
    depense_id = None
    try:
        # Step 1: Create a depense with source 'part_medecin'
        depense_payload = {
            "montant": 150.0,
            "description": "Paiement part médecin test",
            "source": "part_medecin",
            "mode_paiement_id": None  # Assuming mode_paiement is optional or we can fetch one
        }

        # Fetch existing ModePaiement with source 'part_medecin' or any valid ModePaiement for linking
        mode_paiement_resp = requests.get(f"{BASE_URL}/modepaiements", auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
        if mode_paiement_resp.status_code == 200:
            mp_items = mode_paiement_resp.json()
            if isinstance(mp_items, list) and len(mp_items) > 0:
                part_medecin_mode = next((mp for mp in mp_items if mp.get("source") == "part_medecin"), None)
                if part_medecin_mode:
                    depense_payload["mode_paiement_id"] = part_medecin_mode.get("id")
                else:
                    # fallback assign first mode_paiement id if available
                    depense_payload["mode_paiement_id"] = mp_items[0].get("id")

        # Create the depense
        resp_create = requests.post(f"{BASE_URL}/depenses", json=depense_payload, auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
        assert resp_create.status_code == 201, f"Failed to create depense, status: {resp_create.status_code}, response: {resp_create.text}"
        depense_data = resp_create.json()
        depense_id = depense_data.get("id")
        assert depense_id is not None, "Created depense does not have an ID"

        # Step 2: Fetch depenses filtered by source=part_medecin
        resp_filter = requests.get(f"{BASE_URL}/depenses", params={"source": "part_medecin"}, auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
        assert resp_filter.status_code == 200, f"Failed to get filtered depenses, status: {resp_filter.status_code}, response: {resp_filter.text}"
        depenses = resp_filter.json()
        assert isinstance(depenses, list), "Expected a list of depenses"

        # Step 3: Verify the created depense is in the filtered list and all have source 'part_medecin'
        filtered_ids = [dep.get("id") for dep in depenses if dep.get("source") == "part_medecin"]
        assert depense_id in filtered_ids, "Created depense with source 'part_medecin' not found in filtered depenses"
        for dep in depenses:
            assert dep.get("source") == "part_medecin", f"Found depense with source != 'part_medecin': {dep}"

    finally:
        # Cleanup: Delete the created depense if exists
        if depense_id:
            requests.delete(f"{BASE_URL}/depenses/{depense_id}", auth=AUTH, headers=HEADERS, timeout=TIMEOUT)

test_verify_depense_part_medecin_source()
