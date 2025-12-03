import requests

BASE_URL = "http://localhost:8000"
TIMEOUT = 30

HEADERS = {
    "Content-Type": "application/json",
    "Accept": "application/json"
}


def login_get_token(email, password):
    payload = {
        "email": email,
        "password": password
    }
    resp = requests.post(f"{BASE_URL}/login", json=payload, headers=HEADERS, timeout=TIMEOUT)
    assert resp.status_code == 200, f"Login failed: {resp.text}"
    data = resp.json()
    # Assuming the token is returned under 'token' or 'access_token'
    token = data.get('token') or data.get('access_token')
    assert token, "No token found in login response"
    return token


def test_verify_modepaiement_source_tracking():
    token = login_get_token("abdoullah@gmail.com", "12345678")
    auth_headers = HEADERS.copy()
    auth_headers["Authorization"] = f"Bearer {token}"

    created_modepaiement_ids = []

    try:
        # 1. Create ModePaiement with source "facture"
        facture_payload = {
            "montant": 1000,
            "type": "espèces",
            "source": "facture",
            "description": "Paiement facture test"
        }
        resp_facture = requests.post(
            f"{BASE_URL}/modepaiements",
            json=facture_payload,
            headers=auth_headers,
            timeout=TIMEOUT
        )
        assert resp_facture.status_code == 201, f"Failed to create facture source ModePaiement: {resp_facture.text}"
        facture_data = resp_facture.json()
        created_modepaiement_ids.append(facture_data["id"])

        # 2. Create ModePaiement with source "depense"
        depense_payload = {
            "montant": 500,
            "type": "bankily",
            "source": "depense",
            "description": "Paiement dépense test"
        }
        resp_depense = requests.post(
            f"{BASE_URL}/modepaiements",
            json=depense_payload,
            headers=auth_headers,
            timeout=TIMEOUT
        )
        assert resp_depense.status_code == 201, f"Failed to create depense source ModePaiement: {resp_depense.text}"
        depense_data = resp_depense.json()
        created_modepaiement_ids.append(depense_data["id"])

        # 3. Create ModePaiement with source "part_medecin"
        part_medecin_payload = {
            "montant": 300,
            "type": "masrivi",
            "source": "part_medecin",
            "description": "Paiement part medecin test"
        }
        resp_part_medecin = requests.post(
            f"{BASE_URL}/modepaiements",
            json=part_medecin_payload,
            headers=auth_headers,
            timeout=TIMEOUT
        )
        assert resp_part_medecin.status_code == 201, f"Failed to create part_medecin source ModePaiement: {resp_part_medecin.text}"
        part_medecin_data = resp_part_medecin.json()
        created_modepaiement_ids.append(part_medecin_data["id"])

        # 4. Create ModePaiement with source "credit_assurance"
        credit_assurance_payload = {
            "montant": 800,
            "type": "sedad",
            "source": "credit_assurance",
            "description": "Paiement credit assurance test"
        }
        resp_credit_assurance = requests.post(
            f"{BASE_URL}/modepaiements",
            json=credit_assurance_payload,
            headers=auth_headers,
            timeout=TIMEOUT
        )
        assert resp_credit_assurance.status_code == 201, f"Failed to create credit_assurance source ModePaiement: {resp_credit_assurance.text}"
        credit_assurance_data = resp_credit_assurance.json()
        created_modepaiement_ids.append(credit_assurance_data["id"])

        # Verify all created IDs are unique and present
        ids_set = set(created_modepaiement_ids)
        assert len(ids_set) == 4, "Not all ModePaiement entries created successfully with unique IDs."

        # Now test filtering by source
        for source in ["facture", "depense", "part_medecin", "credit_assurance"]:
            resp_filter = requests.get(
                f"{BASE_URL}/modepaiements",
                headers=auth_headers,
                timeout=TIMEOUT,
                params={"source": source}
            )
            assert resp_filter.status_code == 200, f"Failed to get ModePaiement filtered by source {source}: {resp_filter.text}"
            list_data = resp_filter.json()
            # The list should contain at least one entry with the source requested
            assert any(mp.get("source") == source for mp in list_data), f"No ModePaiement found for source {source}"

    finally:
        # Cleanup: delete created ModePaiement entries
        for mp_id in created_modepaiement_ids:
            try:
                del_resp = requests.delete(
                    f"{BASE_URL}/modepaiements/{mp_id}",
                    headers=auth_headers,
                    timeout=TIMEOUT
                )
                assert del_resp.status_code in (200, 204), f"Failed to delete ModePaiement id {mp_id}: {del_resp.text}"
            except Exception:
                pass


test_verify_modepaiement_source_tracking()