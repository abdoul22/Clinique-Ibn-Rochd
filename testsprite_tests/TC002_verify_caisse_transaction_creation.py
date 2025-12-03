import requests

BASE_URL = "http://localhost:8000"
AUTH = ('abdoullah@gmail.com', '12345678')
TIMEOUT = 30
HEADERS = {'Content-Type': 'application/json'}


def verify_caisse_transaction_creation():
    # Step 1: Fetch existing ModePaiements to get an id
    mode_paiement_id = None
    try:
        res_mode_list = requests.get(
            f"{BASE_URL}/api/modepaiements",
            auth=AUTH,
            headers=HEADERS,
            timeout=TIMEOUT,
        )
        assert res_mode_list.status_code == 200, f"Failed to fetch ModePaiements: {res_mode_list.text}"
        mode_paiements = res_mode_list.json()
        assert isinstance(mode_paiements, list) and len(mode_paiements) > 0, "No ModePaiements found for test"
        mode_paiement_id = mode_paiements[0].get("id")
        assert mode_paiement_id is not None, "ModePaiement id missing in first entry"

        # Step 2: Prepare data for creating a caisse transaction (facture)
        res_examens = requests.get(
            f"{BASE_URL}/api/examens",
            auth=AUTH,
            headers=HEADERS,
            timeout=TIMEOUT,
        )
        assert res_examens.status_code == 200, f"Failed fetching examens: {res_examens.text}"
        examens_list = res_examens.json()
        assert isinstance(examens_list, list) and len(examens_list) > 0, "No examens found for test"

        examen = examens_list[0]
        examen_id = examen.get("id")
        part_medecin = examen.get("part_medecin")
        part_clinique = examen.get("part_cabinet") or examen.get("part_clinique") or 0
        prix = examen.get("prix") or 100  # fallback price

        examens_data = [
            {
                "examen_id": examen_id,
                "quantite": 2,
                "prix": prix,
                "part_medecin": part_medecin,
                "part_clinique": part_clinique,
            }
        ]
        total_expected = prix * 2
        part_medecin_expected = part_medecin * 2
        part_clinique_expected = part_clinique * 2

        # Step 3: Create caisse transaction (facture)
        caisse_payload = {
            "date": "2025-06-15",
            "mode_paiement_id": mode_paiement_id,
            "examens": examens_data,
            "remise": 0,
            "description": "Facture test creation",
            # Omit patient_id and medecin_id if None or not needed
        }

        res_caisse = requests.post(
            f"{BASE_URL}/api/caisses",
            json=caisse_payload,
            auth=AUTH,
            headers=HEADERS,
            timeout=TIMEOUT,
        )
        assert res_caisse.status_code == 201, f"Failed to create caisse: {res_caisse.text}"
        caisse = res_caisse.json()
        caisse_id = caisse.get("id")
        assert caisse_id is not None, "Caisse id missing in response"

        total_returned = caisse.get("total")
        part_medecin_returned = caisse.get("part_medecin")
        part_clinique_returned = caisse.get("part_clinique")

        assert total_returned == total_expected, f"Total mismatch: expected {total_expected}, got {total_returned}"
        assert part_medecin_returned == part_medecin_expected, f"part_medecin mismatch: expected {part_medecin_expected}, got {part_medecin_returned}"
        assert part_clinique_returned == part_clinique_expected, f"part_clinique mismatch: expected {part_clinique_expected}, got {part_clinique_returned}"

        res_etat = requests.get(
            f"{BASE_URL}/etatcaisse?caisse_id={caisse_id}",
            auth=AUTH,
            headers=HEADERS,
            timeout=TIMEOUT,
        )
        if res_etat.status_code != 200:
            res_etat_all = requests.get(
                f"{BASE_URL}/etatcaisse",
                auth=AUTH,
                headers=HEADERS,
                timeout=TIMEOUT,
            )
            assert res_etat_all.status_code == 200, f"Failed to fetch EtatCaisse: {res_etat_all.text}"
            etats = [etat for etat in res_etat_all.json() if etat.get("caisse_id") == caisse_id]
            assert len(etats) > 0, "No EtatCaisse entry found for created caisse"
            etat_caisse = etats[0]
        else:
            etats = res_etat.json()
            if isinstance(etats, list):
                assert len(etats) > 0, "No EtatCaisse entry found for created caisse"
                etat_caisse = etats[0]
            else:
                etat_caisse = etats

        recette = etat_caisse.get("recette")
        assert recette == total_expected, f"Recette mismatch in EtatCaisse: expected {total_expected}, got {recette}"

        etat_part_medecin = etat_caisse.get("part_medecin")
        etat_part_clinique = etat_caisse.get("part_clinique")

        assert etat_part_medecin == part_medecin_expected, f"EtatCaisse part_medecin mismatch: expected {part_medecin_expected}, got {etat_part_medecin}"
        assert etat_part_clinique == part_clinique_expected, f"EtatCaisse part_clinique mismatch: expected {part_clinique_expected}, got {etat_part_clinique}"

    finally:
        if 'caisse' in locals() and caisse and 'id' in caisse:
            requests.delete(
                f"{BASE_URL}/api/caisses/{caisse['id']}",
                auth=AUTH,
                headers=HEADERS,
                timeout=TIMEOUT,
            )


overify_caisse_transaction_creation()
