import requests

BASE_URL = "http://localhost:8000"
AUTH = ("abdoullah@gmail.com", "12345678")
HEADERS = {"Content-Type": "application/json"}
TIMEOUT = 30

def test_verify_credit_creation_for_assurance():
    caisse_url = f"{BASE_URL}/api/caisses"
    credits_url = f"{BASE_URL}/credits"
    assurances_url = f"{BASE_URL}/assurances"

    # Step 1: Create a test insurance (assurance) to be referenced in the caisse transaction
    insurance_payload = {
        "nom": "Test Assurance",
        "couverture": 20  # 20% coverage
    }

    insurance_id = None
    caisse_id = None
    credit_id = None
    try:
        resp_assurance = requests.post(assurances_url, json=insurance_payload, auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
        assert resp_assurance.status_code in (200, 201), f"Failed to create insurance, status {resp_assurance.status_code}"
        insurance_data = resp_assurance.json()
        insurance_id = insurance_data.get("id")
        assert insurance_id is not None, "Insurance ID missing in response"

        # Step 2: Create a caisse transaction including the insurance
        # Assuming minimal required fields and a 'total' field for amount
        # Example total: 1000 for easier calculation (coverage 20% -> 200 credit)
        caisse_payload = {
            "patient_id": 1,       # Assuming patient with ID 1 exists for test
            "medecin_id": 1,       # Assuming doctor with ID 1 exists for test
            "total": 1000,
            "assurance_id": insurance_id,
            "description": "Test transaction with insurance coverage"
        }

        resp_caisse = requests.post(caisse_url, json=caisse_payload, auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
        assert resp_caisse.status_code == 201, f"Failed to create caisse transaction, status {resp_caisse.status_code}"
        caisse_data = resp_caisse.json()
        caisse_id = caisse_data.get("id")
        total = caisse_data.get("total")
        assert caisse_id is not None, "Caisse transaction ID missing in response"

        # Step 3: Verify a Credit was created for the Assurance with correct montant calculation
        # Credit amount = total * couverture / 100
        expected_credit_montant = total * insurance_payload["couverture"] / 100

        # Search credits linked to this insurance
        resp_credits = requests.get(credits_url, auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
        assert resp_credits.status_code == 200, f"Failed to get credits, status {resp_credits.status_code}"
        credits_list = resp_credits.json()

        # Filter credits for this insurance and caisse transaction
        related_credits = [
            c for c in credits_list
            if c.get("assurance_id") == insurance_id and c.get("caisse_id") == caisse_id
        ]
        assert len(related_credits) > 0, "No credit created for the insurance linked to caisse"

        # Check montant correctness in first related Credit
        credit = related_credits[0]
        credit_id = credit.get("id")
        montant = credit.get("montant")
        assert montant is not None, "Credit montant missing"
        # Assert montant is approximately equal (float tolerance)
        assert abs(montant - expected_credit_montant) < 0.01, (
            f"Credit montant incorrect: expected {expected_credit_montant}, got {montant}"
        )

    finally:
        # Cleanup: Delete created Credit
        if credit_id is not None:
            try:
                requests.delete(f"{credits_url}/{credit_id}", auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
            except Exception:
                pass
        # Cleanup: Delete created caisse transaction
        if caisse_id is not None:
            try:
                requests.delete(f"{caisse_url}/{caisse_id}", auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
            except Exception:
                pass
        # Cleanup: Delete created insurance
        if insurance_id is not None:
            try:
                requests.delete(f"{assurances_url}/{insurance_id}", auth=AUTH, headers=HEADERS, timeout=TIMEOUT)
            except Exception:
                pass

test_verify_credit_creation_for_assurance()
