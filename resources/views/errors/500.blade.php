<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur 500 - Erreur Interne du Serveur</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #e74c3c;
            font-size: 32px;
            margin: 0 0 20px 0;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }

        .error-details {
            background-color: #f9f9f9;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #2980b9;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1e1e1e;
                color: #e0e0e0;
            }

            .container {
                background-color: #2d2d2d;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            }

            .error-details {
                background-color: #1a1a1a;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>❌ Erreur 500</h1>
        <p><strong>Erreur Interne du Serveur</strong></p>
        <p>Une erreur s'est produite lors du traitement de votre demande. Notre équipe technique a été notifiée.</p>

        @if (config('app.debug'))
        <div class="error-details">
            Vérifiez les logs pour plus de détails:
            - Laravel: storage/logs/laravel.log
            - Serveur: logs Apache/Nginx
        </div>
        @endif

        <a href="{{ url('/') }}" class="button">Retour à l'accueil</a>
    </div>
</body>

</html>
