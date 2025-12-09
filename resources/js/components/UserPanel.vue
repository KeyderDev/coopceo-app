<template>
  <div class="portal-container">
    <!-- 🧭 Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <i class="fa-solid fa-building-columns"></i> COOPCEO
      </div>
      <nav>
        <ul>
          <li>
            <router-link to="/" exact>
              <i class="fa-solid fa-house"></i>
              <span>Inicio</span>
            </router-link>
          </li>
          <li>
            <router-link to="transactions">
              <i class="fa-solid fa-scroll"></i>
              <span>Mis Transacciones</span>
            </router-link>
          </li>
          <li>
            <router-link to="menu">
              <i class="fa-solid fa-utensils"></i>
              <span>Menu</span>
            </router-link>
          </li>
          <li>
            <router-link to="reviews">
              <i class="fa-solid fa-comment"></i>
                <span>Reseñas</span>
            </router-link>
          </li>
          <li>
            <a href="#">
              <i class="fa-solid fa-gear"></i>
              <span>Configuración</span>
            </a>
          </li>
        </ul>
      </nav>
    </aside>

    <div class="main-content">
      <header class="topbar">
        <div class="topbar-left">
          <h2><i class="fa-solid fa-user"></i> Panel de Usuario</h2>
        </div>
        <div class="topbar-right">
          <button @click="logout"><i class="fa-solid fa-right-from-bracket"></i> Salir</button>
        </div>
      </header>
      <div class="marquee">
        <span>🎄 ¡Felices fiestas les desea la Coopceo! 🎁</span>
      </div>
      <div class="portal">
        <router-view v-slot="{ Component }">
          <div class="user-card" v-if="!Component && user">
            <div class="avatar">
              <i class="fa-solid fa-user-circle"></i>
            </div>
            <div class="info">
              <h3>#{{ user.numero_socio }} - {{ user.nombre }} {{ user.apellido }}</h3>
              <p>Balance acumulado</p>
              <span class="balance">${{ user.dividendos }}</span>
            </div>
          </div>

          <component :is="Component" v-else />
        </router-view>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "UserPanel",
  data() {
    return { user: null };
  },
  async created() {
    const token = localStorage.getItem("auth_token");
    if (!token) {
      window.location.href = "/user-panel/login";
      return;
    }

    try {
      const response = await axios.get(`${import.meta.env.VITE_APP_URL}/api/user`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.user = response.data;
    } catch (error) {
      console.error("Error al cargar usuario:", error);
      localStorage.removeItem("auth_token");
      window.location.href = "/user-panel/login";
    }
  },
  methods: {
    async logout() {
      const token = localStorage.getItem("auth_token");
      try {
        await axios.post(`${import.meta.env.VITE_APP_URL}/api/logout`, {}, {
          headers: { Authorization: `Bearer ${token}` },
        });
      } catch (error) {
        console.warn("Error en logout:", error);
      } finally {
        localStorage.removeItem("auth_token");
        window.location.href = "/user-panel/login";
      }
    },
  },
};
</script>

<style scoped>
.portal-container {
  display: flex;
  min-height: 100vh;
  height: auto;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  color: #fff;
  font-family: "Inter", sans-serif;
}

.marquee {
  width: 100%;
  overflow: hidden;
  white-space: nowrap;
  background: black;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  padding: 0.5rem 0;
  backdrop-filter: blur(6px);
}

.marquee span {
  display: inline-block;
  padding-left: 100%;
  font-size: 1.1rem;
  font-weight: 600;
  color: #9dd86a;
  animation: marqueeMove 12s linear infinite;
}

@keyframes marqueeMove {
  0% {
    transform: translateX(0);
  }

  100% {
    transform: translateX(-100%);
  }
}

.sidebar {
  width: 250px;
  background: rgba(25, 27, 31, 0.75);
  backdrop-filter: blur(12px);
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  flex-direction: column;
  padding: 1.5rem 1rem;
  box-shadow: 4px 0 10px rgba(0, 0, 0, 0.25);
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.logo {
  font-size: 1.4rem;
  font-weight: 700;
  color: #9dd86a;
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 2rem;
}

.sidebar nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar nav ul li {
  margin-bottom: 0.9rem;
}

.sidebar nav ul li a {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  padding: 0.7rem 1rem;
  border-radius: 10px;
  color: #ccc;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.sidebar nav ul li a:hover {
  background-color: rgba(157, 216, 106, 0.1);
  color: #9dd86a;
  transform: translateX(4px);
}

.sidebar nav ul li a.router-link-active {
  background-color: rgba(157, 216, 106, 0.15);
  color: #9dd86a;
  font-weight: 600;
}

.sidebar i {
  width: 20px;
  text-align: center;
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 0;
}

.topbar {
  height: 65px;
  background: rgba(157, 216, 106, 0.1);
  backdrop-filter: blur(12px);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  flex-shrink: 0;
}

.topbar h2 {
  font-weight: 600;
  font-size: 1.3rem;
  color: #9dd86a;
}

.topbar button {
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  border: none;
  padding: 0.55rem 1.2rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.topbar button:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-2px);
}

.portal {
  flex: 1;
  padding: 2rem;
  color: #fff;
  background: rgba(20, 22, 25, 0.65);
  backdrop-filter: blur(8px);
  overflow-y: auto;
  min-height: 0;
  box-sizing: border-box;
}

.user-card {
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-left: 5px solid #9dd86a;
  border-radius: 12px;
  padding: 1.8rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
  display: flex;
  align-items: center;
  gap: 1.2rem;
  animation: fadeIn 0.6s ease-in-out;
}

.user-card .avatar i {
  font-size: 3rem;
  color: #9dd86a;
}

.user-card .info h3 {
  font-size: 1.25rem;
  margin: 0;
  color: #fff;
}

.user-card .info p {
  color: #ccc;
  margin: 0.3rem 0;
}

.user-card .balance {
  font-size: 1.6rem;
  font-weight: 700;
  color: #9dd86a;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .portal-container {
    flex-direction: column;
    height: auto;
  }

  .sidebar {
    width: 100%;
    flex-direction: row;
    overflow-x: auto;
    overflow-y: hidden;
    justify-content: space-between;
    padding: 0.8rem 1rem;
    gap: 1rem;
  }

  .logo {
    margin-bottom: 0;
    font-size: 1.1rem;
  }

  .sidebar nav ul {
    display: flex;
    gap: 0.6rem;
  }

  .sidebar nav ul li {
    margin-bottom: 0;
  }

  .sidebar nav ul li a {
    padding: 0.5rem 0.8rem;
    font-size: 0.85rem;
  }

  .topbar {
    flex-direction: column;
    height: auto;
    gap: 0.5rem;
    text-align: center;
    padding: 0.8rem 1rem;
  }

  .marquee {
    font-size: 0.9rem;
  }

  .portal {
    padding: 1.2rem;
    overflow-y: visible;
  }

  .user-card {
    flex-direction: column;
    text-align: center;
  }
}
</style>
