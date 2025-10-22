<template>
    <div class="portal-container">
        <aside class="sidebar">
            <div class="logo">
                <span>COOPCEO</span>
            </div>
            <nav>
                <ul>
                    <li>
                        <router-link to="/">
                            <i class="fa-solid fa-house"></i>
                            <span>Inicio</span>
                        </router-link>
                    </li>

                    <li>
                        <router-link to="calendar">
                            <i class="fa-solid fa-calendar"></i>
                            <span>Calendario</span>
                        </router-link>
                    </li>

                    <li>
                        <router-link to="users">
                            <i class="fa-solid fa-users"></i>
                            <span>Socios</span>
                        </router-link>
                    </li>

                    <li>
                        <router-link to="email">
                            <i class="fa-solid fa-envelope"></i>
                            <span>Email</span>
                        </router-link>
                    </li>

                    <li class="has-submenu" @click="toggleDropdown('gestion')">
                        <div class="dropdown-toggle">
                            <i class="fa-solid fa-user-tie"></i>
                            <span>Gestion</span>
                            <i class="fa-solid fa-chevron-down chevron"
                                :class="{ open: openDropdown === 'gestion' }"></i>
                        </div>
                        <ul v-show="openDropdown === 'gestion'" class="submenu">
                            <li>
                                <router-link to="horarios">
                                    <i class="fa-solid fa-clock"></i> <span>Horarios</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="inventario">
                                    <i class="fa-solid fa-box"></i>
                                    <span>Inventario</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="documentos">
                                    <i class="fa-solid fa-folder-open"></i> <span>Documentos</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="logs">
                                    <i class="fa-solid fa-scroll"></i>
                                    <span>Logs</span>
                                </router-link>
                            </li>
                        </ul>
                    </li>


                </ul>

                <h2 class="sidebar-section">POS</h2>
                <ul>
                    <li class="has-submenu" @click="toggleDropdown('pos')">
                        <div class="dropdown-toggle">
                            <i class="fa-solid fa-cash-register"></i>
                            <span>Terminal</span>
                            <i class="fa-solid fa-chevron-down chevron" :class="{ open: openDropdown === 'pos' }"></i>
                        </div>
                        <ul v-show="openDropdown === 'pos'" class="submenu">
                            <li>
                                <router-link to="terminal">
                                    <i class="fa-solid fa-cash-register"></i>
                                    <span>Terminal</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="productos">
                                    <i class="fa-solid fa-box"></i>
                                    <span>Productos</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="pos-transactions">
                                    <i class="fa-solid fa-list"></i>
                                    <span>Transacciones</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="cuadres">
                                    <i class="fa-solid fa-building-columns"></i>
                                    <span>Cuadres</span>
                                </router-link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </aside>

        <div class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <h2>{{ user.posicion }}</h2>
                </div>
                <div class="topbar-right">
                    <button @click="logout">Salir</button>
                </div>
            </header>

            <div class="portal">
                <router-view v-slot="{ Component }">
                    <div class="welcome-banner" v-if="!Component && user">
                        <h3>{{ saludo }}, {{ user.nombre }}</h3>
                        <p>{{ currentDateTime }}</p>
                        <p v-if="weather">
                            Clima: {{ weather.icon }} {{ weather.temp }}°F, {{ weather.desc }}
                        </p>
                        <p v-else>
                            Cargando clima...
                        </p>
                    </div>

                    <div v-if="!Component && totalSalesToday !== null" class="sales-summary">
                        Total de ventas hoy: ${{ totalSalesToday.toFixed(2) }}
                    </div>

                    <component v-if="Component" :is="Component" />

                    <div v-else-if="showSalesChart && !Component" class="charts-container">
                        <SalesChart />
                        <WeeklySalesChart />
                    </div>

                </router-view>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import SalesChart from "./SalesChart.vue";
import WeeklySalesChart from "./WeeklySalesChart.vue";
import dayjs from "dayjs";

