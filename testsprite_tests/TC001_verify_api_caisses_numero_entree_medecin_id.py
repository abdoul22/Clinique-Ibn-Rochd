import requests
from requests.auth import HTTPBasicAuth

def test_verify_api_caisses_numero_entree_medecin_id():
    base_url = "http://localhost:8000"
    username = "abdoullah@gmail.com"
    password = "12345678"
    timeout = 30

    medecin_id = None
    auth = HTTPBasicAuth(username, password)
    headers = {"Accept": "application/json"}

    try:
        list_medecins_url = f"{base_url}/admin/medecins"
        resp = requests.get(list_medecins_url, auth=auth, headers=headers, timeout=timeout)
        resp.raise_for_status()
        medecins = resp.json()
        if isinstance(medecins, list) and len(medecins) > 0:
            medecin_id = medecins[0].get("id")
    except Exception:
        medecin_id = 1

    assert medecin_id is not None, "No medecin ID available for testing."

    endpoint = f"{base_url}/api/caisses/numero-entree/{medecin_id}"

    try:
        response = requests.get(endpoint, auth=auth, headers=headers, timeout=timeout)
    except requests.RequestException as e:
        assert False, f"Request to {endpoint} failed: {e}"

    assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"

    try:
        data = response.json()
    except ValueError:
        assert False, "Response is not a valid JSON."

    # If data is a list, try to get the first element
    if isinstance(data, list) and len(data) > 0:
        data = data[0]

    assert isinstance(data, dict), "Response JSON is not an object/dictionary."

    possible_keys = ["numero_entree", "numeroEntree", "entry_number", "unique_number", "numero"]
    found_key = None
    for key in possible_keys:
        if key in data:
            found_key = key
            break
    assert found_key is not None, f"Response JSON does not contain any expected keys {possible_keys}"

    val = data[found_key]
    assert val is not None, "Unique entry number is None."
    assert (isinstance(val, int) and val >= 0) or (isinstance(val, str) and val.strip() != ""), "Invalid unique entry number format."

test_verify_api_caisses_numero_entree_medecin_id()