import requests


def verify_hospitalisations_lits_disponibles():
    base_url = "http://localhost:8000"
    login_url = base_url + "/login"
    endpoint = "/hospitalisations/lits-disponibles"
    url = base_url + endpoint

    headers = {
        "Accept": "application/json"
    }

    login_payload = {
        "email": "abdoullah@gmail.com",
        "password": "12345678"
    }

    session = requests.Session()

    # Perform login to obtain session cookies
    try:
        login_response = session.post(login_url, data=login_payload, headers=headers, timeout=30)
        login_response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Login request to {login_url} failed: {e}"

    # Now perform GET request with session authentication
    try:
        response = session.get(url, headers=headers, timeout=30)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request to {url} failed: {e}"

    try:
        data = response.json()
    except ValueError:
        assert False, "Response content is not valid JSON"

    # Validate that the response is a list or dict with availability info
    assert isinstance(data, (list, dict)), "Response JSON should be a list or dict"

    if isinstance(data, list):
        # If list, expect each item to represent a bed availability entity
        for item in data:
            assert isinstance(item, dict), "Each item in response list should be a dict"
            # Check keys and types relevant for bed availability if any keys present
            if item:
                if "id" in item:
                    assert isinstance(item["id"], int), "'id' should be int"
                if "status" in item:
                    assert item["status"] in ["available", "occupied", "cleaning"], "'status' should be a valid bed status"
                if "room_number" in item:
                    assert isinstance(item["room_number"], (int, str)), "'room_number' should be int or str"
    else:
        # If dict, check for keys that represent availability summary or details
        if "total_available" in data:
            assert isinstance(data["total_available"], int), "'total_available' should be int"
        if "beds" in data:
            assert isinstance(data["beds"], list), "'beds' should be a list"

    # Check available beds count
    available_beds_count = 0
    if isinstance(data, list):
        for bed in data:
            if bed.get("status") == "available":
                available_beds_count += 1
    elif isinstance(data, dict) and "total_available" in data:
        available_beds_count = data["total_available"]

    assert available_beds_count >= 0, "Available beds count should be zero or more"


verify_hospitalisations_lits_disponibles()
