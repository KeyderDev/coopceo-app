<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JuCoop POS | Punto de Venta Escolar</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
html,body{height:100%}
body{
  overflow-x:hidden;
  font-family:'Inter',sans-serif;
  background:#0f2027;
  color:#e4f0eb;
  display:flex;
  flex-direction:column
}

img{
  max-width:100%;
  height:auto
}

.navbar{
  width:100%;
  padding:16px 22px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  background:#0f2027;
  position:sticky;
  top:0;
  z-index:40
}
.nav-left{display:flex;align-items:center;gap:10px}
.logo-img{height:34px}
.nav-title{font-size:22px;font-weight:700;color:#97d569}
.burger{
  display:none;
  background:none;
  border:none;
  font-size:28px;
  color:#fff;
  cursor:pointer
}
.nav-menu{
  display:flex;
  gap:26px;
  align-items:center
}
.nav-menu a{
  color:#d4e4df;
  text-decoration:none;
  font-size:15px;
  transition:.3s
}
.nav-menu a:hover{color:#97d569}
.login-container{position:relative}
.login-button{
  background:#97d569;
  color:#0f2027;
  border:none;
  padding:9px 15px;
  border-radius:6px;
  font-weight:600;
  cursor:pointer;
  font-size:14px
}
.login-dropdown{
  display:none;
  position:absolute;
  right:0;
  top:42px;
  background:#112d36;
  width:180px;
  border-radius:8px;
  box-shadow:0 4px 14px rgba(0,0,0,.3)
}
.login-dropdown a{
  display:block;
  padding:12px 16px;
  color:#97d569;
  text-decoration:none;
  font-size:14px
}
.login-dropdown a:hover{background:#0f2027}
.login-container.show .login-dropdown{display:block}
.desktop-login{display:block}
.mobile-login{display:none}

@media(max-width:768px){
  .burger{display:block}
  .nav-menu{
    display:none;
    flex-direction:column;
    width:100%;
    padding:20px;
    background:#112d36;
    gap:20px;
    position:absolute;
    top:66px;
    left:0
  }
  .nav-menu.show{display:flex}
  .desktop-login{display:none}
  .mobile-login{display:block;width:100%}
}

main{flex:1}

.section{
  padding:70px 6%
}

.section-dark{background:#0f2027}
.section-mid{background:#112d36}
.section-gradient{background:linear-gradient(135deg,#03355c,#0f2027)}

.pos-hero{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:50px;
  min-height:420px
}
.pos-hero-left{max-width:520px}
.pos-hero-title{
  font-size:40px;
  line-height:1.2;
  color:#97d569
}
.pos-hero-subtitle{
  margin-top:14px;
  font-size:18px;
  color:#c7d4d9
}
.pos-hero-tags{
  margin-top:18px;
  display:flex;
  flex-wrap:wrap;
  gap:8px
}
.badge{
  display:inline-flex;
  align-items:center;
  padding:4px 10px;
  border-radius:999px;
  font-size:12px;
  background:rgba(151,213,105,0.1);
  color:#d8f5ce;
  border:1px solid rgba(151,213,105,0.4)
}
.primary-btn{
  display:inline-block;
  margin-top:26px;
  padding:12px 26px;
  border-radius:8px;
  border:none;
  font-size:15px;
  font-weight:700;
  background:#97d569;
  color:#0f2027;
  cursor:pointer;
  text-decoration:none
}
.secondary-link{
  margin-left:14px;
  font-size:14px;
  color:#d4e4df;
  text-decoration:none
}
.secondary-link:hover{color:#97d569}

.pos-hero-right{
  flex:1;
  display:flex;
  justify-content:center
}

.pos-hero-right img{
  width:100%;
  max-width:520px;
  border-radius:14px;
  box-shadow:0 10px 26px rgba(0,0,0,0.45);
  object-fit:cover;
  margin-left:300px
}

@media(max-width:900px){
  .pos-hero{
    flex-direction:column;
    text-align:center;
    gap:30px;
    padding-top:20px
  }
  .pos-hero-left{
    max-width:90%
  }
  .pos-hero-right img{
    margin-left:0;
    max-width:420px
  }
}

@media(max-width:600px){
  .pos-hero-right img{
    max-width:300px
  }
}

.section-title{
  font-size:32px;
  color:#97d569;
  text-align:center
}
.section-subtitle{
  margin-top:10px;
  font-size:16px;
  color:#c6d6d0;
  text-align:center;
  max-width:620px;
  margin-inline:auto
}

.pos-overview-grid{
  margin-top:40px;
  display:grid;
  grid-template-columns:2fr 1.4fr;
  gap:40px;
  align-items:flex-start
}
.pos-overview-text p{
  margin-bottom:14px;
  font-size:15px;
  color:#d7e3dd;
  line-height:1.6
}
.pos-overview-list{
  margin-top:10px;
  list-style:none
}
.pos-overview-list li{
  margin-bottom:8px;
  font-size:15px;
  color:#cfe0d9
}
.pos-overview-list span{
  color:#97d569;
  font-weight:600
}
.pos-overview-aside{
  background:#03355c;
  padding:22px;
  border-radius:12px;
  box-shadow:0 6px 20px rgba(0,0,0,.35)
}
.pos-overview-aside h3{
  font-size:18px;
  color:#97d569;
  margin-bottom:10px
}
.pos-overview-aside p{
  font-size:14px;
  color:#d8e7e3;
  line-height:1.5
}

@media(max-width:900px){
  .pos-overview-grid{grid-template-columns:1fr}
}

.pos-layout-wrapper{
  margin-top:45px;
  display:grid;
  grid-template-columns:1.7fr 1.3fr;
  gap:40px;
  align-items:center
}
.pos-layout-image{
  background:#0f2027;
  border-radius:14px;
  padding:18px;
  box-shadow:0 8px 22px rgba(0,0,0,.45)
}
.pos-layout-image img{
  width:100%;
  border-radius:10px;
  object-fit:cover
}
.pos-layout-legend h3{
  font-size:20px;
  color:#dff6d5;
  margin-bottom:12px
}
.pos-layout-legend ul{
  list-style:none;
  margin-top:10px
}
.pos-layout-legend li{
  margin-bottom:10px;
  font-size:14px;
  color:#d3e4dd
}
.pos-layout-legend strong{
  color:#97d569
}

@media(max-width:900px){
  .pos-layout-wrapper{grid-template-columns:1fr}
}

.pos-sections-grid{
  margin-top:45px;
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:24px
}
.pos-section-card{
  background:#0f2027;
  border-radius:12px;
  padding:20px;
  box-shadow:0 4px 16px rgba(0,0,0,.35)
}
.pos-section-card h3{
  font-size:18px;
  color:#97d569;
  margin-bottom:8px
}
.pos-section-card p{
  font-size:14px;
  color:#d6e3dc;
  line-height:1.5
}

@media(max-width:1024px){
  .pos-sections-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:700px){
  .pos-sections-grid{grid-template-columns:1fr}
}

.steps-wrapper{
  margin-top:45px;
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:22px
}
.step-card{
  background:#0f2027;
  border-radius:12px;
  padding:18px 16px;
  box-shadow:0 4px 14px rgba(0,0,0,.35)
}
.step-number{
  width:28px;
  height:28px;
  border-radius:999px;
  background:#97d569;
  color:#0f2027;
  font-size:13px;
  font-weight:700;
  display:flex;
  align-items:center;
  justify-content:center;
  margin-bottom:10px
}
.step-card h3{
  font-size:16px;
  color:#e8f3ea;
  margin-bottom:6px
}
.step-card p{
  font-size:14px;
  color:#cbdad6
}

@media(max-width:900px){
  .steps-wrapper{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:600px){
  .steps-wrapper{grid-template-columns:1fr}
}

.features-grid{
  margin-top:45px;
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:26px
}
.feature-card{
  background:#03355c;
  border-radius:12px;
  padding:22px;
  box-shadow:0 5px 18px rgba(0,0,0,.35)
}
.feature-card h3{
  font-size:17px;
  color:#dff6d5;
  margin-bottom:8px
}
.feature-card p{
  font-size:14px;
  color:#d2e2de
}

@media(max-width:1024px){
  .features-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:700px){
  .features-grid{grid-template-columns:1fr}
}

.security-grid{
  margin-top:42px;
  display:grid;
  grid-template-columns:1.4fr 1.6fr;
  gap:36px;
  align-items:flex-start
}
.security-text p{
  font-size:14px;
  color:#d4e0dd;
  margin-bottom:10px;
  line-height:1.6
}
.security-list{
  list-style:none;
  margin-top:10px
}
.security-list li{
  margin-bottom:8px;
  font-size:14px;
  color:#cfe1da
}
.security-list span{
  color:#97d569;
  font-weight:600
}
.security-card{
  background:#0f2027;
  padding:20px;
  border-radius:12px;
  box-shadow:0 5px 20px rgba(0,0,0,.35)
}
.security-card h3{
  font-size:18px;
  color:#97d569;
  margin-bottom:10px
}
.security-card ul{
  list-style:none
}
.security-card li{
  font-size:14px;
  color:#d3e3dd;
  margin-bottom:8px
}

@media(max-width:900px){
  .security-grid{grid-template-columns:1fr}
}

.integration-grid{
  margin-top:40px;
  display:grid;
  grid-template-columns:1.6fr 1.4fr;
  gap:34px;
  align-items:center
}
.integration-text p{
  font-size:14px;
  color:#d5e2dd;
  margin-bottom:10px;
  line-height:1.6
}
.integration-tags{
  margin-top:8px;
  display:flex;
  flex-wrap:wrap;
  gap:8px
}
.integration-image{
  background:#0f2027;
  border-radius:12px;
  padding:16px;
  box-shadow:0 6px 18px rgba(0,0,0,.4)
}
.integration-image img{
  width:100%;
  border-radius:8px;
  object-fit:cover
}

@media(max-width:900px){
  .integration-grid{grid-template-columns:1fr}
}

@media(max-width:600px){
  .pos-sections-grid,
  .features-grid,
  .steps-wrapper{
    gap:18px
  }
  .pos-layout-wrapper,
  .integration-grid,
  .security-grid{
    gap:25px
  }
}

.pos-cta-inner{
  text-align:center;
  max-width:640px;
  margin-inline:auto
}
.pos-cta-inner h2{
  font-size:32px;
  color:#97d569
}
.pos-cta-inner p{
  margin-top:10px;
  font-size:15px;
  color:#d3e2dc
}

footer{
  padding:30px;
  text-align:center;
  color:#d3e6dd;
  background:#0f2027
}

</style>
</head>
<body>

<header class="navbar">
  <div class="nav-left">
    <img src="/images/coopceopng.png" class="logo-img" alt="JuCoop">
    <span class="nav-title">JuCoop</span>
  </div>

  <button class="burger" id="burgerBtn">☰</button>

  <nav class="nav-menu" id="mobileMenu">
    <a href="/">Inicio</a>
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

<main>

<section class="section section-gradient">
  <div class="pos-hero">
    <div class="pos-hero-left">
      <div class="badge">Módulo especializado para cooperativas escolares</div>
      <h1 class="pos-hero-title">JuCoop POS</h1>
      <p class="pos-hero-subtitle">Un punto de venta moderno pensado para estudiantes, asesores y administradores. Rápido, seguro y totalmente integrado al Back Office de JuCoop.</p>
      <div class="pos-hero-tags">
        <span class="badge">Ventas rápidas</span>
        <span class="badge">Inventario en tiempo real</span>
        <span class="badge">Control de gavetas</span>
      </div>
      <a href="#flujo-pos" class="primary-btn">Ver cómo funciona</a>
      <a href="#partes-pos" class="secondary-link">Conocer las partes del POS</a>
    </div>
    <div class="pos-hero-right">
      <img src="/images/pos.png" alt="Interfaz de JuCoop POS en una tablet">
    </div>
  </div>
</section>

<section class="section section-dark">
  <h2 class="section-title">¿Qué es JuCoop POS?</h2>
  <p class="section-subtitle">Es el módulo de punto de venta que centraliza todo el movimiento diario de la cooperativa escolar: ventas, métodos de pago, control de cajero, clientes y cierre de día.</p>

  <div class="pos-overview-grid">
    <div class="pos-overview-text">
      <p>JuCoop POS reemplaza la caja registradora tradicional por una interfaz clara y guiada, donde cada estudiante puede registrar ventas de forma segura y supervisada. Cada transacción actualiza inventario, estadísticas y reportes en el Back Office.</p>
      <p>Funciona completamente en línea, con autenticación por usuario, permisos por rol y registro de quién realizó cada movimiento. La información fluye automáticamente hacia los módulos de reportes, cuadre de gavetas y estados financieros.</p>
      <ul class="pos-overview-list">
        <li><span>Orientado a escuelas:</span> interfaz simple, textos claros y botones grandes.</li>
        <li><span>Totalmente trazable:</span> cada venta queda asociada a un cajero, cliente y método de pago.</li>
        <li><span>Sin papeleo:</span> reportes en PDF listos para imprimir o enviar por correo.</li>
      </ul>
    </div>
    <aside class="pos-overview-aside">
      <h3>Resumen operativo diario</h3>
      <p>Desde el POS se puede ver el total vendido en el día, los métodos de pago utilizados, los productos más vendidos y el estado de la gaveta asignada. Todo queda disponible en segundos para directores, asesores y juntas de la cooperativa.</p>
    </aside>
  </div>
</section>

<section class="section section-mid" id="partes-pos">
  <h2 class="section-title">Partes de la interfaz de JuCoop POS</h2>
  <p class="section-subtitle">Cada área tiene un propósito claro para que la operación sea rápida y sin confusiones. A continuación se muestra una vista general del layout del POS.</p>

  <div class="pos-layout-wrapper">
    <div class="pos-layout-image">
      <img src="/images/pos.png" alt="Distribución de la pantalla de JuCoop POS">
    </div>
    <div class="pos-layout-legend">
      <h3>Áreas principales</h3>
      <ul>
        <li><strong>1. Barra de busqueda:</strong> permite buscar al socio al que se le adjudicara la compra y los dividendos resultantes.</li>
        <li><strong>2. Seccion de la orden:</strong> lista de productos seleccionados para la compra.</li>
        <li><strong>3. Seccion de productos:</strong> el listado de productos registrados desde el back office.</li>
        <li><strong>4. Resumen de pago:</strong> total, impuestos (si aplican), descuentos autorizados y botones de método de pago.</li>
        <li><strong>5. Barra de acciones:</strong> botones para seleccionar el monto en efectivo, mostrar el codigo qr de ATH Movil, y boton de mas opciones para funciones de administrador.</li>
      </ul>
    </div>
  </div>

  <div class="pos-sections-grid" style="margin-top:35px">
    <div class="pos-section-card">
      <h3>Área de cliente</h3>
      <p>Permite asociar la venta a un socio específico mediante búsqueda en tiempo real. Esto facilita reportes por estudiante y estados de cuenta individuales en el portal para socios.</p>
    </div>
    <div class="pos-section-card">
      <h3>Grid de productos</h3>
      <p>Presenta los artículos disponibles con sus existencias. Cuando el inventario llega a cero, el producto se desactiva visualmente para evitar ventas fuera de stock.</p>
    </div>
    <div class="pos-section-card">
      <h3>Carrito dinámico</h3>
      <p>Actualiza automaticamente cantidades, subtotales y total general. El cajero puede modificar cantidades o eliminar líneas sin perder el resto de la venta.</p>
    </div>
    <div class="pos-section-card">
      <h3>Métodos de pago</h3>
      <p>Permite diferenciar efectivo, ATH Móvil u otros métodos definidos por la cooperativa. Esta información se usa luego en el cuadre de gaveta y los reportes diarios.</p>
    </div>
    <div class="pos-section-card">
      <h3>Control de gavetas</h3>
      <p>Cada usuario solo puede operar la gaveta que se le asignó. Los movimientos quedan vinculados a esa gaveta para facilitar el cuadre al final del turno o del día.</p>
    </div>
    <div class="pos-section-card">
      <h3>Alertas y validaciones</h3>
      <p>El sistema muestra mensajes claros cuando falta seleccionar cliente, método de pago, o cuando se intenta vender un producto sin inventario suficiente.</p>
    </div>
  </div>
</section>

<section class="section section-dark" id="flujo-pos">
  <h2 class="section-title">Cómo funciona una venta en JuCoop POS</h2>
  <p class="section-subtitle">El flujo está diseñado para que estudiantes y cajeros nuevos lo aprendan en minutos, manteniendo a la vez el control administrativo que la cooperativa necesita.</p>

  <div class="steps-wrapper">
    <div class="step-card">
      <div class="step-number">1</div>
      <h3>Iniciar sesión y abrir gaveta</h3>
      <p>El cajero entra con su usuario y, si tiene gaveta asignada, confirma el fondo inicial. Desde aquí, cada movimiento quedará registrado a su nombre.</p>
    </div>
    <div class="step-card">
      <div class="step-number">2</div>
      <h3>Seleccionar cliente</h3>
      <p>Se busca al socio por nombre o número y se vincula la venta. Si el cliente es general, se puede manejar como venta rápida sin socio.</p>
    </div>
    <div class="step-card">
      <div class="step-number">3</div>
      <h3>Agregar productos</h3>
      <p>El cajero selecciona los productos desde la cuadrícula. El carrito suma cantidades, descuentos autorizados y subtotales.</p>
    </div>
    <div class="step-card">
      <div class="step-number">4</div>
      <h3>Registrar el pago</h3>
      <p>Se elige método de pago, se registra el monto recibido y el sistema calcula cambio si aplica. Al confirmar, la venta queda guardada y el inventario actualizado, y los dividendos del socio son actualizados.</p>
    </div>
  </div>
</section>

<section class="section section-mid">
  <h2 class="section-title">Funciones clave del módulo POS</h2>
  <p class="section-subtitle">Además de registrar ventas, JuCoop POS incorpora herramientas específicas para el entorno cooperativo escolar.</p>

  <div class="features-grid">
    <div class="feature-card">
      <h3>Inventario en tiempo real</h3>
      <p>Cada venta descuenta automáticamente las existencias y evita vender productos agotados, manteniendo el Back Office siempre sincronizado.</p>
    </div>
    <div class="feature-card">
      <h3>Perfiles y roles</h3>
      <p>Los estudiantes ven solo las funciones de cajero, mientras que asesores y administradores pueden acceder a cierres, devoluciones y reportes.</p>
    </div>
    <div class="feature-card">
      <h3>Cierres y cuadres guiados</h3>
      <p>Al finalizar el día, el sistema ayuda a comparar ventas registradas vs efectivo y otros métodos, generando reportes listos para imprimir.</p>
    </div>
    <div class="feature-card">
      <h3>Historial detallado</h3>
      <p>Cada ticket registra fecha, hora, cajero, socio, productos y método de pago, permitiendo auditorías rápidas cuando sea necesario.</p>
    </div>
    <div class="feature-card">
      <h3>Soporte para precios especiales</h3>
      <p>Se pueden manejar descuentos autorizados mediante código de superadministrador, dejando registro de quién los aprobó.</p>
    </div>
    <div class="feature-card">
      <h3>Diseñado para pantallas táctiles</h3>
      <p>Botones grandes, contraste alto y estructura pensada para funcionar en tablets y equipos tipo kiosko dentro de la escuela.</p>
    </div>
  </div>
</section>

<section class="section section-dark">
  <h2 class="section-title">Seguridad y control en el POS</h2>
  <p class="section-subtitle">La plataforma protege tanto la información de la cooperativa como la experiencia educativa de los estudiantes.</p>

  <div class="security-grid">
    <div class="security-text">
      <p>JuCoop POS incorpora buenas prácticas de seguridad para evitar accesos no autorizados, modificaciones sin permiso y errores de operación. Cada cambio importante requiere rol adecuado o código de superadministrador.</p>
      <ul class="security-list">
        <li><span>Acceso por usuario:</span> cada cajero tiene sus propias credenciales.</li>
        <li><span>Bitácora de acciones:</span> se registra qué usuario realizó cada venta o devolución.</li>
        <li><span>Control de gavetas:</span> una gaveta no puede usarse por varios usuarios a la vez sin autorización.</li>
        <li><span>Datos centralizados:</span> la información se guarda en la base de datos de la cooperativa, no en las estaciones de trabajo.</li>
      </ul>
    </div>
    <div class="security-card">
      <h3>Ejemplos de controles</h3>
      <ul>
        <li>Solicitud de código de superadmin para cancelaciones mayores.</li>
        <li>Bloqueo de sesión tras inactividad prolongada.</li>
        <li>Resumen de movimientos por usuario al final del turno.</li>
        <li>Reportes que comparan ventas por gaveta vs sistema.</li>
      </ul>
    </div>
  </div>
</section>

<section class="section section-mid">
  <h2 class="section-title">Integración con otros módulos de JuCoop</h2>
  <p class="section-subtitle">El POS no está aislado: se conecta directamente con el Back Office y el Portal para Socios para mantener toda la información alineada.</p>

  <div class="integration-grid">
    <div class="integration-text">
      <p>Cuando se registra una venta en JuCoop POS, el sistema actualiza inventario, estadísticas diarias, reportes de cooperativa y, si aplica, el historial del socio. Esto evita dobles registros y reduce errores en los informes oficiales.</p>
      <p>El Back Office puede ver en tiempo real qué se está vendiendo, cuánto se ha recaudado y qué productos requieren reposición. A la vez, los estudiantes socios pueden consultar su historial de compras desde el portal.</p>
      <div class="integration-tags">
        <span class="badge">Back Office</span>
        <span class="badge">Portal Socios</span>
        <span class="badge">Reportes PDF</span>
      </div>
    </div>
    <div class="integration-image">
      <img src="/images/pos-backoffice-sync.png" alt="Sincronización entre JuCoop POS y Back Office">
    </div>
  </div>
</section>

<section class="section section-gradient">
  <div class="pos-cta-inner">
    <h2>¿Listos para implementar JuCoop POS en tu escuela?</h2>
    <p>Programamos una demostración guiada donde explicamos el flujo completo de trabajo, la configuración de usuarios, gavetas y reportes para tu cooperativa.</p>
    <a href="/contacto" class="primary-btn">Solicitar demostración</a>
  </div>
</section>

</main>

<footer>
  © 2025 JuCoop — Todos los derechos reservados.
</footer>

<script>
document.addEventListener("DOMContentLoaded",()=>{
  const burger=document.getElementById("burgerBtn")
  const mobileMenu=document.getElementById("mobileMenu")
  burger.addEventListener("click",()=>{mobileMenu.classList.toggle("show")})
  const loginButtons=document.querySelectorAll(".login-button")
  loginButtons.forEach(button=>{
    button.addEventListener("click",function(e){
      e.stopPropagation()
      this.parentElement.classList.toggle("show")
    })
  })
  document.addEventListener("click",()=>{
    document.querySelectorAll(".login-container").forEach(c=>c.classList.remove("show"))
  })
})
</script>

</body>
</html>
