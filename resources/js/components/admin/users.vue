<template>
    <div>
        <div class="total-users">
            Total de socios registrados: {{ users.length }}
        </div>
        <div class="search-wrapper">
            <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Buscar usuario..."
                class="user-search-input"
            />
        </div>
        <!-- Contenedor de cards -->
        <div class="user-cards-scroll">
            <div v-for="user in filteredUsers" :key="user.id" class="user-card">
                <div class="user-info">
                    <h3>{{ user.nombre }} {{ user.apellido }}</h3>
                    <p><strong>Número socio:</strong> {{ user.numero_socio }}</p>
                    <p><strong>Email:</strong> {{ user.email }}</p>
                </div>
                <div class="user-balance">
                    Balance: ${{ user.dividendos ?? 0 }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            users: [],
            searchQuery: "",
        };
    },
    computed: {
        filteredUsers() {
            if (!this.searchQuery) return this.users;
            const query = this.searchQuery.toLowerCase();
            return this.users.filter(u =>
                u.nombre.toLowerCase().includes(query) ||
                u.apellido.toLowerCase().includes(query) ||
                (u.email && u.email.toLowerCase().includes(query)) ||
                (u.numero_socio && u.numero_socio.toString().includes(query))
            );
        }
    },
    async created() {
        const token = localStorage.getItem('auth_token');
        if (!token) return;

        try {
            const res = await axios.get("https://coopceo.ddns.net:8000/api/users", {
                headers: { Authorization: `Bearer ${token}` }
            });
            this.users = res.data;
        } catch (err) {
            console.error(err);
        }
    }
};
</script>

<style>
/* Contenedor de búsqueda con icono */
/* Contenedor de búsqueda con icono */
.search-wrapper {
    position: relative;
    width: 100%;
    margin-bottom: 1rem;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.1rem;
    color: #044271;
    pointer-events: none;
}

/* Input de búsqueda */
.user-search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem; /* espacio para el icono */
    border: 1px solid #044271;
    border-radius: 12px;
    font-size: 1rem;
    background-color: #97d569;
    color: #044271;
    transition: all 0.3s ease;
}

.user-search-input::placeholder {
    color: #044271;
}

.user-search-input:focus {
    outline: none;
    border-color: #044271;
    box-shadow: 0 0 8px rgba(4, 66, 113, 0.5);
}

.total-users {
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #044271;
    font-size: 1.1rem;
}
</style>
