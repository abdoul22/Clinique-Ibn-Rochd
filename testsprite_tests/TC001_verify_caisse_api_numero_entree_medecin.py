import requests
from requests.auth import HTTPBasicAuth

def verify_caisse_api_numero_entree_medecin():
    base_url = "http://localhost:8000"
    numero_entree_endpoint_template = f"{base_url}/api/caisses/numero-entree/{{medecin_id}}"
    auth = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
    timeout = 30

    medecin_id = 1  # Use a fixed medecin_id for the test

    try:
        # Call the numero-entree endpoint for the medecin_id
        numero_entree_endpoint = numero_entree_endpoint_template.format(medecin_id=medecin_id)
        response = requests.get(numero_entree_endpoint, auth=auth, timeout=timeout)
        response.raise_for_status()

        data = response.json()
        # Validate response is a dict with 'next_numero_entree' integer field
        assert isinstance(data, dict), "Response should be a JSON object"
        next_numero_entree = data.get("next_numero_entree")
        assert next_numero_entree is not None, "Response missing 'next_numero_entree'"
        assert isinstance(next_numero_entree, int), "'next_numero_entree' should be an integer"
        assert next_numero_entree >= 0, "'next_numero_entree' should be non-negative"

    except requests.exceptions.RequestException as e:
        assert False, f"HTTP request failed: {e}"

verify_caisse_api_numero_entree_medecin()