import requests
from requests.auth import HTTPBasicAuth

def test_verify_chambres_api_disponibles():
    base_url = "http://localhost:8000"
    endpoint = "/chambres-api/disponibles"
    url = base_url + endpoint

    auth = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
    headers = {
        "Accept": "application/json"
    }

    try:
        response = requests.get(url, headers=headers, auth=auth, timeout=30)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request to {url} failed: {e}"

    try:
        data = response.json()
    except ValueError:
        assert False, f"Response is not valid JSON: {response.text}"

    assert isinstance(data, list), f"Expected response to be a list but got {type(data)}"
    for item in data:
        assert isinstance(item, dict), f"Expected each item to be a dict but got {type(item)}"
        assert "id" in item, "Expected 'id' field in chambre item."
        if "disponible" in item:
            assert isinstance(item["disponible"], bool), "'disponible' field should be bool."
        if "status" in item:
            assert item["status"] in ["available", "occupied", "maintenance", "reserved"], (
                f"Unexpected status value: {item['status']}"
            )

test_verify_chambres_api_disponibles()