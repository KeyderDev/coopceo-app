<template>
  <div class="usuarios-container">
    <!-- 🔍 Buscador -->
    <div class="search-section">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Buscar usuario..."
          class="search-input"
        />
      </div>

      <div class="filter-section">
        <label for="filter">Ordenar por:</label>
        <select id="filter" v-model="selectedFilter" class="filter-select">
          <option value="default">Predeterminado</option>
          <option value="administradores">Administradores</option>
          <option value="numeroSocio">Número de socio</option>
          <option value="balance">Balance</option>
          <option value="recent">Recientes</option>
          <option value="alphabetical">Orden alfabético</option>
        </select>
      </div>
    </div>

    <!-- 🧮 Total -->
    <div class="total-users">
      Total de socios registrados: <strong>{{ users.length }}</strong>
    </div>

    <!-- 👥 Listado -->
    <div class="users-list">
      <div v-for="user in filteredUsers" :key="user.id" class="user-card">
        <div class="user-main">
          <div class="user-info">
            <h3>{{ user.nombre }} {{ user.apellido }}</h3>
            <p><strong>Número socio:</strong> {{ user.numero_socio }}</p>
            <p><strong>Teléfono:</strong> {{ user.telefono || '—' }}</p>
            <p><strong>Email:</strong> {{ user.email }}</p>
          </div>
          <div class="user-balance">
            <span>Balance</span>
            <p class="amount">${{ user.dividendos ?? 0 }}</p>
          </div>
        </div>
        <button class="delete-btn" @click="deleteUser(user.id)">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>
    </div>

  </div>
      <button class="btn-volver" @click="volverMenu">
      <i class="fa-solid fa-house"></i> Menú Principal
    </button>
</template>

<script>
import axios from "axios";

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
      const query = this.searchQuery.toLowerCase();

      if (query) {
        result = result.filter(
          (u) =>
            u.nombre?.toLowerCase().includes(query) ||
            u.apellido?.toLowerCase().includes(query) ||
            u.email?.toLowerCase().includes(query) ||
            u.numero_socio?.toString().includes(query)
        );
      }

      switch (this.selectedFilter) {
        case "balance":
          result.sort((a, b) => (b.dividendos ?? 0) - (a.dividendos ?? 0));
          break;
        case "alphabetical":
          result.sort((a, b) =>
            (a.nombre + " " + a.apellido).localeCompare(
              b.nombre + " " + b.apellido
            )
          );
          break;
        case "recent":
          result.sort((a, b) =>
            a.created_at && b.created_at
              ? new Date(b.created_at) - new Date(a.created_at)
              : b.id - a.id
          );
          break;
        case "numeroSocio":
          result.sort(
            (a, b) => (b.numero_socio ?? 0) - (a.numero_socio ?? 0)
          );
          break;
        case "administradores":
          result = result.filter((u) => u.admin === 1);
          break;
      }

      return result;
    },
  },
  methods: {
    async deleteUser(userId) {
      if (!confirm("¿Seguro que deseas eliminar este usuario?")) return;
      const token = localStorage.getItem("auth_token");
      if (!token) return;

      try {
        await axios.delete(
          `${import.meta.env.VITE_APP_URL}/api/users/${userId}`,
          { headers: { Authorization: `Bearer ${token}` } }
        );
        this.users = this.users.filter((u) => u.id !== userId);
      } catch (err) {
        console.error(err);
        alert("Error al eliminar usuario");
      }
    },
    volverMenu() {
      this.$router.push("/");
    },
  },
  async created() {
    const token = localStorage.getItem("auth_token");
    if (!token) return;

    try {
      const res = await axios.get(`${import.meta.env.VITE_APP_URL}/api/users`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.users = res.data;
    } catch (err) {
      console.error(err);
    }
  },
};
</script>

<style scoped>
.usuarios-container {
  width: 100%;
  max-width: 1500px;
  margin: 2rem auto;
  background: #f9fafb;
  padding: 2.5rem 3rem;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  min-height: 90vh;
}

.search-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
  flex-wrap: wrap;
  margin-bottom: 2rem;
}

.search-box {
  flex: 1;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #044271;
  font-size: 1.2rem;
}

.search-input {
  width: 100%;
  padding: 0.9rem 1rem 0.9rem 3rem;
  border-radius: 10px;
  border: 1px solid #044271;
  background: #e8f6dc;
  font-size: 1rem;
  color: #044271;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  box-shadow: 0 0 8px rgba(4, 66, 113, 0.3);
}

.filter-section {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  font-weight: 600;
  color: #044271;
}

.filter-select {
  padding: 0.6rem 1rem;
  border-radius: 8px;
  border: 1px solid #044271;
  background: #97d569;
  color: #044271;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:hover {
  background: #a8e07b;
}

/* 🧮 Total usuarios */
.total-users {
  font-size: 1.2rem;
  font-weight: 600;
  color: #044271;
  text-align: right;
  margin-bottom: 1.5rem;
}

/* 👥 Cards */
.users-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
  gap: 1.5rem;
}

.user-card {
  background: #fff;
  border-left: 6px solid #97d569;
  border-radius: 12px;
  padding: 1.5rem;
  position: relative;
  transition: all 0.2s ease;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}

.user-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.user-main {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  flex-wrap: wrap;
}

.user-info h3 {
  color: #044271;
  font-size: 1.4rem;
  margin-bottom: 0.4rem;
}

.user-info p {
  color: #555;
  margin: 0.2rem 0;
}

.user-balance {
  text-align: right;
}

.user-balance .amount {
  font-size: 1.5rem;
  color: #044271;
  font-weight: bold;
}

/* 🗑️ Eliminar */
.delete-btn {
  position: absolute;
  bottom: 15px;
  right: 18px;
  background: transparent;
  border: none;
  color: #dc2626;
  font-size: 1.3rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  color: #b91c1c;
  transform: scale(1.2);
}

/* 🏠 Volver */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 30px;
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 0.8rem 1.3rem;
  border-radius: 12px;
  font-weight: bold;
  font-size: 1rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  cursor: pointer;
}

.btn-volver:hover {
  background-color: #43a047;
}
</style>
