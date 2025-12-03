import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
AUTH = HTTPBasicAuth("abdoullah@gmail.com", "12345678")
TIMEOUT = 30
HEADERS = {
    "Accept": "application/pdf"
}

def test_verify_caisse_pdf_export():
    caisse_id = None
    created_caisse_id = None

    # Step 1: Create a caisse transaction to have a valid resource for PDF export test
    caisse_create_url = f"{BASE_URL}/superadmin/caisses"
    caisse_data = {
        # Minimal valid caisse creation data for financial transaction
        "description": "Test transaction for PDF export",
        "montant": 150.00,
        "mode_paiement": "espèces",
        "examen_ids": [],  # Assuming empty list or valid exam IDs if required
        "date": "2025-11-19",
        # Add additional required fields here as per API schema if necessary
    }

    # Create a new caisse entry
    try:
        response_create = requests.post(
            caisse_create_url,
            auth=AUTH,
            json=caisse_data,
            timeout=TIMEOUT
        )
        assert response_create.status_code == 201, f"Unexpected status code on caisse creation: {response_create.status_code}"
        created_caisse = response_create.json()
        created_caisse_id = created_caisse.get("id")
        assert created_caisse_id is not None, "Missing caisse ID in creation response"

        # Step 2: Export the PDF for the created caisse
        pdf_export_url = f"{BASE_URL}/caisses/{created_caisse_id}/exportPdf"
        response_pdf = requests.get(
            pdf_export_url,
            auth=AUTH,
            headers=HEADERS,
            timeout=TIMEOUT
        )
        assert response_pdf.status_code == 200, f"PDF export failed with status {response_pdf.status_code}"
        content_type = response_pdf.headers.get("Content-Type", "")
        assert content_type == "application/pdf", f"Expected PDF Content-Type but got {content_type}"

        pdf_content = response_pdf.content
        assert len(pdf_content) > 1000, "PDF content is unexpectedly small, may be invalid or empty"

        # Optional: Check for presence of key financial strings inside the PDF bytes decoded to text (may require pdf parsing lib)
        # Since no library usage specified other than requests, apply basic bytes assert for presence of expected sections.
        # We check raw PDF content for key byte strings for parts medecin and clinique (ASCII-only versions).
        assert b"partie medecin" in pdf_content or b"part_medecin" in pdf_content or b"medecin" in pdf_content, \
            "PDF does not contain medecin share information."
        assert b"partie clinique" in pdf_content or b"part_clinique" in pdf_content or b"clinique" in pdf_content, \
            "PDF does not contain clinique share information."
        assert b"recette" in pdf_content or b"total" in pdf_content or b"montant" in pdf_content, \
            "PDF does not contain financial totals or recette information."

    finally:
        # Cleanup: Delete the created caisse to maintain test environment
        if created_caisse_id:
            delete_url = f"{BASE_URL}/superadmin/caisses/{created_caisse_id}"
            try:
                response_delete = requests.delete(delete_url, auth=AUTH, timeout=TIMEOUT)
                assert response_delete.status_code in (200, 204), f"Failed to delete test caisse with status {response_delete.status_code}"
            except Exception:
                pass


test_verify_caisse_pdf_export()
