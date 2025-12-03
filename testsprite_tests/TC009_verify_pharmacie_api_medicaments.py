import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
ENDPOINT = "/pharmacie-api/medicaments"
AUTH_USERNAME = "abdoullah@gmail.com"
AUTH_PASSWORD = "12345678"
TIMEOUT = 30

def test_verify_pharmacie_api_medicaments():
    url = BASE_URL + ENDPOINT
    auth = HTTPBasicAuth(AUTH_USERNAME, AUTH_PASSWORD)
    headers = {
        "Accept": "application/json"
    }

    # Test general inventory fetch without query parameter
    try:
        response = requests.get(url, headers=headers, auth=auth, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected status 200, got {response.status_code}"
        data = response.json()
        assert isinstance(data, list), "Response should be a list"
        assert all('id' in med and 'nom' in med and 'stock' in med for med in data), "Each medication should have id, nom, and stock keys"
        assert all(isinstance(med['stock'], int) and med['stock'] >= 0 for med in data), "Stock levels should be non-negative integers"
    except requests.RequestException as e:
        assert False, f"Request failed: {e}"

    # Test search functionality with query parameter 'q'
    search_query = "aspirine"
    try:
        params = {"q": search_query}
        response = requests.get(url, headers=headers, auth=auth, params=params, timeout=TIMEOUT)
        assert response.status_code == 200, f"Search query status expected 200, got {response.status_code}"
        search_data = response.json()
        assert isinstance(search_data, list), "Search response should be a list"
        # If results present, all should have the queried string in their name (case-insensitive)
        if search_data:
            assert all(search_query.lower() in med.get('nom', "").lower() for med in search_data), "Search results should match query"
    except requests.RequestException as e:
        assert False, f"Search request failed: {e}"

test_verify_pharmacie_api_medicaments()
