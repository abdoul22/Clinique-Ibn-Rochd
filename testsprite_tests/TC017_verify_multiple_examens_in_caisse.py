import requests

BASE_URL = "http://localhost:8000"
AUTH_USERNAME = "abdoullah@gmail.com"
AUTH_PASSWORD = "12345678"
TIMEOUT = 30

def test_verify_multiple_examens_in_caisse():
    session = requests.Session()
    headers = {'Content-Type': 'application/json'}

    # Perform login to obtain authenticated session cookie and CSRF token
    login_url = f"{BASE_URL}/login"

    # Get login page to fetch CSRF token
    resp_login_page = session.get(login_url, timeout=TIMEOUT)
    assert resp_login_page.status_code == 200, f"Failed to get login page: {resp_login_page.status_code}"

    # Extract CSRF token from HTML
    import re
    csrf_token_match = re.search(r'name="_token" value="([^"]+)"', resp_login_page.text)
    assert csrf_token_match, "CSRF token not found on login page"
    csrf_token = csrf_token_match.group(1)

    # Post credentials to login form
    login_payload = {
        '_token': csrf_token,
        'email': AUTH_USERNAME,
        'password': AUTH_PASSWORD
    }
    resp_login = session.post(login_url, data=login_payload, timeout=TIMEOUT, allow_redirects=False)
    # After successful login, status code should be 302 redirect
    assert resp_login.status_code in [302, 303], f"Login failed with status code {resp_login.status_code}"

    # Now session is authenticated

    # Step 1: Create multiple examens to be used in the caisse transaction
    examens_data = []
    created_examens_ids = []
    try:
        # Create two examens with known part_medecin and part_cabinet values for summation testing
        for i in range(2):
            examen_payload = {
                "name": f"Test Examen {i+1}",
                "price": 200 + i*100,  # arbitrary price
                "part_medecin": 50 + i*10,  # doctor share
                "part_cabinet": 30 + i*15,  # clinic share
                "category": "Radiology",
                "code": f"EXAM{i+1}CODE"
            }
            # Endpoint to create examen assumed /admin/examens POST for creation
            create_examen_url = f"{BASE_URL}/admin/examens"
            resp = session.post(create_examen_url, json=examen_payload, headers=headers, timeout=TIMEOUT)
            assert resp.status_code == 201, f"Failed to create examen {i+1}: {resp.text}"
            examen_created = resp.json()
            examen_id = examen_created.get("id")
            assert examen_id is not None, "Created examen missing ID"
            created_examens_ids.append(examen_id)
            examens_data.append({
                "examen_id": examen_id,
                "quantity": 2  # arbitrary quantity to test summing logic
            })

        # Step 2: Create caisse transaction with multiple examens
        caisse_payload = {
            "medecin_id": 1,
            "patient_id": 1,
            "examens_data": examens_data,
            "total": 0,
            "mode_paiement_id": 1
        }
        create_caisse_url = f"{BASE_URL}/admin/caisses"
        resp = session.post(create_caisse_url, json=caisse_payload, headers=headers, timeout=TIMEOUT)
        assert resp.status_code == 201, f"Failed to create caisse transaction: {resp.text}"
        caisse_created = resp.json()
        caisse_id = caisse_created.get("id")
        assert caisse_id is not None, "Created caisse transaction missing ID"

        # Step 3: Validate sums of part_medecin and part_clinique in response
        expected_part_medecin = 0
        expected_part_clinique = 0

        # Fetch each examen details to get part_medecin and part_cabinet
        for examen_entry in examens_data:
            examen_id = examen_entry["examen_id"]
            qty = examen_entry["quantity"]
            examen_detail_url = f"{BASE_URL}/admin/examens/{examen_id}"
            resp_examen = session.get(examen_detail_url, headers=headers, timeout=TIMEOUT)
            assert resp_examen.status_code == 200, f"Failed to fetch examen {examen_id} details: {resp_examen.text}"
            examen_detail = resp_examen.json()
            part_medecin = examen_detail.get("part_medecin", 0)
            part_cabinet = examen_detail.get("part_cabinet", 0)
            expected_part_medecin += part_medecin * qty
            expected_part_clinique += part_cabinet * qty

        actual_part_medecin = caisse_created.get("part_medecin")
        actual_part_clinique = caisse_created.get("part_clinique")

        assert actual_part_medecin is not None, "Caisse response missing part_medecin"
        assert actual_part_clinique is not None, "Caisse response missing part_clinique"

        assert abs(actual_part_medecin - expected_part_medecin) < 0.01, \
            f"part_medecin mismatch: expected {expected_part_medecin}, got {actual_part_medecin}"
        assert abs(actual_part_clinique - expected_part_clinique) < 0.01, \
            f"part_clinique mismatch: expected {expected_part_clinique}, got {actual_part_clinique}"

    finally:
        # Cleanup: delete caisse transaction and created examens
        if 'caisse_id' in locals():
            delete_caisse_url = f"{BASE_URL}/admin/caisses/{caisse_id}"
            session.delete(delete_caisse_url, headers=headers, timeout=TIMEOUT)

        for examen_id in created_examens_ids:
            delete_examen_url = f"{BASE_URL}/admin/examens/{examen_id}"
            session.delete(delete_examen_url, headers=headers, timeout=TIMEOUT)

test_verify_multiple_examens_in_caisse()