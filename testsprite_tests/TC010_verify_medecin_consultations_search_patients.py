import requests

base_url = "http://localhost:8000"

login_endpoint = "/login"
search_endpoint = "/medecin/consultations/search-patients"
timeout = 30

# Correct login payload as per Laravel Auth standard: email and password
login_payload = {
    "email": "abdoullah@gmail.com",
    "password": "12345678"
}

headers = {
    "Accept": "application/json"
}

def test_verify_medecin_consultations_search_patients():
    try:
        # Step 1: Login to get auth token
        login_response = requests.post(
            base_url + login_endpoint,
            json=login_payload,
            headers=headers,
            timeout=timeout
        )
        login_response.raise_for_status()
        login_data = login_response.json()

        # Assume token is in login_data['token'] or login_data['access_token']
        token = login_data.get('token') or login_data.get('access_token')
        assert token, "Authentication token not found in login response"

        # Prepare Authorization header with Bearer token
        auth_headers = headers.copy()
        auth_headers["Authorization"] = f"Bearer {token}"

        # Example search parameters
        params = {"q": "John"}

        # Step 2: Call search patients
        response = requests.get(
            base_url + search_endpoint,
            headers=auth_headers,
            params=params,
            timeout=timeout
        )
        response.raise_for_status()

        data = response.json()

        assert isinstance(data, (list, dict)), "Response JSON is not a list or dict"

        patients = data if isinstance(data, list) else data.get("patients", [])
        assert isinstance(patients, list), "Patients data is not a list"

        assert any("John" in (p.get("name") or "") or "John" in (p.get("prenom") or "") for p in patients), \
            "No patient matching search string found in response"

    except requests.exceptions.RequestException as e:
        assert False, f"HTTP Request failed: {e}"


test_verify_medecin_consultations_search_patients()
