<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JuCoop | Transformando el Comercio Escolar</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
    }

    body {
      overflow-x: hidden;
      font-family: 'Inter', sans-serif;
      background: #ffffff;
      display: flex;
      flex-direction: column;
    }
/* ===== TRUST SECTION ===== */

.trust-section {
  padding: 60px 5%;
  background: #ffffff;
  text-align: center;
}

.trust-section h2 {
  font-size: 36px;
  color: #0f2027;
}

.trust-subtitle {
  margin-top: 8px;
  color: #555;
  font-size: 17px;
}

.trust-logos {
  margin-top: 40px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 22px;
}

.school-card {
  background: #f0f4f1;
  padding: 20px;
  border-radius: 12px;
  font-size: 16px;
  color: #033961;
  font-weight: 600;
  border: 1px solid #dfe6df;
}

@media(max-width: 992px) {
  .trust-logos {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media(max-width: 768px) {
  .trust-logos {
    grid-template-columns: 1fr;
  }
}

/* ===== STATS SECTION ===== */

.stats-section {
  margin-top: 0;
  padding: 60px 5%;
  background: #0f2027;
  color: white;
  display: flex;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
}

.stat-card {
  text-align: center;
}

.stat-card h3 {
  font-size: 44px;
  color: #97d569;
}

.stat-card p {
  font-size: 16px;
  margin-top: 5px;
}

/* ===== WHY CHOOSE US ===== */

.why-section {
  padding: 60px 5%;
  text-align: center;
}

.why-section h2 {
  font-size: 38px;
  color: #0f2027;
}

.why-grid {
  margin-top: 40px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 25px;
}

.why-card {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.1);
}

.why-card h4 {
  font-size: 20px;
  color: #033961;
}

.why-card p {
  margin-top: 8px;
  color: #555;
}

@media(max-width: 992px) {
  .why-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media(max-width: 768px) {
  .why-grid {
    grid-template-columns: 1fr;
  }
}

/* ===== CTA FINAL ===== */

.cta-section {
  margin-top: 60px;
  padding: 70px 5%;
  background: #033961;
  color: white;
  text-align: center;
  border-radius: 0;
}

.cta-section h2 {
  font-size: 38px;
}

.cta-section p {
  margin-top: 10px;
  font-size: 18px;
}

.cta-btn {
  display: inline-block;
  margin-top: 25px;
  background: #97d569;
  padding: 14px 28px;
  border-radius: 8px;
  text-decoration: none;
  color: #033961;
  font-weight: 700;
  font-size: 17px;
}

.cta-btn:hover {
  background: #89c157;
}

    /* ===== NAVBAR ===== */

    .navbar {
      width: 100%;
      padding: 14px 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #0f2027;
      color: white;
      position: relative;
    }

    .nav-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo-img {
      height: 32px;
    }

    .nav-title {
      font-size: 20px;
      font-weight: 700;
    }

    /* BURGER MENU */
    .burger {
      display: none;
      background: none;
      border: none;
      font-size: 28px;
      color: white;
      cursor: pointer;
    }

    /* MENU DESKTOP */
    .nav-menu {
      display: flex;
      gap: 22px;
      align-items: center;
    }

    .nav-menu a {
      color: #dfe6e9;
      text-decoration: none;
      font-size: 15px;
      font-weight: 500;
    }

    .nav-menu a:hover {
      color: #97d569;
    }

    /* LOGIN BUTTON */
    .login-container {
      position: relative;
    }

    .login-button {
      background: #97d569;
      color: #0f2027;
      border: none;
      padding: 9px 15px;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      font-size: 14px;
    }

    .login-button:hover {
      background: #8ac05d;
    }

    .login-dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: 40px;
      background: white;
      width: 170px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      z-index: 20;
    }

    .login-dropdown a {
      display: block;
      padding: 12px 16px;
      color: #0f2027;
      text-decoration: none;
      font-size: 15px;
    }

    .login-dropdown a:hover {
      background: #eef3ee;
      color: #97d569;
    }

    .login-container.show .login-dropdown {
      display: block;
    }

    /* ===== MOBILE MENU ===== */

    .desktop-login {
      display: block;
    }
    .mobile-login {
      display: none;
    }

    @media (max-width: 768px) {

      .burger {
        display: block;
      }

      .nav-menu {
        display: none;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        background: #0f2027;
        padding: 18px;
        gap: 16px;
        position: absolute;
        top: 60px;
        left: 0;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        animation: fadeDown 0.25s ease-out;
      }

      .nav-menu.show {
        display: flex;
      }

      @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
      }

      .desktop-login {
        display: none;
      }

      .mobile-login {
        display: block;
        width: 100%;
      }
    }

    /* ===== HERO ===== */

    .hero {
      display: flex;
      padding: 55px 5%;
      justify-content: space-between;
      align-items: center;
      background: #f5f7fa;
      gap: 40px;
    }

    .hero-text {
      max-width: 500px;
    }

    .hero-text h1 {
      font-size: 46px;
      line-height: 1.2;
      color: #0f2027;
    }

    .hero-text span {
      color: #97d569;
    }

    .hero-text p {
      font-size: 18px;
      color: #555;
      margin-top: 15px;
    }

    .hero-img img {
      width: 100%;
      max-width: 380px;
      border-radius: 10px;
    }

    @media (max-width: 768px) {
      .hero {
        flex-direction: column;
        text-align: center;
        padding: 45px 20px;
      }

      .hero-text h1 {
        font-size: 32px;
      }

      .hero-img img {
        max-width: 240px;
      }
    }

    /* ===== OFFER SECTION ===== */

    .offer-section {
      padding: 70px 5%;
      text-align: center;
    }

    .offer-section h2 {
      font-size: 40px;
      color: #0f2027;
    }

    .offer-grid {
      margin-top: 50px;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 35px;
    }

    @media (max-width: 992px) {
      .offer-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .offer-grid {
        grid-template-columns: 1fr;
      }
    }

    .offer-card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    }

    .offer-card h3 {
      color: #033961;
      font-size: 22px;
    }

    .offer-card p {
      margin-top: 8px;
      color: #555;
    }

    /* FOOTER */
    footer {
      margin-top: auto;
      background: #0f2027;
      color: white;
      padding: 30px;
      text-align: center;
      width: 100%;
    }
  </style>
