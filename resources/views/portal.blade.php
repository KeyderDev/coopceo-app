<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JuCoop | Transformando el Comercio Escolar</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

html, body { height: 100%; }

body {
  overflow-x: hidden;
  font-family: 'Inter', sans-serif;
  background: #0f2027;
  display: flex;
  flex-direction: column;
  color: #e4f0eb;
}

/* -------- NAVBAR -------- */
.navbar {
  width: 100%;
  padding: 16px 22px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #0f2027;
}

.nav-left { display: flex; align-items: center; gap: 10px; }

.logo-img { height: 34px; }

.nav-title { font-size: 22px; font-weight: 700; color: #97d569; }

.burger {
  display: none;
  background: none;
  border: none;
  font-size: 28px;
  color: white;
  cursor: pointer;
}

.nav-menu {
  display: flex;
  gap: 26px;
  align-items: center;
}

.nav-menu a {
  color: #d4e4df;
  text-decoration: none;
  font-size: 15px;
  transition: 0.3s;
}

.nav-menu a:hover {
  color: #97d569;
}

.login-container { position: relative; }

.login-button {
  background: #97d569;
  color: #0f2027;
  border: none;
  padding: 9px 15px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  font-size: 14px;
  font-family: 'Inter', sans-serif;  
}


.login-dropdown {
  display: none;
  position: absolute;
  right: 0;
  top: 42px;
  background: #112d36;
  width: 170px;
  border-radius: 8px;
  box-shadow: 0 4px 14px rgba(0,0,0,0.3);
}

.login-dropdown a {
  display: block;
  padding: 12px 16px;
  color: #97d569;
}

.login-dropdown a:hover {
  background: #0f2027;
}

.login-container.show .login-dropdown { display: block; }

.desktop-login { display: block; }
.mobile-login { display: none; }

@media(max-width: 768px){
  .burger { display: block; }

  .nav-menu {
    display: none;
    flex-direction: column;
    width: 100%;
    padding: 20px;
    background: #112d36;
    gap: 20px;
    position: absolute;
    top: 66px;
    left: 0;
  }

  .nav-menu.show { display: flex; }

  .desktop-login { display: none; }
  .mobile-login { display: block; width: 100%; }
}

/* -------- HERO -------- */
.hero {
  display: flex;
  padding: 70px 6%;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(135deg, #03355c 0%, #0f2027 100%);
  gap: 40px;
  min-height: 440px;
}

.hero-text { max-width: 520px; }

.hero-text h1 {
  font-size: 48px;
  line-height: 1.2;
  color: #97d569;
}

.hero-text span { color: #d9f7cd; }

.hero-text p {
  color: #c7d4d9;
  margin-top: 18px;
  font-size: 19px;
}

.hero-img img {
  width: 100%;
  max-width: 480px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.35);
}


@media(max-width: 768px){
  .hero {
    flex-direction: column;
    text-align: center;
    padding: 55px 20px;
  }
  .hero-text h1 { font-size: 34px; }
  .hero-img img { max-width: 240px; }
}

/* -------- OFFER SECTION -------- */
.offer-section {
  background: #112d36;
  padding: 80px 6%;
  text-align: center;
}

.offer-section h2 {
  font-size: 38px;
  color: #97d569;
}

.offer-grid {
  margin-top: 55px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 38px;
}

@media(max-width:992px) { .offer-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:768px) { .offer-grid { grid-template-columns: 1fr; } }

.offer-card {
  background: #03355c;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.25);
  transition: 0.25s;
}

.offer-card:hover {
  transform: translateY(-6px);
}

.offer-card h3 {
  font-size: 22px;
  color: #97d569;
}

.offer-card p {
  margin-top: 10px;
  color: #d4e5e4;
}

/* -------- TRUST SECTION -------- */
.trust-section {
  background: #0f2027;
  padding: 75px 6%;
  text-align: center;
}

.trust-section h2 {
  font-size: 36px;
  color: #97d569;
}

.trust-subtitle {
  color: #b9cac8;
  margin-top: 10px;
  font-size: 17px;
}

.trust-logos {
  margin-top: 40px;
  display: grid;
  grid-template-columns: repeat(4,1fr);
  gap: 25px;
}

.school-card {
  background: #112d36;
  padding: 20px;
  border-radius: 10px;
  border: 1px solid #97d569;
  color: #d1f2ce;
  font-weight: 600;
}

@media(max-width:992px){ .trust-logos { grid-template-columns: repeat(2,1fr); } }
@media(max-width:768px){ .trust-logos { grid-template-columns: 1fr; } }

/* -------- STATS -------- */
.stats-section {
  padding: 65px 6%;
  background: #112d36;
  display: flex;
  justify-content: center;
  gap: 55px;
  flex-wrap: wrap;
}

.stat-card {
  text-align: center;
}

.stat-card h3 {
  font-size: 48px;
  color: #97d569;
}

.stat-card p {
  color: #c6d6ca;
  margin-top: 5px;
}

/* -------- WHY -------- */
.why-section {
  padding: 80px 6%;
  background: #0f2027;
  text-align: center;
}

.why-section h2 {
  font-size: 38px;
  color: #97d569;
}

.why-grid {
  margin-top: 45px;
  display: grid;
  grid-template-columns: repeat(3,1fr);
  gap: 30px;
}

@media(max-width:992px){ .why-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:768px){ .why-grid { grid-template-columns: 1fr; } }

.why-card {
  padding: 26px;
  border-radius: 12px;
  background: #112d36;
  box-shadow: 0 4px 14px rgba(0,0,0,0.25);
  transition: 0.25s;
}

.why-card:hover {
  transform: translateY(-6px);
}

.why-card h4 {
  color: #97d569;
  font-size: 21px;
}

.why-card p {
  margin-top: 10px;
  color: #cddbd7;
}

/* -------- CTA -------- */
.cta-section {
  padding: 75px 6%;
  background: linear-gradient(135deg,#03355c,#0c1a21);
  text-align: center;
}

.cta-section h2 { color: #97d569; font-size: 40px; }

.cta-section p { margin-top: 12px; color: #d5e2d8; font-size: 19px; }

.cta-btn {
  display: inline-block;
  margin-top: 30px;
  padding: 14px 32px;
  background: #97d569;
  color: #0f2027;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 700;
  font-size: 18px;
}

/* FOOTER */
footer {
  padding: 30px;
  text-align: center;
  color: #d3e6dd;
  background: #0f2027;
}
</style>
</head>

<body>

<header class="navbar">
  <div class="nav-left">
    <img src="/images/coopceopng.png" class="logo-img">
    <span class="nav-title">JuCoop</span>
  </div>

  <button class="burger" id="burgerBtn">☰</button>

  <nav class="nav-menu" id="mobileMenu">
    <a href="#">Inicio</a>
    <a href="#">Servicios</a>
    <a href="#">Back Office</a>
    <a href="/pos">POS</a>

    <div class="login-container mobile-login">
      <button class="login-button">Iniciar Sesión ▾</button>
      <div class="login-dropdown">
        <a href="/user-panel">Como Socio</a>
        <a href="/admin-panel">Como Administrador</a>
      </div>
    </div>
  </nav>

  <div class="login-container desktop-login">
    <button class="login-button">Iniciar Sesión ▾</button>
    <div class="login-dropdown">
      <a href="/user-panel">Como Socio</a>
      <a href="/admin-panel">Como Administrador</a>
    </div>
  </div>
</header>

<section class="hero">
  <div class="hero-text">
    <h1>Transformando<br><span>el Comercio Escolar</span></h1>
    <p>En JuCoop ayudamos a cooperativas escolares a modernizar sus operaciones diarias.</p>
  </div>
  <div class="hero-img">
    <img src="/images/grupo.jpeg">
  </div>
</section>

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

<section class="trust-section">
  <h2>Escuelas que confían en nosotros</h2>
  <p class="trust-subtitle">JuCoop ya impulsa cooperativas escolares alrededor de Puerto Rico.</p>

  <div class="trust-logos">
    <div class="school-card">✨ Escuela Superior Vocacional Tomas C. Ongay</div>
  </div>
</section>

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


<section class="why-section">
  <h2>¿Por qué elegir JuCoop?</h2>

  <div class="why-grid">
    <div class="why-card">
      <h4>✔ 100% para escuelas</h4>
      <p>Diseñado exclusivamente para cooperativas escolares.</p>
    </div>

    <div class="why-card">
      <h4>✔ Moderno y rápido</h4>
      <p>Interfaz elegante y fácil de usar.</p>
    </div>

    <div class="why-card">
      <h4>✔ Reportes automáticos</h4>
      <p>PDFs profesionales de ventas, inventario y más.</p>
    </div>
  </div>
</section>

<section class="cta-section">
  <h2>Moderniza tu cooperativa con JuCoop</h2>
  <p>Únete a las escuelas que ya transformaron su sistema de ventas.</p>
  <a href="#" class="cta-btn">Solicitar información</a>
</section>

<footer>
  © 2025 JuCoop — Todos los derechos reservados.
</footer>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const burger = document.getElementById("burgerBtn");
  const mobileMenu = document.getElementById("mobileMenu");

  burger.addEventListener("click", () => {
    mobileMenu.classList.toggle("show");
  });

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

  const statsSection = document.querySelector(".stats-section");

  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) {
      startCounting();
    }
  }, { threshold: 0.4 });

  observer.observe(statsSection);
});
</script>

</body>
</html>
