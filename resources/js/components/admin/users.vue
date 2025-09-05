<template>
    <div>
        <!-- Barra de búsqueda -->
        <div class="search-wrapper">
            <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" v-model="searchQuery" placeholder="Buscar usuario..." class="user-search-input" />
        </div>

        <!-- Contenedor del filtro + total -->
        <div class="filter-total-container">
            <div class="filter-wrapper">
                <label for="filter">Ordenar por:</label>
                <select id="filter" v-model="selectedFilter" class="filter-select">
                    <option value="default">Predeterminado</option>
                    <option value="balance">Balance</option>
                    <option value="recent">Recientes</option>
                    <option value="alphabetical">Orden alfabético</option>
                </select>
            </div>

            <div class="total-users">
                Total de socios registrados: {{ users.length }}
            </div>
        </div>

        <!-- Cards -->
        <div class="user-cards-scroll">
            <div v-for="user in filteredUsers" :key="user.id" class="user-card">
                <div class="user-info">
                    <h3>{{ user.nombre }} {{ user.apellido }}</h3>
                    <p><strong>Número socio:</strong> {{ user.numero_socio }}</p>
                    <p><strong>Número de Telefono:</strong> {{ user.telefono }}</p>
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
            selectedFilter: "default",
        };
    },
    computed: {
        filteredUsers() {
            let result = [...this.users];

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                result = result.filter(u =>
                    u.nombre.toLowerCase().includes(query) ||
                    u.apellido.toLowerCase().includes(query) ||
                    (u.email && u.email.toLowerCase().includes(query)) ||
                    (u.numero_socio && u.numero_socio.toString().includes(query))
                );
            }

            if (this.selectedFilter === "balance") {
                result.sort((a, b) => (b.dividendos ?? 0) - (a.dividendos ?? 0));
            } else if (this.selectedFilter === "alphabetical") {
                result.sort((a, b) =>
                    (a.nombre + " " + a.apellido).localeCompare(b.nombre + " " + b.apellido)
                );
            } else if (this.selectedFilter === "recent") {
                // Si tienes created_at, usa eso:
                if (result.length && result[0].created_at) {
                    result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                } else {
                    // fallback con id (los más altos son más recientes)
                    result.sort((a, b) => b.id - a.id);
                }
            }

            return result;
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
    padding: 0.75rem 1rem 0.75rem 2.5rem;
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

.filter-total-container {
    display: flex;
    justify-content: space-between;
    /* uno a la izq (filtro) y otro a la der (total) */
    align-items: center;
    margin-bottom: 1rem;
}

.total-users {
    font-weight: bold;
    color: #044271;
    font-size: 1.1rem;
}

/* Estilos del filtro */
.filter-wrapper {
    margin-bottom: 1rem;
    color: #044271;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-select {
    border: 1px solid #044271;
    border-radius: 8px;
    padding: 0.5rem;
    background-color: #97d569;
    color: #044271;
    font-size: 1rem;
    cursor: pointer;
}
</style>