</head>

<body>

  <!-- ===== HEADER ===== -->
  <header class="navbar">
    <div class="nav-left">
      <img src="/images/coopceopng.png" alt="JuCoop" class="logo-img">
      <span class="nav-title">JuCoop</span>
    </div>

    <button class="burger" id="burgerBtn">☰</button>

    <nav class="nav-menu" id="mobileMenu">
      <a href="#">Inicio</a>
      <a href="#">Servicios</a>
      <a href="#">Back Office</a>
      <a href="#">POS</a>

      <div class="login-container mobile-login">
        <button class="login-button">Iniciar Sesión ▾</button>
        <div class="login-dropdown">
          <a href="{{ url('/user-panel') }}">Como Socio</a>
          <a href="{{ url('/admin-panel') }}">Como Administrador</a>
        </div>
      </div>
    </nav>

    <div class="login-container desktop-login">
      <button class="login-button">Iniciar Sesión ▾</button>
      <div class="login-dropdown">
        <a href="{{ url('/user-panel') }}">Como Socio</a>
        <a href="{{ url('/admin-panel') }}">Como Administrador</a>
      </div>
    </div>
  </header>

  <!-- ===== HERO ===== -->
  <section class="hero">
    <div class="hero-text">
      <h1>Transformando<br><span>el Comercio Escolar</span></h1>
      <p>En JuCoop ayudamos a cooperativas escolares a modernizar sus operaciones diarias.</p>
    </div>

    <div class="hero-img">
      <img src="/images/coopwoman.jpg">
    </div>
  </section>

  <!-- ===== OFFER SECTION ===== -->
  <section class="offer-section">
    <h2>Lo que ofrecemos</h2>

    <div class="offer-grid">
      <div class="offer-card">
        <h3>POS Escolar Moderno</h3>
        <p>Sistema de caja registradora intuitivo y veloz.</p>
      </div>

      <div class="offer-card">
        <h3>Back Office Avanzado</h3>
        <p>Control de inventario, ventas, usuarios y reportes PDF.</p>
      </div>

      <div class="offer-card">
        <h3>Portal para Socios</h3>
        <p>Accede a balances, historial y datos personales.</p>
      </div>
    </div>
  </section>

  <!-- ===== TRUST SECTION (ESCUELAS) ===== -->
