
# TestSprite AI Testing Report(MCP)

---

## 1️⃣ Document Metadata
- **Project Name:** clinique-ibn-rochd
- **Date:** 2025-12-02
- **Prepared by:** TestSprite AI Team

---

## 2️⃣ Requirement Validation Summary

#### Test TC001
- **Test Name:** verify_caisse_api_numero_entree_medecin
- **Test Code:** [TC001_verify_caisse_api_numero_entree_medecin.py](./TC001_verify_caisse_api_numero_entree_medecin.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 29, in <module>
  File "<string>", line 20, in verify_caisse_api_numero_entree_medecin
AssertionError: Response should be a JSON object

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/9f2b2043-8600-4d25-9b2c-be48d614f1ed
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC002
- **Test Name:** verify_caisse_transaction_creation
- **Test Code:** [TC002_verify_caisse_transaction_creation.py](./TC002_verify_caisse_transaction_creation.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 129, in <module>
NameError: name 'overify_caisse_transaction_creation' is not defined. Did you mean: 'verify_caisse_transaction_creation'?

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/ab7bad66-1566-4125-b105-3064901b9a6b
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC003
- **Test Name:** verify_caisse_doctor_share_calculation
- **Test Code:** [TC003_verify_caisse_doctor_share_calculation.py](./TC003_verify_caisse_doctor_share_calculation.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 100, in <module>
  File "<string>", line 33, in test_verify_caisse_doctor_share_calculation
  File "/var/task/requests/models.py", line 1024, in raise_for_status
    raise HTTPError(http_error_msg, response=self)
requests.exceptions.HTTPError: 404 Client Error: Not Found for url: http://localhost:8000/api/examens

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/eb9e97c3-c481-4c80-abde-960cfe00a03d
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC004
- **Test Name:** verify_caisse_clinic_share_calculation
- **Test Code:** [TC004_verify_caisse_clinic_share_calculation.py](./TC004_verify_caisse_clinic_share_calculation.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 92, in <module>
  File "<string>", line 34, in test_verify_caisse_clinic_share_calculation
AssertionError: Failed to create examen: {"message":"Unauthenticated."}

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/3d7af8c3-6cc3-4c46-9824-06650e8fa5fc
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC005
- **Test Name:** verify_caisse_insurance_coverage
- **Test Code:** [TC005_verify_caisse_insurance_coverage.py](./TC005_verify_caisse_insurance_coverage.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 107, in <module>
  File "<string>", line 23, in test_verify_caisse_insurance_coverage
AssertionError

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/25e05549-cf1e-4267-86c3-b9f92525e054
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC006
- **Test Name:** verify_etatcaisse_recette_tracking
- **Test Code:** [TC006_verify_etatcaisse_recette_tracking.py](./TC006_verify_etatcaisse_recette_tracking.py)
- **Test Error:** Traceback (most recent call last):
  File "<string>", line 31, in create_caisse_transaction
AssertionError: Unexpected status code when creating caisse: 200

During handling of the above exception, another exception occurred:

Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 91, in <module>
  File "<string>", line 60, in test_verify_etatcaisse_recette_tracking
  File "<string>", line 35, in create_caisse_transaction
AssertionError: Failed to create caisse transaction: Unexpected status code when creating caisse: 200

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/d9b82ca6-f206-49e4-9795-800bcd9041bd
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC007
- **Test Name:** verify_etatcaisse_validation
- **Test Code:** [TC007_verify_etatcaisse_validation.py](./TC007_verify_etatcaisse_validation.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 127, in <module>
  File "<string>", line 35, in test_verify_etatcaisse_validation
AssertionError: Caisse creation failed: {
    "message": "The route api/caisses could not be found.",
    "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
    "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\AbstractRouteCollection.php",
    "line": 45,
    "trace": [
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\RouteCollection.php",
            "line": 162,
            "function": "handleMatchedRoute",
            "class": "Illuminate\\Routing\\AbstractRouteCollection",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
            "line": 763,
            "function": "match",
            "class": "Illuminate\\Routing\\RouteCollection",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
            "line": 750,
            "function": "findRoute",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
            "line": 739,
            "function": "dispatchToRoute",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php",
            "line": 200,
            "function": "dispatch",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 169,
            "function": "Illuminate\\Foundation\\Http\\{closure}",
            "class": "Illuminate\\Foundation\\Http\\Kernel",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\livewire\\livewire\\src\\Features\\SupportDisablingBackButtonCache\\DisableBackButtonCacheMiddleware.php",
            "line": 19,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Livewire\\Features\\SupportDisablingBackButtonCache\\DisableBackButtonCacheMiddleware",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\app\\Http\\Middleware\\SetLocale.php",
            "line": 21,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "App\\Http\\Middleware\\SetLocale",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\View\\Middleware\\ShareErrorsFromSession.php",
            "line": 48,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\View\\Middleware\\ShareErrorsFromSession",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Session\\Middleware\\StartSession.php",
            "line": 120,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Session\\Middleware\\StartSession.php",
            "line": 63,
            "function": "handleStatefulRequest",
            "class": "Illuminate\\Session\\Middleware\\StartSession",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Session\\Middleware\\StartSession",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse.php",
            "line": 36,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php",
            "line": 21,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull.php",
            "line": 31,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php",
            "line": 21,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TrimStrings.php",
            "line": 51,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\TrimStrings",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePostSize.php",
            "line": 27,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Http\\Middleware\\ValidatePostSize",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance.php",
            "line": 109,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\HandleCors.php",
            "line": 61,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Http\\Middleware\\HandleCors",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\TrustProxies.php",
            "line": 58,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Http\\Middleware\\TrustProxies",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks.php",
            "line": 22,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePathEncoding.php",
            "line": 26,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 208,
            "function": "handle",
            "class": "Illuminate\\Http\\Middleware\\ValidatePathEncoding",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
            "line": 126,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php",
            "line": 175,
            "function": "then",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php",
            "line": 144,
            "function": "sendRequestThroughRouter",
            "class": "Illuminate\\Foundation\\Http\\Kernel",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php",
            "line": 1219,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Kernel",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\public\\index.php",
            "line": 20,
            "function": "handleRequest",
            "class": "Illuminate\\Foundation\\Application",
            "type": "->"
        },
        {
            "file": "C:\\Users\\Abdou\\Desktop\\web\\2025-projects\\ibnrochd\\clinique-ibn-rochd\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\resources\\server.php",
            "line": 23,
            "function": "require_once"
        }
    ]
}

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/ad93b84b-5bac-4d9c-a37d-bdcd7db86f8a
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC008
- **Test Name:** verify_modepaiement_creation
- **Test Code:** [TC008_verify_modepaiement_creation.py](./TC008_verify_modepaiement_creation.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 62, in <module>
  File "<string>", line 32, in test_verify_modepaiement_creation
AssertionError: Failed to create ModePaiement for espèces

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/66a30d80-d868-4151-b4a0-df0345c19e69
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC009
- **Test Name:** verify_modepaiement_source_tracking
- **Test Code:** [TC009_verify_modepaiement_source_tracking.py](./TC009_verify_modepaiement_source_tracking.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 133, in <module>
  File "<string>", line 27, in test_verify_modepaiement_source_tracking
  File "<string>", line 18, in login_get_token
AssertionError: Login failed: {"message":"Unauthenticated."}

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/6e90c4ba-4534-42fc-8fb5-8485467ca3ee
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC010
- **Test Name:** verify_modepaiement_totals_calculation
- **Test Code:** [TC010_verify_modepaiement_totals_calculation.py](./TC010_verify_modepaiement_totals_calculation.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 112, in <module>
  File "<string>", line 34, in test_verify_modepaiement_totals_calculation
AssertionError: Create caisse failed: {"message":"Unauthenticated."}

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/9b0361ad-0acb-4262-b6d1-62d76ba38232
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC011
- **Test Name:** verify_depense_creation
- **Test Code:** [TC011_verify_depense_creation.py](./TC011_verify_depense_creation.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 61, in <module>
  File "<string>", line 30, in test_verify_depense_creation
AssertionError: Failed to create Depense: <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Not Found</title>

        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}code{font-family:monospace,monospace;font-size:1em}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}code{font-family:Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-gray-400{--border-opacity:1;border-color:#cbd5e0;border-color:rgba(203,213,224,var(--border-opacity))}.border-t{border-top-width:1px}.border-r{border-right-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-xl{max-width:36rem}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-4{padding-left:1rem;padding-right:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.uppercase{text-transform:uppercase}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.tracking-wider{letter-spacing:.05em}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@-webkit-keyframes spin{0%{transform:rotate(0deg)}to{transform:rotate(1turn)}}@keyframes spin{0%{transform:rotate(0deg)}to{transform:rotate(1turn)}}@-webkit-keyframes ping{0%{transform:scale(1);opacity:1}75%,to{transform:scale(2);opacity:0}}@keyframes ping{0%{transform:scale(1);opacity:1}75%,to{transform:scale(2);opacity:0}}@-webkit-keyframes pulse{0%,to{opacity:1}50%{opacity:.5}}@keyframes pulse{0%,to{opacity:1}50%{opacity:.5}}@-webkit-keyframes bounce{0%,to{transform:translateY(-25%);-webkit-animation-timing-function:cubic-bezier(.8,0,1,1);animation-timing-function:cubic-bezier(.8,0,1,1)}50%{transform:translateY(0);-webkit-animation-timing-function:cubic-bezier(0,0,.2,1);animation-timing-function:cubic-bezier(0,0,.2,1)}}@keyframes bounce{0%,to{transform:translateY(-25%);-webkit-animation-timing-function:cubic-bezier(.8,0,1,1);animation-timing-function:cubic-bezier(.8,0,1,1)}50%{transform:translateY(0);-webkit-animation-timing-function:cubic-bezier(0,0,.2,1);animation-timing-function:cubic-bezier(0,0,.2,1)}}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}}
        </style>

        <style>
            body {
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center pt-8 sm:justify-start sm:pt-0">
                    <div class="px-4 text-lg text-gray-500 border-r border-gray-400 tracking-wider">
                        404                    </div>

                    <div class="ml-4 text-lg text-gray-500 uppercase tracking-wider">
                        Not Found                    </div>
                </div>
            </div>
        </div>
    </body>
</html>


- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/0e17e883-223f-4b47-9bbb-102b05b244e9
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC012
- **Test Name:** verify_depense_part_medecin_source
- **Test Code:** [TC012_verify_depense_part_medecin_source.py](./TC012_verify_depense_part_medecin_source.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 59, in <module>
  File "<string>", line 37, in test_verify_depense_part_medecin_source
AssertionError: Failed to create depense, status: 401, response: {"message":"Unauthenticated."}

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/9f13bbee-5ad7-429f-b5d2-fe1fffdd6174
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC013
- **Test Name:** verify_credit_creation_for_assurance
- **Test Code:** [TC013_verify_credit_creation_for_assurance.py](./TC013_verify_credit_creation_for_assurance.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/requests/models.py", line 974, in json
    return complexjson.loads(self.text, **kwargs)
           ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
  File "/var/lang/lib/python3.12/site-packages/simplejson/__init__.py", line 514, in loads
    return _default_decoder.decode(s)
           ^^^^^^^^^^^^^^^^^^^^^^^^^^
  File "/var/lang/lib/python3.12/site-packages/simplejson/decoder.py", line 386, in decode
    obj, end = self.raw_decode(s)
               ^^^^^^^^^^^^^^^^^^
  File "/var/lang/lib/python3.12/site-packages/simplejson/decoder.py", line 416, in raw_decode
    return self.scan_once(s, idx=_w(s, idx).end())
           ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
simplejson.errors.JSONDecodeError: Expecting value: line 1 column 1 (char 0)

During handling of the above exception, another exception occurred:

Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 93, in <module>
  File "<string>", line 25, in test_verify_credit_creation_for_assurance
  File "/var/task/requests/models.py", line 978, in json
    raise RequestsJSONDecodeError(e.msg, e.doc, e.pos)
requests.exceptions.JSONDecodeError: Expecting value: line 1 column 1 (char 0)

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/1ab02de7-70ce-4223-944b-ba49ed1d6f24
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC014
- **Test Name:** verify_credit_payment_processing
- **Test Code:** [TC014_verify_credit_payment_processing.py](./TC014_verify_credit_payment_processing.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/requests/models.py", line 974, in json
    return complexjson.loads(self.text, **kwargs)
           ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
  File "/var/lang/lib/python3.12/site-packages/simplejson/__init__.py", line 514, in loads
    return _default_decoder.decode(s)
           ^^^^^^^^^^^^^^^^^^^^^^^^^^
  File "/var/lang/lib/python3.12/site-packages/simplejson/decoder.py", line 386, in decode
    obj, end = self.raw_decode(s)
               ^^^^^^^^^^^^^^^^^^
  File "/var/lang/lib/python3.12/site-packages/simplejson/decoder.py", line 416, in raw_decode
    return self.scan_once(s, idx=_w(s, idx).end())
           ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
simplejson.errors.JSONDecodeError: Expecting value: line 1 column 1 (char 0)

During handling of the above exception, another exception occurred:

Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 82, in <module>
  File "<string>", line 30, in test_verify_credit_payment_processing
  File "/var/task/requests/models.py", line 978, in json
    raise RequestsJSONDecodeError(e.msg, e.doc, e.pos)
requests.exceptions.JSONDecodeError: Expecting value: line 1 column 1 (char 0)

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/28135aa1-1530-4a19-a1aa-5af2dbfa80d9
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC015
- **Test Name:** verify_etatcaisse_general_generation
- **Test Code:** [TC015_verify_etatcaisse_general_generation.py](./TC015_verify_etatcaisse_general_generation.py)
- **Test Error:** Traceback (most recent call last):
  File "<string>", line 21, in test_verify_etatcaisse_general_generation
  File "/var/task/requests/models.py", line 1024, in raise_for_status
    raise HTTPError(http_error_msg, response=self)
requests.exceptions.HTTPError: 401 Client Error: Unauthorized for url: http://localhost:8000/login

During handling of the above exception, another exception occurred:

Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 60, in <module>
  File "<string>", line 23, in test_verify_etatcaisse_general_generation
AssertionError: Login request failed: 401 Client Error: Unauthorized for url: http://localhost:8000/login

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/76a200e4-7336-42b9-a951-72c22196d028
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC016
- **Test Name:** verify_situation_journaliere_daily_report
- **Test Code:** [TC016_verify_situation_journaliere_daily_report.py](./TC016_verify_situation_journaliere_daily_report.py)
- **Test Error:** Traceback (most recent call last):
  File "<string>", line 19, in test_verify_situation_journaliere_daily_report
  File "/var/task/requests/models.py", line 1024, in raise_for_status
    raise HTTPError(http_error_msg, response=self)
requests.exceptions.HTTPError: 401 Client Error: Unauthorized for url: http://localhost:8000/superadmin/situation-journaliere

During handling of the above exception, another exception occurred:

Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 70, in <module>
  File "<string>", line 21, in test_verify_situation_journaliere_daily_report
AssertionError: Request to http://localhost:8000/superadmin/situation-journaliere failed: 401 Client Error: Unauthorized for url: http://localhost:8000/superadmin/situation-journaliere

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/337b12f0-6301-45b5-9a81-efd72458efbc
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC017
- **Test Name:** verify_multiple_examens_in_caisse
- **Test Code:** [TC017_verify_multiple_examens_in_caisse.py](./TC017_verify_multiple_examens_in_caisse.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 117, in <module>
  File "<string>", line 33, in test_verify_multiple_examens_in_caisse
AssertionError: Login failed with status code 200

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/9e436365-ff26-4b93-b0a1-0aea14f419b1
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---

#### Test TC018
- **Test Name:** verify_caisse_pdf_export
- **Test Code:** [TC018_verify_caisse_pdf_export.py](./TC018_verify_caisse_pdf_export.py)
- **Test Error:** Traceback (most recent call last):
  File "/var/task/handler.py", line 258, in run_with_retry
    exec(code, exec_env)
  File "<string>", line 76, in <module>
  File "<string>", line 35, in test_verify_caisse_pdf_export
AssertionError: Unexpected status code on caisse creation: 200

- **Test Visualization and Result:** https://www.testsprite.com/dashboard/mcp/tests/323a054b-1fb8-4a04-aef3-c1db1f2f31df/c70368c8-c284-4fd7-93b5-03b5919bf350
- **Status:** ❌ Failed
- **Analysis / Findings:** {{TODO:AI_ANALYSIS}}.
---


## 3️⃣ Coverage & Matching Metrics

- **0.00** of tests passed

| Requirement        | Total Tests | ✅ Passed | ❌ Failed  |
|--------------------|-------------|-----------|------------|
| ...                | ...         | ...       | ...        |
---


## 4️⃣ Key Gaps / Risks
{AI_GNERATED_KET_GAPS_AND_RISKS}
---