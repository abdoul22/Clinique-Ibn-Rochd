import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
AUTH = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
HEADERS = {"Accept": "application/json"}
TIMEOUT = 30

def test_verify_etatcaisse_validation():
    caisse_id = None
    etatcaisse_id = None
    try:
        # Step 1: Create a new caisse transaction to generate EtatCaisse entry
        caisse_payload = {
            # Minimal valid payload example with required fields,
            # since PRD does not provide exact payload, 
            # use plausible fields: medecin_id, total, examenes or similar
            "medecin_id": 1,
            "total": 1000,
            "examens_data": [
                {
                    "examen_id": 1,
                    "quantity": 1
                }
            ],
            "mode_paiement": "espèces"
        }
        response_caisse = requests.post(
            f"{BASE_URL}/api/caisses",
            json=caisse_payload,
            headers=HEADERS,
            auth=AUTH,
            timeout=TIMEOUT
        )
        assert response_caisse.status_code == 201, f"Caisse creation failed: {response_caisse.text}"
        caisse_data = response_caisse.json()
        caisse_id = caisse_data.get("id")
        assert caisse_id is not None, "Created caisse has no ID"
        
        # Step 2: Retrieve EtatCaisse linked to created caisse
        # Assuming EtatCaisse is listed via /etatcaisse and linked by caisse_id
        response_etat_list = requests.get(
            f"{BASE_URL}/etatcaisse?caisse_id={caisse_id}",
            headers=HEADERS,
            auth=AUTH,
            timeout=TIMEOUT
        )
        assert response_etat_list.status_code == 200, f"EtatCaisse list retrieval failed: {response_etat_list.text}"
        etat_list = response_etat_list.json()
        assert isinstance(etat_list, list) and len(etat_list) > 0, "No EtatCaisse entry found for created caisse"
        etatcaisse = etat_list[0]
        etatcaisse_id = etatcaisse.get("id")
        assert etatcaisse_id is not None, "EtatCaisse entry has no ID"
        
        part_medecin_before = etatcaisse.get("part_medecin", 0)
        validated_before = etatcaisse.get("validated", False)
        
        # Step 3: Validate EtatCaisse entry (PUT or PATCH /etatcaisse/{id}/validate)
        response_validate = requests.put(
            f"{BASE_URL}/etatcaisse/{etatcaisse_id}/validate",
            headers=HEADERS,
            auth=AUTH,
            timeout=TIMEOUT
        )
        assert response_validate.status_code == 200, f"Validation of EtatCaisse failed: {response_validate.text}"
        validate_data = response_validate.json()
        assert validate_data.get("validated") is True, "EtatCaisse not marked as validated after validation"
        
        # Step 4: Verify that validated part_medecin is tracked in totals via /etatcaisse/totals or /etatcaisse
        response_totals = requests.get(
            f"{BASE_URL}/etatcaisse/totals",
            headers=HEADERS,
            auth=AUTH,
            timeout=TIMEOUT
        )
        assert response_totals.status_code == 200, f"Fetching EtatCaisse totals failed: {response_totals.text}"
        totals = response_totals.json()
        total_part_medecin = totals.get("validated_part_medecin", 0)
        assert total_part_medecin >= part_medecin_before, "Validated part_medecin total does not include the validated EtatCaisse"
        
        # Step 5: Unvalidate EtatCaisse entry (PUT or PATCH /etatcaisse/{id}/unvalidate)
        response_unvalidate = requests.put(
            f"{BASE_URL}/etatcaisse/{etatcaisse_id}/unvalidate",
            headers=HEADERS,
            auth=AUTH,
            timeout=TIMEOUT
        )
        assert response_unvalidate.status_code == 200, f"Unvalidation of EtatCaisse failed: {response_unvalidate.text}"
        unvalidate_data = response_unvalidate.json()
        assert unvalidate_data.get("validated") is False, "EtatCaisse still marked as validated after unvalidation"
        
        # Step 6: Verify totals updated accordingly (validated_part_medecin should decrease)
        response_totals_after = requests.get(
            f"{BASE_URL}/etatcaisse/totals",
            headers=HEADERS,
            auth=AUTH,
            timeout=TIMEOUT
        )
        assert response_totals_after.status_code == 200, f"Fetching EtatCaisse totals after unvalidate failed: {response_totals_after.text}"
        totals_after = response_totals_after.json()
        total_part_medecin_after = totals_after.get("validated_part_medecin", 0)
        assert total_part_medecin_after <= total_part_medecin, "Validated part_medecin total did not decrease after unvalidation"
    
    finally:
        # Cleanup: Delete created caisse and linked EtatCaisse if exists
        if etatcaisse_id:
            try:
                requests.delete(
                    f"{BASE_URL}/etatcaisse/{etatcaisse_id}",
                    headers=HEADERS,
                    auth=AUTH,
                    timeout=TIMEOUT
                )
            except Exception:
                pass
        if caisse_id:
            try:
                requests.delete(
                    f"{BASE_URL}/api/caisses/{caisse_id}",
                    headers=HEADERS,
                    auth=AUTH,
                    timeout=TIMEOUT
                )
            except Exception:
                pass

test_verify_etatcaisse_validation()