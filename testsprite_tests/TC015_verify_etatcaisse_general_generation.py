import requests

def test_verify_etatcaisse_general_generation():
    base_url = "http://localhost:8000"
    login_url = f"{base_url}/login"
    etatcaisse_url = f"{base_url}/etatcaisse/general"
    headers = {"Accept": "application/json"}
    timeout = 30

    # Credentials for login
    login_data = {
        "email": "abdoullah@gmail.com",
        "password": "12345678"
    }

    session = requests.Session()

    try:
        # Perform login
        login_response = session.post(login_url, data=login_data, headers={"Accept": "application/json"}, timeout=timeout)
        login_response.raise_for_status()
    except requests.RequestException as e:
        assert False, f"Login request failed: {str(e)}"

    # Verify login success by checking if session cookie is set or status code
    if not session.cookies:
        assert False, "Login failed, no session cookie received"

    try:
        response = session.get(etatcaisse_url, headers=headers, timeout=timeout)
        response.raise_for_status()
    except requests.RequestException as e:
        assert False, f"Request to generate general EtatCaisse report failed: {str(e)}"

    try:
        data = response.json()
    except ValueError:
        assert False, "Response is not valid JSON"

    # Verify keys exist in the response
    assert "totaux" in data, "Response JSON missing 'totaux' key"
    totaux = data["totaux"]

    required_fields = ["recette", "part_medecin", "part_clinique", "depenses", "credits"]
    for field in required_fields:
        assert field in totaux, f"Key '{field}' missing in 'totaux'"

        value = totaux[field]
        assert isinstance(value, (int, float)), f"Value for '{field}' should be a number"
        assert value >= 0, f"Value for '{field}' should be non-negative"

    recette = totaux["recette"]
    part_medecin = totaux["part_medecin"]
    part_clinique = totaux["part_clinique"]

    assert recette >= part_medecin, "recette should be greater or equal to part_medecin"
    assert recette >= part_clinique, "recette should be greater or equal to part_clinique"


test_verify_etatcaisse_general_generation()
