import requests


def test_verify_lits_api_disponibles():
    base_url = "http://localhost:8000"
    login_url = base_url + "/login"
    endpoint = "/lits-api/disponibles"
    lits_url = base_url + endpoint

    login_payload = {
        'email': 'abdoullah@gmail.com',
        'password': '12345678'
    }

    headers = {
        "Accept": "application/json"
    }

    session = requests.Session()

    # First, login to get authenticated session
    try:
        login_response = session.post(login_url, data=login_payload, headers=headers, timeout=30)
        login_response.raise_for_status()
    except requests.RequestException as e:
        assert False, f"Login request to {login_url} failed: {e}"

    # Check if login was successful by status code and content
    assert login_response.status_code == 200, f"Expected 200 OK from login but got {login_response.status_code}"

    # Now, make the authenticated request to lits-api/disponibles
    try:
        response = session.get(lits_url, headers=headers, timeout=30)
        response.raise_for_status()
    except requests.RequestException as e:
        assert False, f"Request to {lits_url} failed: {e}"

    # Validate response code
    assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"

    # Validate response content type
    content_type = response.headers.get("Content-Type", "")
    assert "application/json" in content_type, f"Expected JSON response but got Content-Type: {content_type}"

    # Validate response JSON structure and data (assuming it returns a list or dict with availability info)
    try:
        data = response.json()
    except ValueError:
        assert False, "Response is not valid JSON"

    # Check that data is not empty and contains expected keys or structure
    # Assuming the API returns a list of beds with availability status
    assert isinstance(data, (list, dict)), "Response JSON should be a list or dict"
    if isinstance(data, list):
        assert len(data) > 0, "Expected at least one bed availability record"
        expected_keys = {"id", "available"}
        for item in data:
            assert isinstance(item, dict), "Each item in response list should be a dict"
            assert expected_keys.issubset(item.keys()), f"Expected keys {expected_keys} in item, got {item.keys()}"
            assert isinstance(item["available"], (bool, int)), "'available' key should be boolean or integer"
    elif isinstance(data, dict):
        assert "beds" in data, "Response dict should contain 'beds' key"
        beds = data["beds"]
        assert isinstance(beds, list), "'beds' key should be a list"
        assert len(beds) > 0, "Beds list should not be empty"
        for bed in beds:
            assert isinstance(bed, dict), "Each bed should be a dict"
            expected_keys = {"id", "available"}
            assert expected_keys.issubset(bed.keys()), f"Expected keys {expected_keys} in bed, got {bed.keys()}"
            assert isinstance(bed["available"], (bool, int)), "'available' key should be boolean or integer"
    else:
        assert False, "Unexpected JSON structure"


test_verify_lits_api_disponibles()