export default {
    name: "AdminPanel",
    components: { SalesChart, WeeklySalesChart },
    data() {
        return {
            user: null,
            users: [],
            loading: false,
            totalSalesToday: null,
            currentDateTime: "",
            showSalesChart: true,
            openDropdown: null,
        };
    },
    async created() {
        const token = localStorage.getItem('auth_token');

        if (!token) {
            this.redirectToLogin();
            return;
        }

        await this.loadUser(token);
        this.updateDateTime();
        setInterval(this.updateDateTime, 4000);
        await this.loadWeather();
        await this.loadSalesToday(token);
    },

    computed: {
        saludo() {
            const hora = dayjs().hour();
            if (hora >= 5 && hora < 12) return "Buenos días";
            if (hora >= 12 && hora < 19) return "Buenas tardes";
            return "Buenas noches";
        }
    },

    methods: {
        toggleDropdown(menu) {
            this.openDropdown = this.openDropdown === menu ? null : menu;
        },
        updateDateTime() {
            this.currentDateTime = dayjs().format('dddd, D MMMM YYYY HH:mm:ss');
        },
        async loadUser(token) {
            try {
                this.loading = true;
                const response = await axios.get("https://coopceo.ddns.net:8000/api/user", {
                    headers: { Authorization: `Bearer ${token}` }
                });
                this.user = response.data;

                if (!this.user.admin) {
                    window.location.href = "/";
                    return;
                }

                await this.loadUsers(token);
            } catch (error) {
                console.error("Error al cargar usuario:", error);
                this.handleInvalidToken();
            } finally {
                this.loading = false;
            }
        },
        async loadUsers(token) {
            try {
                const responseUsers = await axios.get("https://coopceo.ddns.net:8000/api/users", {
                    headers: { Authorization: `Bearer ${token}` }
                });
                this.users = responseUsers.data;
            } catch (error) {
                console.error("Error al cargar usuarios:", error);
            }
        },
        async logout() {
            const token = localStorage.getItem('auth_token');
            try {
                await axios.post("https://coopceo.ddns.net:8000/api/logout", {}, {
                    headers: { Authorization: `Bearer ${token}` }
                });
            } catch (error) {
                console.warn("Error en logout, pero continuando:", error);
            } finally {
                this.handleInvalidToken();
            }
        },
        async loadSalesToday(token) {
            try {
                const response = await axios.get(
                    "https://coopceo.ddns.net:8000/api/sales",
                    { headers: { Authorization: `Bearer ${token}` } }
                );

                const today = dayjs();
                const salesToday = response.data.filter(sale =>
                    dayjs(sale.created_at).isSame(today, 'day')
                );

                this.totalSalesToday = salesToday.reduce((sum, sale) => sum + parseFloat(sale.total), 0);
            } catch (error) {
                console.error("Error al cargar ventas del día:", error);
                this.totalSalesToday = 0;
            }
        },
        async loadWeather() {
            try {
                const lat = 18.4655;
                const lon = -66.1057;
                const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&temperature_unit=fahrenheit`;
                const response = await axios.get(url);
                const data = response.data.current_weather;
                this.weather = {
                    temp: Math.round(data.temperature),
                    wind: data.windspeed,
                    icon: data.temperature > 80 ? "🌞" : data.temperature > 68 ? "⛅" : "🌥️",
                };
            } catch (error) {
                console.error("Error al cargar clima:", error);
            }
        },
        redirectToLogin() {
            if (window.location.pathname !== "/admin-panel/login") {
                window.location.href = "/admin-panel/login";
            }
        },
        handleInvalidToken() {
            localStorage.removeItem('auth_token');
            this.redirectToLogin();
        }
    }
};
</script>

<style>
.portal-container {
    display: flex;
    flex-wrap: wrap;
    min-height: 100vh;
    font-family: Arial, sans-serif;
}

.user-position {
    font-size: 0.9rem;
    color: #aaa;
    margin-top: -0.4rem;
    margin-bottom: 0.8rem;
    font-style: italic;
}

.charts-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: stretch;
    gap: 2rem;
    margin-top: 1.5rem;
}

.charts-container > * {
    flex: 1 1 45%;
    min-width: 300px;
    max-width: 600px;
}

.sidebar {
    width: 240px;
    background: linear-gradient(180deg, #044271 0%, #03345a 100%);
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 1.5rem 1rem;
    flex-shrink: 0;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
}

.welcome-banner {
    background: linear-gradient(135deg, #97d569 0%, #5aa832 100%);
    color: #fff;
    padding: 1.5rem 2rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
    animation: fadeIn 1s ease-out;
}

.welcome-banner h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.welcome-banner p {
    margin: 0;
    font-size: 1rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sales-summary {
    background-color: #f1f9f2;
    color: #044271;
    padding: 1rem 2rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
    font-weight: bold;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.welcome-banner::before {
    position: absolute;
    top: -10px;
    right: -10px;
    font-size: 3rem;
    opacity: 0.3;
    transform: rotate(25deg);
}

.welcome-banner::after {
    position: absolute;
    bottom: -10px;
    left: -10px;
    font-size: 2.5rem;
    opacity: 0.25;
    transform: rotate(-15deg);
}

@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateY(-15px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.sidebar .logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.4rem;
    font-weight: bold;
    margin-bottom: 2rem;
    color: #97d569;
}

.sidebar nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar nav ul li {
    margin-bottom: 0.6rem;
}

.sidebar nav ul li a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.7rem 1rem;
    border-radius: 8px;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sidebar nav ul li a:hover {
    background-color: rgba(151, 213, 105, 0.15);
    color: #97d569;
    transform: translateX(4px);
}

.sidebar nav ul li a i {
    width: 20px;
    text-align: center;
}

.sidebar-section {
    margin: 1.5rem 0 0.5rem;
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: 1px;
    color: #97d569;
    text-transform: uppercase;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 1rem;
}

.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.topbar {
    height: 60px;
    background-color: #97d569;
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

.portal {
    flex: 1;
    padding: 1.5rem;
    background-color: #f4f7f9;
    color: #044271;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.user-cards-scroll {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    overflow-y: auto;
    max-height: calc(100vh - 120px);
    padding-right: 0.5rem;
}

.user-card {
    background-color: #fff;
    border-left: 5px solid #97d569;
    padding: 1.25rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.user-info h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: bold;
}

.user-info p {
    margin: 0.25rem 0;
    color: #666;
}

.user-balance {
    font-size: 1.25rem;
    font-weight: bold;
    color: #044271;
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
        padding: 0.5rem;
        align-items: center;
    }

    .sidebar .logo {
        margin-bottom: 0;
        margin-right: 1rem;
        font-size: 1.2rem;
        white-space: nowrap;
    }

    .sidebar nav ul {
        display: flex;
        gap: 1rem;
    }

    .sidebar nav ul li {
        margin-bottom: 0;
    }

    .topbar {
        flex-direction: column;
        height: auto;
        padding: 1rem;
        text-align: center;
        gap: 0.5rem;
    }

    .topbar-left h2 {
        font-size: 1.2rem;
    }

    .topbar-right button {
        width: 100%;
        max-width: 200px;
    }

    .portal {
        padding: 1rem;
    }

    .user-cards-scroll {
        max-height: none;
        gap: 0.75rem;
    }

    .charts-container {
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
    }

    .charts-container > * {
        width: 100%;
        max-width: none;
    }

    .user-card {
        flex-direction: column;
        align-items: flex-start;
    }

    .user-balance {
        margin-top: 0.5rem;
    }
}

.has-submenu .dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.7rem 1rem;
    border-radius: 8px;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.has-submenu .dropdown-toggle:hover {
    background-color: rgba(151, 213, 105, 0.15);
    color: #97d569;
}

.submenu {
    margin-top: 0.3rem;
    border-left: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    overflow: hidden;
    animation: fadeInSubmenu 0.3s ease;
}

.submenu li a {
    display: flex;
    align-items: center;
    color: #ddd;
    font-size: 0.9rem;
}

.submenu li a:hover {
    color: #97d569;
    background-color: rgba(255, 255, 255, 0.1);
}

.chevron {
    transition: transform 0.3s ease;
}

.chevron.open {
    transform: rotate(180deg);
}

@keyframes fadeInSubmenu {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
