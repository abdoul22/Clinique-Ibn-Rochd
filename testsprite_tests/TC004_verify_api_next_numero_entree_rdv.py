import requests

def test_verify_api_next_numero_entree_rdv():
    base_url = "http://localhost:8000"
    endpoint = "/api/next-numero-entree-rdv"
    url = base_url + endpoint

    headers = {
        "Accept": "application/json"
    }

    try:
        response = requests.get(url, headers=headers, timeout=30)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request to {url} failed: {e}"

    # Validate response content-type
    content_type = response.headers.get("Content-Type", "")
    assert "application/json" in content_type, f"Unexpected Content-Type {content_type}"

    data = response.json()
    # Validate that the response contains a key with the next unique number
    assert isinstance(data, dict), "Response JSON is not an object"
    possible_keys = ["next_numero_entree_rdv", "next_numero", "numero_entree_rdv", "nextNumber", "next"]
    key_found = None
    for key in possible_keys:
        if key in data:
            key_found = key
            break
    
    assert key_found is not None, f"Response JSON does not contain expected keys {possible_keys}"
    next_number = data[key_found]

    assert isinstance(next_number, int), f"Next number is not an integer: {next_number}"
    assert next_number > 0, f"Next number is not positive: {next_number}"


test_verify_api_next_numero_entree_rdv()
