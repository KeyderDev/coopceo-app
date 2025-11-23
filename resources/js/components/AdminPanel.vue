<template>
    <div id="app">
        <header class="topbar">
            <div class="topbar-left">
                <h2><i class="fa-solid fa-user-shield"></i>JuCoop Back Office</h2>

            </div>
            <div class="topbar-right">
                <button @click="volverMenu"><i class="fa-solid fa-house"></i> Menu </button>

                <button @click="logout"><i class="fa-solid fa-right-from-bracket"></i> Salir</button>
            </div>
        </header>
        <div class="marquee">
            <span>🎄 ¡Felices fiestas les desea la Coopceo! 🎁</span>
        </div>

        <main class="main-content">
            <div v-if="$route.path === '/' || $route.path === '/admin-panel'" class="menu-grid">
                <router-link to="/management" class="menu-item">
                    <i class="fa-solid fa-gear"></i>
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
            user: {},
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
        setInterval(this.updateDateTime, 1000);
        await this.loadSalesToday(token);
    },
    methods: {
        async loadUser(token) {
            try {
                const res = await axios.get(`${import.meta.env.VITE_APP_URL}/api/user`, {
                    headers: { Authorization: `Bearer ${token}` },
                });
                this.user = res.data;
            } catch {
                this.handleInvalidToken();
            }
        },
        volverMenu() {
            this.$router.push("/");
        },
        async loadSalesToday(token) {
            try {
                const res = await axios.get(`${import.meta.env.VITE_APP_URL}/api/sales`, {
                    headers: { Authorization: `Bearer ${token}` },
                });
                const today = dayjs();
                const salesToday = res.data.filter((s) => dayjs(s.created_at).isSame(today, "day"));
                this.totalSalesToday = salesToday.reduce((sum, s) => sum + parseFloat(s.total || 0), 0);
            } catch (e) {
                console.error("Error al cargar ventas:", e);
            }
        },
        logout() {
            const token = localStorage.getItem("auth_token");
            axios
                .post(`${import.meta.env.VITE_APP_URL}/api/logout`, {}, { headers: { Authorization: `Bearer ${token}` } })
                .finally(() => this.handleInvalidToken());
        },
        updateDateTime() {
            const now = dayjs();
            this.currentDate = now.format("dddd, D MMMM YYYY");
            this.currentTime = now.format("HH:mm:ss");
        },
        formatCurrency(value) {
            return value.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        redirectToLogin() {
            localStorage.removeItem("auth_token");
            window.location.href = "/admin-panel/login";
        },
        handleInvalidToken() {
            localStorage.removeItem("auth_token");
            this.redirectToLogin();
        },
    },
};
</script>

<style scoped>
#app {
    font-family: "Inter", sans-serif;
    background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
    color: #fff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
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

.topbar {
    height: 70px;
    background: rgba(157, 216, 106, 0.08);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    flex-shrink: 0;
}

.topbar h2 {
    font-weight: 600;
    color: #9dd86a;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.topbar button {
    background: linear-gradient(135deg, #9dd86a, #7ab55c);
    color: #fff;
    border: none;
    padding: 0.6rem 1.3rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.topbar button:hover {
    background: linear-gradient(135deg, #b9f089, #91d46d);
    transform: translateY(-2px);
}

.topbar-right {
    display: flex;
    gap: 0.8rem;
    align-items: center;
}

.main-content {
    flex: 1;
    background: rgba(20, 22, 25, 0.7);
    backdrop-filter: blur(10px);
    padding: 2rem;
    overflow-y: auto;
    box-sizing: border-box;
    animation: fadeIn 0.6s ease-in-out;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.5rem;
    max-width: 900px;
    margin: 0 auto;
}

.menu-item {
    background: rgba(157, 216, 106, 0.1);
    border: 1px solid rgba(157, 216, 106, 0.15);
    border-radius: 15px;
    color: #fff;
    text-decoration: none;
    text-align: center;
    padding: 2rem 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.menu-item:hover {
    background: rgba(157, 216, 106, 0.2);
    transform: translateY(-6px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.35);
}

.menu-item i {
    font-size: 2.5rem;
    color: #9dd86a;
    margin-bottom: 0.7rem;
}

.menu-item p {
    font-weight: 600;
    font-size: 1rem;
}

.child-component {
    width: 100%;
    background: rgba(25, 27, 31, 0.85);
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    padding: 2rem;
    animation: fadeIn 0.6s ease-in-out;
}

.footer {
    background: rgba(25, 27, 31, 0.8);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: #ccc;
    font-weight: 500;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 2rem;
    height: 60px;
    backdrop-filter: blur(8px);
    flex-wrap: wrap;
    gap: 0.8rem;
}

.footer i {
    color: #9dd86a;
}

.footer-left,
.footer-center,
.footer-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
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
    .topbar {
        flex-direction: column;
        padding: 1rem;
        gap: 0.5rem;
        height: auto;
        text-align: center;
    }

    .main-content {
        padding: 1.2rem;
    }

    .menu-grid {
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    }

    .menu-item {
        padding: 1.5rem 0.5rem;
    }

    .child-component {
        width: 100vw;
        max-width: 100%;
        border-radius: 0;
        padding: 0.6rem;
        margin: 0;
        background: transparent;
        box-shadow: none;
    }

    .main-content {
        padding: 0;
    }

    .footer {
        flex-direction: column;
        text-align: center;
        height: auto;
        padding: 1rem;
    }
}
</style>
