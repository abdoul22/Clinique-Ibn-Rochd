import requests
from requests.auth import HTTPBasicAuth

def test_verify_hospitalisations_search_patients_by_phone():
    base_url = "http://localhost:8000"
    endpoint = "/hospitalisations/search-patients-by-phone"
    url = base_url + endpoint
    auth = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
    headers = {
        "Accept": "application/json"
    }

    # Sample phone number to test search functionality (using a dummy phone number)
    phone_number = "1234567890"

    try:
        response = requests.get(url, params={"phone": phone_number}, auth=auth, headers=headers, timeout=30)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request failed: {e}"

    # Validate response
    json_data = response.json()
    assert isinstance(json_data, list), "Response should be a list of patient records"

    for patient in json_data:
        # Each patient record should be a dict containing at least a phone field that includes the search number
        assert isinstance(patient, dict), "Each patient record should be a dictionary"
        assert "phone" in patient, "Patient record should contain 'phone' field"
        assert phone_number in patient["phone"], "Patient phone field should contain the search input"

test_verify_hospitalisations_search_patients_by_phone()