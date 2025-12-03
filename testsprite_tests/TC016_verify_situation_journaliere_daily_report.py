import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
TIMEOUT = 30
USERNAME = "abdoullah@gmail.com"
PASSWORD = "12345678"

def test_verify_situation_journaliere_daily_report():
    """Test the daily situation report (/superadmin/situation-journaliere) and verify breakdown of financial data."""

    url = f"{BASE_URL}/superadmin/situation-journaliere"
    headers = {
        "Accept": "application/json"
    }

    try:
        response = requests.get(url, headers=headers, auth=HTTPBasicAuth(USERNAME, PASSWORD), timeout=TIMEOUT)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request to {url} failed: {e}"

    data = response.json()

    # Validate general structure
    assert isinstance(data, dict), "Response should be a JSON object"

    assert "services" in data, "'services' key must be present in response"
    assert isinstance(data["services"], list), "'services' should be a list"

    for service in data["services"]:
        assert isinstance(service, dict), "Each service entry should be a dict"
        for key in ["service_name", "total_recette", "part_medecin", "part_clinique", "depenses"]:
            assert key in service, f"Key '{key}' missing in service entry"
        
        assert isinstance(service["service_name"], str), "'service_name' should be a string"
        assert isinstance(service["total_recette"], (int, float)), "'total_recette' should be numeric"
        assert isinstance(service["part_medecin"], (int, float)), "'part_medecin' should be numeric"
        assert isinstance(service["part_clinique"], (int, float)), "'part_clinique' should be numeric"
        assert isinstance(service["depenses"], (int, float)), "'depenses' should be numeric"

        # Financial logic assertions (non-negative values)
        assert service["total_recette"] >= 0, "total_recette must be >= 0"
        assert service["part_medecin"] >= 0, "part_medecin must be >= 0"
        assert service["part_clinique"] >= 0, "part_clinique must be >= 0"
        assert service["depenses"] >= 0, "depenses must be >= 0"

        # part_medecin + part_clinique should not exceed total_recette
        assert service["part_medecin"] + service["part_clinique"] <= service["total_recette"] + 0.01, \
            "Sum of part_medecin and part_clinique should not exceed total_recette"

    assert "total" in data, "'total' key must be present in response"
    total = data["total"]
    for key in ["recette", "part_medecin", "part_clinique", "depenses"]:
        assert key in total, f"Key '{key}' missing in total summary"
        assert isinstance(total[key], (int, float)), f"Total {key} should be numeric"
        assert total[key] >= 0, f"Total {key} must be >= 0"

    # totals sanity: total recette should be >= sum of parts for all services (allow tiny float rounding)
    sum_recette = sum(s["total_recette"] for s in data["services"])
    sum_part_medecin = sum(s["part_medecin"] for s in data["services"])
    sum_part_clinique = sum(s["part_clinique"] for s in data["services"])
    sum_depenses = sum(s["depenses"] for s in data["services"])

    assert abs(total["recette"] - sum_recette) < 0.1, "total recette mismatch vs sum of services"
    assert abs(total["part_medecin"] - sum_part_medecin) < 0.1, "total part_medecin mismatch vs sum of services"
    assert abs(total["part_clinique"] - sum_part_clinique) < 0.1, "total part_clinique mismatch vs sum of services"
    assert abs(total["depenses"] - sum_depenses) < 0.1, "total depenses mismatch vs sum of services"

test_verify_situation_journaliere_daily_report()
