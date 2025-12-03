import requests

BASE_URL = "http://localhost:8000"
HEADERS = {
    "Authorization": "Basic YWJkb3VsbGFoQGdtYWlsLmNvbToxMjM0NTY3OA==",
    "Content-Type": "application/json",
    "Accept": "application/json"
}
TIMEOUT = 30

def test_verify_caisse_insurance_coverage():
    # Step 1: Create a test insurance company (Assurance)
    assurance_payload = {
        "nom": "Test Assurance",
        "couverture": 20  # 20% coverage
    }
    assurance_resp = requests.post(
        f"{BASE_URL}/assurances",
        json=assurance_payload,
        headers=HEADERS,
        timeout=TIMEOUT,
    )
    assert assurance_resp.status_code == 201
    assurance = assurance_resp.json()
    assurance_id = assurance.get("id")
    assert assurance_id is not None

    # Step 2: Create a caisse transaction with a total and assurance_id
    # We will create this caisse with total = 1000 and assurance coverage 20%
    caisse_payload = {
        "date": "2025-11-19",
        "total": 1000,
        "assurance_id": assurance_id,
        "description": "Test caisse transaction with insurance coverage"
    }

    caisse_resp = requests.post(
        f"{BASE_URL}/caisses",
        json=caisse_payload,
        headers=HEADERS,
        timeout=TIMEOUT,
    )
    assert caisse_resp.status_code == 201
    caisse = caisse_resp.json()
    caisse_id = caisse.get("id")
    assert caisse_id is not None

    try:
        # Step 3: Get detailed caisse record to verify recette calculation
        get_caisse_resp = requests.get(
            f"{BASE_URL}/caisses/{caisse_id}",
            headers=HEADERS,
            timeout=TIMEOUT,
        )
        assert get_caisse_resp.status_code == 200
        caisse_detail = get_caisse_resp.json()

        total = float(caisse_detail.get("total", 0))
        recette = float(caisse_detail.get("recette", -1))
        couverture = float(assurance.get("couverture", 0))

        # recette should be total - (total * couverture / 100)
        expected_recette = total - (total * couverture / 100)

        assert abs(recette - expected_recette) < 0.01, (
            f"Expected recette {expected_recette}, got {recette}")

        # Step 4: Verify that a Credit was created for the insurance company
        # We fetch credits filtered by assurance_id and caisse_id to find a matching credit
        credits_resp = requests.get(
            f"{BASE_URL}/credits?assurance_id={assurance_id}&caisse_id={caisse_id}",
            headers=HEADERS,
            timeout=TIMEOUT,
        )
        assert credits_resp.status_code == 200
        credits = credits_resp.json()
        assert isinstance(credits, list)
        assert len(credits) > 0, "No credit created for the insurance company."

        # Check montant of credit equals total * couverture / 100
        credit_match_found = False
        expected_credit_montant = total * couverture / 100
        for credit in credits:
            montant = float(credit.get("montant", -1))
            # Accept small floating point tolerance
            if abs(montant - expected_credit_montant) < 0.01:
                credit_match_found = True
                break
        assert credit_match_found, "Credit montant does not match insurance coverage calculation."

    finally:
        # Cleanup: Delete the created caisse transaction
        if caisse_id is not None:
            requests.delete(
                f"{BASE_URL}/caisses/{caisse_id}",
                headers=HEADERS,
                timeout=TIMEOUT,
            )
        # Cleanup: Delete the created insurance company
        if assurance_id is not None:
            requests.delete(
                f"{BASE_URL}/assurances/{assurance_id}",
                headers=HEADERS,
                timeout=TIMEOUT,
            )

test_verify_caisse_insurance_coverage()