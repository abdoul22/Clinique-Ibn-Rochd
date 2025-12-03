import requests
from requests.auth import HTTPBasicAuth

base_endpoint = "http://localhost:8000"
auth = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
timeout = 30

def test_verify_modepaiement_creation():
    headers = {
        "Content-Type": "application/json"
    }

    # Payment types to test
    payment_types = ["espèces", "bankily", "masrivi", "sedad"]

    created_modepaiement_ids = []

    try:
        for paiement_type in payment_types:
            # Create a new ModePaiement entry
            payload = {
                "nom": paiement_type,
                "description": f"Test payment type {paiement_type}"
            }
            response = requests.post(
                f"{base_endpoint}/api/modepaiements",
                auth=auth,
                headers=headers,
                json=payload,
                timeout=timeout
            )
            assert response.status_code == 201, f"Failed to create ModePaiement for {paiement_type}"
            created_entry = response.json()
            assert "id" in created_entry, "Created ModePaiement response missing ID"
            modepaiement_id = created_entry["id"]
            created_modepaiement_ids.append(modepaiement_id)

            # Retrieve the created ModePaiement to verify stored data
            get_response = requests.get(
                f"{base_endpoint}/api/modepaiements/{modepaiement_id}",
                auth=auth,
                headers=headers,
                timeout=timeout
            )
            assert get_response.status_code == 200, f"Failed to retrieve ModePaiement with ID {modepaiement_id}"
            data = get_response.json()
            assert data.get("nom") == paiement_type, f"Payment type mismatch for ID {modepaiement_id}"
            assert data.get("description") == f"Test payment type {paiement_type}", f"Description mismatch for ID {modepaiement_id}"

    finally:
        # Cleanup: delete created ModePaiement entries
        for modepaiement_id in created_modepaiement_ids:
            del_response = requests.delete(
                f"{base_endpoint}/api/modepaiements/{modepaiement_id}",
                auth=auth,
                headers=headers,
                timeout=timeout
            )
            # Allow 200 or 204 for successful deletion
            assert del_response.status_code in [200, 204], f"Failed to delete ModePaiement with ID {modepaiement_id}"

test_verify_modepaiement_creation()