<section class="trust-section">
  <h2>Escuelas que confían en nosotros</h2>
  <p class="trust-subtitle">JuCoop ya impulsa cooperativas escolares alrededor de Puerto Rico.</p>

  <div class="trust-logos">
    <div class="school-card">✨ Escuela Superior Vocacional Tomas C. Ongay</div>
  </div>
</section>

<!-- ===== STATS SECTION ===== -->
<section class="stats-section">
  <div class="stat-card">
    <h3 class="counter" data-target="150">0</h3>
    <p>Estudiantes impactados</p>
  </div>

  <div class="stat-card">
    <h3 class="counter" data-target="700">0</h3>
    <p>Transacciones procesadas</p>
  </div>

  <div class="stat-card">
    <h3 class="counter" data-target="95">0%</h3>
    <p>Satisfacción de uso</p>
  </div>
</section>


<!-- ===== WHY CHOOSE US ===== -->
<section class="why-section">
  <h2>¿Por qué elegir JuCoop?</h2>

  <div class="why-grid">
    <div class="why-card">
      <h4>✔ 100% para escuelas</h4>
      <p>JuCoop está diseñado exclusivamente para cooperativas escolares, con herramientas prácticas y simples.</p>
    </div>

    <div class="why-card">
      <h4>✔ Moderno y rápido</h4>
      <p>Interfaz elegante, fácil de usar, perfecta para estudiantes y administradores.</p>
    </div>

    <div class="why-card">
      <h4>✔ Reportes automáticos</h4>
      <p>Genera PDFs profesionales de ventas, inventario, ganancias y más.</p>
    </div>
  </div>
</section>

<!-- ===== CTA FINAL ===== -->
<section class="cta-section">
  <h2>Moderniza tu cooperativa con JuCoop</h2>
  <p>Únete a las escuelas que ya transformaron su sistema de ventas.</p>
  <a href="#" class="cta-btn">Solicitar información</a>
</section>


  <footer>
    © {{ date('Y') }} JuCoop — Todos los derechos reservados.
  </footer>

<script>
document.addEventListener("DOMContentLoaded", () => {

  /* ===== MENU ===== */
  const burger = document.getElementById("burgerBtn");
  const mobileMenu = document.getElementById("mobileMenu");

  burger.addEventListener("click", () => {
    mobileMenu.classList.toggle("show");
  });

  /* ===== LOGIN DROPDOWN ===== */
  const loginButtons = document.querySelectorAll(".login-button");

  loginButtons.forEach(button => {
    button.addEventListener("click", function (e) {
      e.stopPropagation();
      this.parentElement.classList.toggle("show");
    });
  });

  document.addEventListener("click", () => {
    document.querySelectorAll(".login-container").forEach(c => c.classList.remove("show"));
  });


  /* ===== COUNTER ANIMATION ===== */
  const counters = document.querySelectorAll(".counter");
  let started = false;

  const startCounting = () => {
    if (started) return;
    started = true;

    counters.forEach(counter => {
      const updateCount = () => {
        const target = +counter.getAttribute("data-target");
        const current = +counter.innerText.replace(/[^\d]/g, "");
        const increment = target / 80;

        if (current < target) {
          counter.innerText = Math.ceil(current + increment).toLocaleString();
          setTimeout(updateCount, 20);
        } else {
          counter.innerText = target.toLocaleString();
        }
      };

      updateCount();
    });
  };

  // Activar al llegar a la sección
  const statsSection = document.querySelector(".stats-section");

  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) {
      startCounting();
    }
  }, { threshold: 0.4 });

  if (statsSection) observer.observe(statsSection);

});
</script>


</body>

</html>
