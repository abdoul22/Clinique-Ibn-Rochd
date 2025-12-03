import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
AUTH = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
HEADERS = {"Content-Type": "application/json"}
TIMEOUT = 30

def test_verify_etatcaisse_recette_tracking():
    caisse_url = f"{BASE_URL}/admin/caisses"
    etatcaisse_url = f"{BASE_URL}/admin/etatcaisse"  # Assuming an admin route to get EtatCaisse entries

    # Create a caisse transaction payload with insurance coverage applied
    # To test total - insurance coverage = recette, include assurance_id and coverage
    caisse_payload = {
        "patient_id": 1,           # Must exist or can be dynamically created - using 1 as default for test scope
        "medecin_id": 1,           # Must exist or can be dynamically created - using 1 as default
        "total": 1000.0,
        "assurance_id": 1,         # Insurance present
        "couverture": 30,          # 30% insurance coverage
        "mode_paiement_id": 1,     # Existing payment mode id
        "examens_data": [
            {"examen_id": 1, "quantity": 1}
        ],
        "description": "Test caisse transaction with insurance coverage"
    }

    def create_caisse_transaction():
        try:
            response = requests.post(caisse_url, json=caisse_payload, auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
            assert response.status_code == 201, f"Unexpected status code when creating caisse: {response.status_code}"
            assert response.text.strip() != "", "Empty response received when creating caisse."
            return response.json()
        except Exception as e:
            raise AssertionError(f"Failed to create caisse transaction: {e}")

    def get_etatcaisse_for_caisse(caisse_id):
        # Assuming an API to fetch EtatCaisse by caisse_id or filter by caisse_id
        params = {"caisse_id": caisse_id}
        try:
            resp = requests.get(etatcaisse_url, auth=AUTH, headers=HEADERS, params=params, timeout=TIMEOUT)
            assert resp.status_code == 200, f"Unexpected status code when getting EtatCaisse: {resp.status_code}"
            assert resp.text.strip() != "", "Empty response received when getting EtatCaisse."
            data = resp.json()
            # Assuming 1-to-1 mapping for caisse to etatcaisse entry
            if isinstance(data, list):
                for entry in data:
                    if entry.get("caisse_id") == caisse_id:
                        return entry
                raise AssertionError("EtatCaisse entry for the caisse_id not found")
            elif isinstance(data, dict):
                return data
            else:
                raise AssertionError("Unexpected response format for EtatCaisse")
        except Exception as e:
            raise AssertionError(f"Failed to get EtatCaisse data: {e}")

    caisse = None
    try:
        caisse = create_caisse_transaction()
        assert "id" in caisse, "Caisse transaction creation response missing 'id'."
        caisse_id = caisse["id"]

        etatcaisse_entry = get_etatcaisse_for_caisse(caisse_id)

        assert "recette" in etatcaisse_entry, "EtatCaisse entry missing 'recette'."
        assert "total" in etatcaisse_entry, "EtatCaisse entry missing 'total'."
        assert "couverture" in etatcaisse_entry, "EtatCaisse entry missing 'couverture'."

        total = float(etatcaisse_entry["total"])
        couverture = float(etatcaisse_entry.get("couverture", 0))
        recette = float(etatcaisse_entry["recette"])

        expected_recette = total * (1 - couverture / 100)

        # Allow minor floating rounding difference
        assert abs(recette - expected_recette) < 0.01, (
            f"Recette {recette} does not equal total minus coverage ({expected_recette})."
        )

    finally:
        # Clean up by deleting the created caisse transaction if it was created
        if caisse and "id" in caisse:
            delete_url = f"{caisse_url}/{caisse['id']}"
            try:
                del_resp = requests.delete(delete_url, auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
                del_resp.raise_for_status()
            except Exception:
                pass

test_verify_etatcaisse_recette_tracking()
