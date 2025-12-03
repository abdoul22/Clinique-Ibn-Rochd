import requests
import json

BASE_URL = "http://localhost:8000"
AUTH = ("abdoullah@gmail.com", "12345678")
HEADERS = {"Content-Type": "application/json"}

def test_verify_credit_payment_processing():
    credit_id = None
    try:
        # Step 1: Create a Credit to test payment processing
        # Create a caisse transaction with insurance to generate a Credit or create Credit directly if API allows
        
        # For test simplicity, create a credit resource directly if API supports POST /credits
        credit_payload = {
            "patient_id": 1,      # assuming patient 1 exists; else create patient then use its id
            "assurance_id": 1,    # assuming assurance 1 exists
            "total": 1000,
            "montant_paye": 0,
            "status": "en attente"  # assumed initial status "pending"
        }
        credit_create_resp = requests.post(
            f"{BASE_URL}/credits",
            auth=AUTH,
            headers=HEADERS,
            data=json.dumps(credit_payload),
            timeout=30
        )
        assert credit_create_resp.status_code in (200, 201), f"Expected 200 or 201 Created, got {credit_create_resp.status_code}"
        credit_data = credit_create_resp.json()
        credit_id = credit_data.get("id")
        assert credit_id is not None, "Credit ID not returned on creation"
        assert credit_data.get("montant_paye") == 0
        assert credit_data.get("status") == "en attente" or credit_data.get("status") == "pending"
        
        # Step 2: Make a payment request on the credit to pay some amount
        pay_amount = 600
        payer_payload = {"montant": pay_amount}
        payer_resp = requests.post(
            f"{BASE_URL}/credits/{credit_id}/payer",
            auth=AUTH,
            headers=HEADERS,
            data=json.dumps(payer_payload),
            timeout=30
        )
        assert payer_resp.status_code in (200, 201), f"Expected 200 or 201 on payment, got {payer_resp.status_code}"
        payer_data = payer_resp.json()
        
        # Validate montant_paye updated properly and status changed accordingly
        montant_paye = payer_data.get("montant_paye")
        assert montant_paye is not None, "montant_paye missing in payment response"
        assert montant_paye == pay_amount, f"montant_paye expected {pay_amount}, got {montant_paye}"
        
        status = payer_data.get("status")
        assert status in ("partiel", "en cours", "pending"), f"Unexpected status after partial payment: {status}"
        
        # Step 3: Pay the remaining amount to complete credit payment and check status changes
        remaining_amount = credit_data["total"] - montant_paye
        if remaining_amount > 0:
            payer_resp_2 = requests.post(
                f"{BASE_URL}/credits/{credit_id}/payer",
                auth=AUTH,
                headers=HEADERS,
                data=json.dumps({"montant": remaining_amount}),
                timeout=30
            )
            assert payer_resp_2.status_code in (200, 201), f"Expected 200 or 201 on final payment, got {payer_resp_2.status_code}"
            payer_data_2 = payer_resp_2.json()
            assert payer_data_2.get("montant_paye") == credit_data["total"], f"montant_paye expected {credit_data['total']}, got {payer_data_2.get('montant_paye')}"
            assert payer_data_2.get("status") == "paid" or payer_data_2.get("status") == "termine", f"Expected status 'paid' or 'termine', got {payer_data_2.get('status')}"
        
    finally:
        # Clean up: Delete the created credit if possible
        if credit_id:
            requests.delete(
                f"{BASE_URL}/credits/{credit_id}",
                auth=AUTH,
                headers=HEADERS,
                timeout=30
            )

test_verify_credit_payment_processing()
