<template>
    <div class="portal-container">
        <aside class="sidebar">
            <div class="logo">COOPCEO</div>
            <nav>
                <ul>
                    <li>
                        <router-link to="/">
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
                    <h2>Panel de Usuario</h2>
                </div>
                <div class="topbar-right">
                    <button @click="logout">Salir</button>
                </div>
            </header>

            <div class="portal">
                <router-view v-slot="{ Component }">
                    <!-- Si NO hay un componente cargado, mostrar user-card -->
                    <div class="user-card" v-if="!Component && user">
                        <h3>#{{ user.numero_socio }} - {{ user.nombre }} {{ user.apellido }}</h3>
                        <p>Balance acumulado</p>
                        <span class="balance">${{ user.dividendos }}</span>
                    </div>

                    <!-- Si hay un componente cargado, renderízalo -->
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
        return {
            user: null,
        };
    },
    async created() {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            window.location.href = "/user-panel/login";
            return;
        }

        try {
            const response = await axios.get(`${import.meta.env.VITE_APP_URL}/api/user`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            this.user = response.data;
        } catch (error) {
            console.error("Error al cargar usuario:", error);
            localStorage.removeItem('auth_token');
            window.location.href = "/user-panel/login";
        }
    },
    methods: {
        async logout() {
            const token = localStorage.getItem('auth_token');
            try {
                await axios.post(`${import.meta.env.VITE_APP_URL}/api/logout`, {}, {
                    headers: { Authorization: `Bearer ${token}` }
                });
            } catch (error) {
                console.warn("Error en logout, pero continuando:", error);
            } finally {
                localStorage.removeItem('auth_token');
                window.location.href = "/user-panel/login"; P
            }
        }
    }
};
</script>



<style scoped>
.portal-container {
    display: flex;
    height: 100vh;
    font-family: Arial, sans-serif;
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
    margin-bottom: 0.8rem;
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

/* Contenido principal */
.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Navbar horizontal */
.topbar {
    height: 60px;
    background-color: #97d569;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1.5rem;
    color: #044271;
    font-weight: bold;
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

/* Contenido del portal */
.portal {
    flex: 1;
    padding: 2rem;
    background-color: #f4f7f9;
    color: #044271;
    text-align: left;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* Card de usuario */
.user-card {
    background-color: #fff;
    border-left: 5px solid #97d569;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    max-width: 100%;
}

.user-card h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: bold;
}

.user-card p {
    margin: 0.5rem 0 0;
    color: #666;
}

.user-card .balance {
    display: block;
    margin-top: 1rem;
    font-size: 1.5rem;
    font-weight: bold;
    color: #044271;
}

/* Ajustes responsive */
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
    }

    .sidebar .logo {
        margin-bottom: 0;
        margin-right: 1rem;
        font-size: 1.2rem;
    }

    .sidebar nav ul {
        display: flex;
        gap: 1rem;
    }

    .sidebar nav ul li {
        margin-bottom: 0;
    }

    .main-content {
        flex: 1;
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
        gap: 1rem;
    }

    .user-card h3 {
        font-size: 1rem;
    }

    .user-card .balance {
        font-size: 1.25rem;
    }
}
</style>
