<template>
    <div class="portal-container">
        <aside class="sidebar">
            <div class="logo">COOPCEO</div>
            <nav>
                <ul>
                    <li><router-link to="/">Inicio</router-link></li>
                    <li><router-link to="users">Socios</router-link></li>
                    <li><a href="#">Mensajes</a></li>
                    <li><a href="#">Configuración</a></li>
                </ul>
            </nav>
        </aside>

        <div class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <h2>Panel de Administración</h2>
                </div>
                <div class="topbar-right">
                    <button @click="logout">Salir</button>
                </div>
            </header>

            <div class="portal">
                <router-view></router-view>
            </div>

        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "AdminPanel",
    data() {
        return {
            user: null,
            users: [],
            loading: false,
        };
    },
    async created() {
        const token = localStorage.getItem('auth_token');

        if (!token) {
            this.redirectToLogin();
            return;
        }

        await this.loadUser(token);
    },
    methods: {
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

.sidebar {
    width: 220px;
    background-color: #044271;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 1rem;
    flex-shrink: 0;
}

.sidebar .logo {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 2rem;
}

.sidebar nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar nav ul li {
    margin-bottom: 1rem;
}

.sidebar nav ul li a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.sidebar nav ul li a:hover {
    color: #97d569;
}

/* Contenido principal */
.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
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

/* Contenedor del portal */
.portal {
    flex: 1;
    padding: 1.5rem;
    background-color: #f4f7f9;
    color: #044271;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

/* Scroll vertical para las cards */
.user-cards-scroll {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    overflow-y: auto;
    max-height: calc(100vh - 120px);
    padding-right: 0.5rem;
}

/* Card de usuario estilo lista */
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

/* Responsive */
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

    .user-card {
        flex-direction: column;
        align-items: flex-start;
    }

    .user-balance {
        margin-top: 0.5rem;
    }
}
</style>
