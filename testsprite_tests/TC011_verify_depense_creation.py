import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000/api"
TIMEOUT = 30
AUTH = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
HEADERS = {"Content-Type": "application/json"}


def test_verify_depense_creation():
    depense_id = None
    try:
        # Use an existing modepaiement_id since creation endpoint does not exist
        modepaiement_id = 1  # Assumed existing payment mode ID

        # Create a Depense linked to the existing ModePaiement
        depense_payload = {
            "montant": 150.50,
            "description": "Test depense with payment mode",
            "modepaiement_id": modepaiement_id,
            "date_depense": "2025-11-19"
        }
        depense_response = requests.post(
            f"{BASE_URL}/depenses",
            json=depense_payload,
            headers=HEADERS,
            auth=AUTH,
            timeout=TIMEOUT,
        )
        assert depense_response.status_code == 201, f"Failed to create Depense: {depense_response.text}"
        depense_data = depense_response.json()
        depense_id = depense_data.get("id")
        assert depense_id is not None, "Depense creation response missing 'id'"

        # Retrieve the created Depense and verify
        get_depense_response = requests.get(
            f"{BASE_URL}/depenses/{depense_id}",
            headers=HEADERS,
            auth=AUTH,
            timeout=TIMEOUT,
        )
        assert get_depense_response.status_code == 200, f"Failed to get Depense: {get_depense_response.text}"
        retrieved_depense = get_depense_response.json()

        # Validate fields
        assert abs(float(retrieved_depense.get("montant", 0)) - 150.50) < 0.01, "Depense montant does not match"
        assert retrieved_depense.get("description") == "Test depense with payment mode", "Depense description mismatch"
        assert str(retrieved_depense.get("modepaiement_id")) == str(modepaiement_id), "Depense modepaiement_id mismatch"

    finally:
        # Cleanup: Delete created Depense if it exists
        if depense_id:
            requests.delete(
                f"{BASE_URL}/depenses/{depense_id}",
                headers=HEADERS,
                auth=AUTH,
                timeout=TIMEOUT,
            )


test_verify_depense_creation()
