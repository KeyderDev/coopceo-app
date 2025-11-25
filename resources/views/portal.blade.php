<!-- resources/views/portal.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal COOPCEO</title>
  <meta name="description" content="Portal oficial de COOPCEO — Accede a tu cuenta de socio o al panel administrativo.">
  <meta property="og:image" content="{{ asset('images/coopceofixed.png') }}">
  <link rel="icon" type="image/png" href="/images/coopceofixed.png" />

  <style>
    /* 🌌 Fondo */
    html,
    body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Inter', sans-serif;
      background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow-x: hidden;
    }

    .page {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: stretch;
      width: 100%;
      max-width: 1100px;
      min-height: 80vh;
      margin: 2rem;
      border-radius: 20px;
      background: rgba(25, 27, 31, 0.85);
      backdrop-filter: blur(14px);
      box-shadow: 0 0 40px rgba(0, 0, 0, 0.45);
      overflow: hidden;
      animation: fadeIn 0.8s ease-in-out;
    }

    /* 🌿 Panel izquierdo */
    .portal-section {
      flex: 1;
      padding: 3rem 2rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      border-right: 1px solid rgba(255, 255, 255, 0.1);
      text-align: center;
    }

    .logo-img {
      width: 130px;
      height: auto;
      margin-bottom: 1.5rem;
      filter: drop-shadow(0 0 10px rgba(157, 216, 106, 0.4));
    }

    .title {
      font-size: 2.2rem;
      font-weight: 700;
      color: #9dd86a;
      margin-bottom: 0.4rem;
    }

    .subtitle {
      font-size: 1rem;
      color: #ccc;
      margin-bottom: 2rem;
    }

    .buttons {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      width: 100%;
      max-width: 280px;
    }

    .btn {
      padding: 0.9rem;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 10px;
      text-decoration: none;
      text-align: center;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-account {
      background: linear-gradient(135deg, #9dd86a, #7ab55c);
      color: #fff;
      box-shadow: 0 6px 16px rgba(157, 216, 106, 0.4);
    }

    .btn-account:hover {
      background: linear-gradient(135deg, #b9f089, #91d46d);
      transform: translateY(-2px);
    }

    .btn-admin {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #9dd86a;
    }

    .btn-admin:hover {
      background: rgba(157, 216, 106, 0.15);
      color: #b9f089;
      transform: translateY(-2px);
    }

    /* 📰 Panel derecho */
    .news-section {
      flex: 1;
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
    }

    .news-section h2 {
      font-size: 1.6rem;
      color: #9dd86a;
      margin-bottom: 1.2rem;
      text-align: left;
    }

    .news-section ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .news-section ul li {
      padding: 0.8rem 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      font-size: 1rem;
      color: #eaeaea;
      line-height: 1.6;
    }

    #turnstile-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(10, 10, 10, 0.9);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .turnstile-box {
      background: #1e1e1e;
      padding: 2rem;
      border-radius: 15px;
      text-align: center;
      width: 90%;
      max-width: 400px;
      color: #fff;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
    }

    .turnstile-box h2 {
      margin-bottom: 10px;
      color: #9dd86a;
    }

    .turnstile-box p {
      margin-bottom: 20px;
      color: #ccc;
    }

    .news-section ul li:last-child {
      border-bottom: none;
    }

    /* 🌀 Animaciones */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* 📱 Responsive */
    @media (max-width: 900px) {
      .page {
        flex-direction: column;
        align-items: center;
        height: auto;
        margin: 1rem;
      }

      .portal-section {
        border-right: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 2rem 1.5rem;
      }

      .news-section {
        padding: 2rem 1.5rem;
        text-align: center;
      }

      .news-section h2 {
        text-align: center;
      }

      .news-section ul li {
        font-size: 0.95rem;
      }
    }

    @media (max-width: 480px) {
      .title {
        font-size: 1.8rem;
      }

      .subtitle {
        font-size: 0.9rem;
      }

      .btn {
        font-size: 0.9rem;
        padding: 0.8rem;
      }

      .logo-img {
        width: 110px;
      }
    }
  </style>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<body>
  <div id="turnstile-overlay">
    <div class="turnstile-box">
      <h2>Verificación de seguridad</h2>
      <p>Por favor, confirma que no eres un robot.</p>

      <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}" data-callback="onTurnstileSuccess"></div>
    </div>
  </div>
  </div>

  <div class="page">
    <div class="portal-section">
      <img src="{{ asset('images/coopceofixed.png') }}" alt="COOPCEO Logo" class="logo-img" />
      <h1 class="title">Portal COOPCEO</h1>
      <p class="subtitle">Bienvenido al sistema de gestión de COOPCEO</p>

      <div class="buttons">
        <a href="{{ url('/user-panel') }}" class="btn btn-account">Manejar mi cuenta - Socio</a>
        <a href="{{ url('/admin-panel') }}" class="btn btn-admin">Administración</a>
      </div>
    </div>

    <div class="news-section">
      <h2>📰 Novedades</h2>
      <ul>
        <li>📢 ¡Vuelven los mantecados a la venta en la cooperativa!</li>
        <li>💡 Consulta tus dividendos actualizados en el portal de socios.</li>
      </ul>
    </div>
  </div>

  <script>
    function onTurnstileSuccess(token) {
      document.getElementById('turnstile-overlay').style.display = 'none';
    }
  </script>

</body>

</html>