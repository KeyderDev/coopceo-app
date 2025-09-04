<template>
    <h1>test</h1>
</template>

<script>
import axios from "axios";

export default {
    name: "AdminPanel",
    data() {
        return {
            user: null,
        };
    },
    async created() {
        const token = localStorage.getItem('auth_token');

        if (!token) {
            if (window.location.pathname !== "/admin-panel/login") {
                window.location.href = "/admin-panel/login";
            }
            return;
        }

        try {
            const response = await axios.get("https://coopceo.ddns.net:8000/api/user", {
                headers: { Authorization: `Bearer ${token}` }
            });
            this.user = response.data;

            if (!this.user.admin) {
                window.location.href = "/";
            }

        } catch (error) {
            console.error("Error al cargar usuario:", error);
            localStorage.removeItem('auth_token');
            if (window.location.pathname !== "/admin-panel/login") {
                window.location.href = "/admin-panel/login";
            }
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
                window.location.href = "/admin-panel/login";
            }
        }
    }
};
</script>
