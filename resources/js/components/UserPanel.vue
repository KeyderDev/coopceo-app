<template>
    <div class="portal-container">
        <aside class="sidebar">
            <div class="logo">COOPCEO</div>
            <nav>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Perfil</a></li>
                    <li><a href="#">Mensajes</a></li>
                    <li><a href="#">Configuración</a></li>
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
                <div class="user-card" v-if="user">
                    <h3> #{{ user.numero_socio }} - {{ user.nombre }} {{ user.apellido }}</h3>
                    <p>Balance acumulado</p>
                    <span class="balance">${{ user.dividendos }}</span>
                </div>
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
            // Si no hay token, redirigir al login
            window.location.href = "/user-panel/login";
            return;
        }

        try {
            const response = await axios.get("https://coopceo.ddns.net:8000/api/user", {
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
                await axios.post("https://coopceo.ddns.net:8000/api/logout", {}, {
                    headers: { Authorization: `Bearer ${token}` }
                });
            } catch (error) {
                console.warn("Error en logout, pero continuando:", error);
            } finally {
                localStorage.removeItem('auth_token');
                window.location.href = "/user-panel/login";
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
    width: 220px;
    background-color: #044271;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 1rem;
}

.sidebar .logo {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 2rem;
}

.sidebar nav ul {
    list-style: none;
    padding: 0;
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
