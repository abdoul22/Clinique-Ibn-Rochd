import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000/C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd"
AUTH = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
TIMEOUT = 30

def test_verify_api_examens_id_stock_info():
    examen_id = None
    created_examen_id = None

    headers = {
        "Accept": "application/json",
        "Content-Type": "application/json"
    }

    try:
        # Step 1: Create a new examen resource if needed to have a valid examen ID
        create_url = f"{BASE_URL}/superadmin/examens"
        examen_payload = {
            "nom": "Test Examen",
            "description": "Examen created for testing stock-info endpoint",
            "type": "Echo",
            "tarif": 100.0
        }
        create_response = requests.post(create_url, json=examen_payload, auth=AUTH, headers=headers, timeout=TIMEOUT)
        assert create_response.status_code == 201, f"Failed to create examen, status code: {create_response.status_code}"
        examen_data = create_response.json()
        created_examen_id = examen_data.get("id")
        assert created_examen_id is not None, "Created examen ID not returned"

        examen_id = created_examen_id

        # Step 2: Request the stock info for the created examen ID
        stock_info_url = f"{BASE_URL}/api/examens/{examen_id}/stock-info"
        response = requests.get(stock_info_url, auth=AUTH, headers=headers, timeout=TIMEOUT)

        # Step 3: Validate response status code and content
        assert response.status_code == 200, f"Expected status 200, got {response.status_code}"
        json_data = response.json()
        # The stock-info should include certain keys, for example 'stock_available' or 'medications' list 
        # We assert keys exist but since PRD doesn't specify exact schema of stock-info, we perform basic checks
        assert isinstance(json_data, dict), "Response JSON is not a dictionary"
        # We expect at least one key indicating stock information
        assert len(json_data) > 0, "Stock info response is empty"

        # Example: Check for common fields if available
        if "stock_available" in json_data:
            assert isinstance(json_data["stock_available"], (int, float)), "'stock_available' should be int or float"

        if "medications" in json_data:
            assert isinstance(json_data["medications"], list), "'medications' should be a list"

    finally:
        # Cleanup: Delete the created examen resource
        if created_examen_id is not None:
            delete_url = f"{BASE_URL}/superadmin/examens/{created_examen_id}"
            try:
                del_response = requests.delete(delete_url, auth=AUTH, headers=headers, timeout=TIMEOUT)
                # Accept 200 or 204 as success for deletion
                assert del_response.status_code in [200, 204], f"Failed to delete examen, status code: {del_response.status_code}"
            except Exception:
                pass

test_verify_api_examens_id_stock_info()