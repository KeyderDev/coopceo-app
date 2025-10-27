<!-- resources/views/portal.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal COOPCEO</title>
    <meta name="google-site-verification" content="M5DFDnqwNkwe0zeOGWKtkutRqLIngdrZU1x9S-1hsjc" />
    <meta name="description"
        content="Portal oficial de COOPCEO — Accede a tu cuenta de socio o al panel administrativo.">
    <meta name="keywords" content="COOPCEO, cooperativa, socios, administración, portal, finanzas">
    <meta name="author" content="COOPCEO">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="Portal COOPCEO">
    <meta property="og:description"
        content="Bienvenido al portal oficial de COOPCEO. Gestiona tu cuenta de socio o accede al panel administrativo.">
    <meta property="og:image" content="{{ asset('images/coopceofixed.png') }}">
    <meta property="og:url" content="https://coopceo.online">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Portal COOPCEO">
    <meta name="twitter:description" content="Accede al sistema de gestión de COOPCEO.">
    <meta name="twitter:image" content="{{ asset('images/coopceofixed.png') }}">

    <link rel="icon" type="image/png" href="/images/coopceofixed.png">

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }

        * {
            box-sizing: border-box;
        }

        .page {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #044271 0%, #0a5a95 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 2rem;
            gap: 2rem;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        @media (min-width: 1024px) {
            .page {
                padding-top: 12rem;
            }
        }

        .card {
            background: #ffffff;
            color: #044271;
            border-radius: 1rem;
            padding: 2rem;
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
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 1rem;
            color: #6b7c93;
            margin-bottom: 1.5rem;
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
            width: 140px;
            height: auto;
            margin-bottom: 1.2rem;
        }

        /* Noticias */
        .news {
            background: #ffffff;
            color: #044271;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
            max-width: 500px;
            width: 100%;
        }

        .news h2 {
            margin-top: 0;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }

        .news ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .news ul li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #ccc;
            font-size: 0.95rem;
        }

        .news ul li:last-child {
            border-bottom: none;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .page {
                flex-direction: column;
                align-items: center;
                padding: 1rem;
                gap: 1.5rem;
            }

            .card,
            .news {
                max-width: 100%;
                padding: 1.5rem;
            }

            .title {
                font-size: 1.6rem;
            }

            .subtitle {
                font-size: 0.95rem;
            }

            .logo-img {
                width: 120px;
            }
        }

        @media (max-width: 480px) {
            .title {
                font-size: 1.4rem;
            }

            .btn {
                font-size: 0.9rem;
                padding: 0.7rem;
            }

            .news h2 {
                font-size: 1.2rem;
            }
        }
    </style>

</head>

<body>
    <div class="page">
        <div class="news">
            <h2>📰 Novedades</h2>
            <ul>
                <li>📢 Fecha de apertura oficial: 15 de octubre de 2025</li>
            </ul>
        </div>
        <div class="card">
            <div class="logo">
                <img src="{{ asset('images/coopceofixed.png') }}" alt="COOPCEO Logo" class="logo-img" />
            </div>
            <h1 class="title">Portal COOPCEO</h1>
            <p class="subtitle">Bienvenido al sistema de gestión de COOPCEO</p>
            <div class="buttons">
                <a href="{{ url('/user-panel') }}" class="btn btn-account">Manejar mi cuenta - Socio</a>
                <a href="{{ url('/admin-panel') }}" class="btn btn-admin">Administración</a>
            </div>
        </div>
    </div>
</body>

</html>