<template>
    <div id="app">
        <header class="topbar">
            <div class="topbar-left">
                <h2>{{ user.posicion }}</h2>
            </div>
            <div class="topbar-right">
                <button @click="logout">Salir</button>
            </div>
        </header>

        <main class="main-content">
            <!-- Si no hay ruta hija activa, muestra el grid -->
            <div v-if="$route.path === '/' || $route.path === '/admin-panel'" class="menu-grid">
                <router-link to="/management" class="menu-item"> <i class="fa-solid fa-gear"></i>
                    <p>Management</p>
                </router-link>

                <router-link to="/inventory" class="menu-item">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Inventory</p>
                </router-link>
                <router-link to="/users" class="menu-item">
                    <i class="fa-solid fa-user"></i>
                    <p>Users</p>
                </router-link>
                <router-link to="/ganancias" class="menu-item">
                    <i class="fas fa-chart-line"></i>
                    <p>Sales</p>
                </router-link>

                <router-link to="/terminal" class="menu-item">
                    <i class="fa-solid fa-cash-register"></i>
                    <p>Terminal</p>
                </router-link>

                <router-link to="/documentos" class="menu-item">
                    <i class="fas fa-file-alt"></i>
                    <p>Reports</p>
                </router-link>
            </div>

            <!-- Si hay una ruta hija activa, muestra el componente -->
            <div v-else class="child-component">
                <router-view />
            </div>
        </main>


        <footer class="footer">
            <div class="footer-left">
                <i class="fas fa-calendar-day"></i>
                <span>{{ currentDate }}</span>
            </div>

            <div class="footer-center">
                <i class="fas fa-clock"></i>
                <span>{{ currentTime }}</span>
            </div>

            <div class="footer-right">
                <i class="fas fa-dollar-sign"></i>
                <span>Total ventas hoy: ${{ formatCurrency(totalSalesToday) }}</span>
            </div>
        </footer>

    </div>
</template>

<script>
import axios from "axios";
import dayjs from "dayjs";

export default {
    name: "AdminPanel",
    data() {
        return {
            user: null,
            users: [],
            totalSalesToday: 0,
            currentDate: "",
            currentTime: "",
        };
    },
    async created() {
        const token = localStorage.getItem("auth_token");
        if (!token) return this.redirectToLogin();

        await this.loadUser(token);
        this.updateDateTime();
        setInterval(this.updateDateTime, 1000); // actualiza la hora cada segundo
        await this.loadSalesToday(token);
    },
    methods: {
        async loadUser(token) {
            try {
                const response = await axios.get(`${import.meta.env.VITE_APP_URL}/api/user`, {
                    headers: { Authorization: `Bearer ${token}` },
                });
                this.user = response.data;
            } catch (error) {
                this.handleInvalidToken();
            }
        },
        async loadSalesToday(token) {
            try {
                const response = await axios.get(`${import.meta.env.VITE_APP_URL}/api/sales`, {
                    headers: { Authorization: `Bearer ${token}` },
                });
                const today = dayjs();
                const salesToday = response.data.filter((sale) =>
                    dayjs(sale.created_at).isSame(today, "day")
                );
                this.totalSalesToday = salesToday.reduce(
                    (sum, sale) => sum + parseFloat(sale.total || 0),
                    0
                );
            } catch (error) {
                console.error("Error al cargar ventas del día:", error);
            }
        },
        async logout() {
            const token = localStorage.getItem('auth_token');
            try {
                await axios.post(`${import.meta.env.VITE_APP_URL}/api/logout`, {}, {
                    headers: { Authorization: `Bearer ${token}` }
                });
            } catch (error) {
                console.warn("Error en logout, pero continuando:", error);
            } finally {
                this.handleInvalidToken();
            }
        },
        updateDateTime() {
            const now = dayjs();
            this.currentDate = now.format("dddd, D MMMM YYYY");
            this.currentTime = now.format("HH:mm:ss");
        },
        formatCurrency(value) {
            return value.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },
        redirectToLogin() {
            localStorage.removeItem("auth_token");
            window.location.href = "/admin-panel/login";
        },
        handleInvalidToken() {
            localStorage.removeItem('auth_token');
            this.redirectToLogin();
        }
    },
};
</script>


<style scoped>
/* === ESTILOS GENERALES === */
#app {
    font-family: Arial, sans-serif;
    background-color: #e0e0e0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* === BARRA SUPERIOR === */
.topbar {
    height: 60px;
    background-color: #4caf50;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1.5rem;
    color: #044271;
    font-weight: bold;
    flex-shrink: 0;
}

.topbar button {
    background-color: #044271;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.topbar button:hover {
    background-color: #03345a;
}


.main-content {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    /* Centra el grid en el medio */
    background-color: #e0e0e0;
    min-height: calc(100vh - 120px);
    /* ajusta según header+footer */
    overflow: hidden;
    /* quita el scroll */
}


/* === GRID DEL MENÚ PRINCIPAL === */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    grid-gap: 40px;
    justify-content: center;
    align-content: center;
    /* Centra verticalmente el grid */
    height: 100%;
    max-width: 700px;
    width: 90%;
}

/* === ITEMS DEL MENÚ === */
.menu-item {
    background-color: #4caf50;
    border-radius: 10px;
    height: 150px;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
    text-decoration: none;
}

.menu-item:hover {
    background-color: #45a049;
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
}

.menu-item i {
    font-size: 38px;
    margin-bottom: 12px;
}

.menu-item p {
    font-weight: bold;
    font-size: 1rem;
    text-align: center;
}

/* === CONTENIDO DE COMPONENTES HIJOS === */
.child-component {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 2rem;
    box-sizing: border-box;
}

/* === FOOTER (SI LO REACTIVAS EN EL FUTURO) === */
.footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #e0e0e0;
  padding: 10px 30px;
  height: 60px;
  border-top: 1px solid #ccc;
  color: #044271;
  font-weight: 600;
  flex-wrap: wrap;
  gap: 10px;
  box-sizing: border-box;
}

.footer-left,
.footer-center,
.footer-right {
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.footer i {
  font-size: 18px;
}

/* === 📱 Estilos Responsivos === */
@media (max-width: 768px) {
  .footer {
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: auto;
    padding: 15px;
    gap: 8px;
    text-align: center;
  }

  .footer-left,
  .footer-center,
  .footer-right {
    justify-content: center;
    width: 100%;
    font-size: 14px;
  }

  .footer i {
    font-size: 16px;
  }

  .footer span {
    font-size: 14px;
  }
}

@media (max-width: 480px) {
  .footer {
    padding: 12px;
    gap: 6px;
  }

  .footer span {
    font-size: 13px;
  }

  .footer i {
    font-size: 15px;
  }
}

.btn {
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px 16px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn:hover {
    background-color: #f5f5f5;
}

.workflow {
    display: flex;
    align-items: center;
    gap: 5px;
}

.workflow input {
    width: 16px;
    height: 16px;
}
</style>
