<!-- resources/views/portal.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal COOPCEO</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }

        * {
            box-sizing: border-box;
        }

        /* Página */
        .page {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #044271 0%, #0a5a95 100%);
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .card {
            background: #ffffff;
            color: #044271;
            border-radius: 1rem;
            padding: 3rem 2.5rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
            text-align: center;
            max-width: 420px;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.35);
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 1rem;
            color: #6b7c93;
            margin-bottom: 2rem;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn {
            padding: 0.85rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 0.5rem;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            width: 100%;
            text-decoration: none;
            text-align: center;
        }

        .btn-account {
            background: #9ad369;
            color: #044271;
        }

        .btn-account:hover {
            background: #82b859;
        }

        .btn-admin {
            background: #044271;
            color: #fff;
            border: 1px solid #03365c;
        }

        .btn-admin:hover {
            background: #03365c;
        }

        .logo-img {
            width: 160px;
            height: auto;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <div class="logo">
                <img src="{{ asset('images/coopceopng.png') }}" alt="COOPCEO Logo" class="logo-img" />
            </div>
            <h1 class="title">Portal COOPCEO</h1>
            <p class="subtitle">Bienvenido al sistema de gestión de COOPCEO</p>
            <div class="buttons">
                <a href="{{ url('/user-panel') }}" class="btn btn-account">Manejar mi cuenta</a>
                <a href="{{ url('/admin-panel') }}" class="btn btn-admin">Administración</a>
            </div>
        </div>
    </div>
</body>
</html>
