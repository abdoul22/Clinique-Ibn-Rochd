import requests
from requests.auth import HTTPBasicAuth

def test_verify_api_next_numero_entree():
    base_url = "http://localhost:8000"
    endpoint = "/api/next-numero-entree"
    url = f"{base_url}{endpoint}"
    auth = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
    headers = {
        "Accept": "application/json"
    }
    timeout = 30

    try:
        response = requests.get(url, auth=auth, headers=headers, timeout=timeout)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request failed: {e}"

    # Validate status code
    assert response.status_code == 200, f"Expected status code 200, got {response.status_code}"

    # Validate content type
    content_type = response.headers.get("Content-Type", "")
    assert "application/json" in content_type, f"Expected JSON response, got {content_type}"

    # Validate response content
    try:
        data = response.json()
    except ValueError:
        assert False, "Response is not valid JSON"

    assert isinstance(data, dict), "Response JSON is not a dictionary/object"

    possible_keys = ["next_numero_entree", "nextNumeroEntree", "numero_entree", "numeroEntree", "next_number", "next"]
    found_key = None
    for key in possible_keys:
        if key in data:
            found_key = key
            break
    assert found_key is not None, f"Response JSON does not contain any expected key among {possible_keys}"

    next_num = data[found_key]

    assert isinstance(next_num, (int, str)), f"Next numero entree value should be int or str, got {type(next_num)}"

    if isinstance(next_num, str):
        assert next_num.isdigit(), f"Next numero entree string is not numeric: {next_num}"

test_verify_api_next_numero_entree()