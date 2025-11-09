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

        <div class="user-actions">
          <button class="manage-btn" @click="goToUser(user.id)">
            <i class="fa-solid fa-gear"></i> Administrar
          </button>
          <button class="delete-btn" @click="deleteUser(user.id)">
            <i class="fa-solid fa-trash"></i> Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>
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
    goToUser(userId) {
      this.$router.push(`/users/${userId}`);
    },

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
  min-height: 100vh;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  padding: 1.5rem 1rem 6rem;
  color: #fff;
  font-family: "Inter", sans-serif;
  box-sizing: border-box;
}

.search-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.search-box {
  flex: 1;
  position: relative;
  min-width: 230px;
}

.user-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 1rem;
  gap: 0.7rem;
}

.manage-btn {
  background: rgba(157, 216, 106, 0.15);
  color: #9dd86a;
  border: 1px solid rgba(157, 216, 106, 0.3);
  border-radius: 8px;
  padding: 0.5rem 1rem;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.manage-btn i {
  font-size: 1rem;
}

.manage-btn:hover {
  background: #9dd86a;
  color: #1a1a1a;
  transform: translateY(-2px);
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #9dd86a;
  font-size: 1.2rem;
}

.search-input {
  width: 100%;
  padding: 0.8rem 1rem 0.8rem 2.8rem;
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(157, 216, 106, 0.3);
  border-radius: 10px;
  color: #fff;
  font-size: 1rem;
  transition: 0.25s;
}

.search-input:focus {
  outline: none;
  box-shadow: 0 0 8px rgba(157, 216, 106, 0.6);
}

/* 🔽 Filtro */
.filter-section {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-shrink: 0;
}

.filter-section label {
  font-weight: 600;
  color: #9dd86a;
  font-size: 0.95rem;
}

.filter-select {
  padding: 0.6rem 1rem;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  border: 1px solid rgba(157, 216, 106, 0.3);
  cursor: pointer;
  font-size: 0.95rem;
}

.filter-select:hover {
  background: rgba(157, 216, 106, 0.1);
}

/* 🧮 Total */
.total-users {
  text-align: right;
  font-weight: 600;
  color: #9dd86a;
  margin-bottom: 1rem;
}

/* 👥 Lista */
.users-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
  gap: 1.2rem;
}

.user-card {
  background: rgba(255, 255, 255, 0.05);
  border-left: 5px solid #9dd86a;
  border-radius: 12px;
  padding: 1.2rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
  transition: all 0.25s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  word-break: break-word;
}

.user-card:hover {
  transform: translateY(-4px);
  background: rgba(255, 255, 255, 0.08);
}

.user-info h3 {
  color: #9dd86a;
  font-size: 1.2rem;
  margin-bottom: 0.3rem;
  word-wrap: break-word;
}

.user-info p {
  color: #ccc;
  margin: 0.25rem 0;
  font-size: 0.95rem;
}

.user-balance {
  text-align: right;
  margin-top: 0.5rem;
}

.user-balance span {
  color: #9dd86a;
  font-size: 0.9rem;
}

.user-balance .amount {
  font-size: 1.3rem;
  color: #fff;
  font-weight: bold;
}

/* 🧰 Acciones */
.user-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 1rem;
}

/* 🗑️ Botón eliminar */
.delete-btn {
  background: rgba(244, 67, 54, 0.15);
  color: #ff6b6b;
  border: 1px solid rgba(244, 67, 54, 0.3);
  border-radius: 8px;
  padding: 0.5rem 1rem;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.delete-btn i {
  font-size: 1rem;
}

.delete-btn:hover {
  background: #f44336;
  color: #fff;
  border-color: #f44336;
  transform: translateY(-2px);
}

/* 🏠 Volver */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 25px;
  background: linear-gradient(135deg, #9dd86a, #7ac75d);
  color: #1a1a1a;
  border: none;
  padding: 0.9rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
  transition: all 0.3s ease;
  z-index: 1000;
}

.btn-volver:hover {
  transform: translateY(-3px);
  filter: brightness(1.1);
}

/* 📱 Responsivo */
@media (max-width: 768px) {
  .usuarios-container {
    padding: 1rem;
  }

  .search-section {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-section {
    justify-content: space-between;
  }

  .users-list {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .user-card {
    padding: 1rem;
  }

  .user-info h3 {
    font-size: 1.1rem;
  }

  .user-info p {
    font-size: 0.9rem;
  }

  .user-balance .amount {
    font-size: 1.2rem;
  }

  .btn-volver {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 0.8rem 1.3rem;
    font-size: 0.95rem;
  }
}
</style>

