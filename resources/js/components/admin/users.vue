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

    <!-- 👥 Listado de usuarios -->
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

    <button class="btn-volver" @click="volverMenu">
      <i class="fa-solid fa-house"></i> Menú Principal
    </button>
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
  width: 95%;
  max-width: 1300px;
  margin: 2rem auto;
  background: #f8fafc;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
  overflow-x: hidden;
}

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
  cursor: pointer;
}

/* 🔍 Sección de búsqueda */
.search-section {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.search-box {
  flex: 1 1 400px;
  position: relative;
  min-width: 250px;
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #044271;
  font-size: 1.2rem;
}

.search-input {
  width: 100%;
  padding: 0.8rem 1rem 0.8rem 2.8rem;
  border-radius: 12px;
  border: 1px solid #044271;
  background: #97d569;
  font-size: 1rem;
  color: #044271;
  transition: box-shadow 0.2s ease;
  box-sizing: border-box;
}

.search-input::placeholder {
  color: #044271;
}

.search-input:focus {
  outline: none;
  border-color: #044271;
  box-shadow: 0 0 8px rgba(4, 66, 113, 0.5);
}

/* 🧩 Filtros */
.filter-section {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #044271;
  flex-shrink: 0;
}

.filter-select {
  padding: 0.5rem 0.8rem;
  border-radius: 8px;
  border: 1px solid #044271;
  background: #97d569;
  color: #044271;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:hover {
  border-color: #044271;
  background: #a6dd79;
}

/* 🧮 Total */
.total-users {
  font-size: 1.1rem;
  font-weight: 600;
  color: #044271;
  margin-bottom: 1rem;
  text-align: right;
}

/* 👥 Tarjetas */
.users-list {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
  max-height: calc(100vh - 250px);
  overflow-y: auto;
  padding-right: 0.5rem;
}

.user-card {
  background: #fff;
  border-left: 6px solid #97d569;
  border-radius: 10px;
  padding: 1.5rem 1.5rem 2.5rem;
  display: flex;
  flex-direction: column;
  position: relative;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  overflow-wrap: break-word; /* 👈 fuerza el salto de línea */
  word-break: break-word; /* 👈 corta palabras largas */
  hyphens: auto; /* 👈 mejora legibilidad al cortar */
}

.user-card:hover {
  transform: scale(1.01);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.user-main {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.user-info h3 {
  font-size: 1.3rem;
  color: #044271;
  margin-bottom: 0.3rem;
  word-break: break-word;
}

.user-info p {
  margin: 0.2rem 0;
  color: #555;
  word-break: break-all; /* 👈 para emails o cadenas sin espacios */
  overflow-wrap: anywhere;
}

.user-balance {
  text-align: right;
  min-width: 150px;
}

.user-balance span {
  color: #777;
  font-size: 0.9rem;
}

.user-balance .amount {
  font-size: 1.4rem;
  font-weight: 700;
  color: #044271;
}

/* 🗑️ Botón eliminar */
.delete-btn {
  position: absolute;
  bottom: 12px;
  right: 18px;
  background: transparent;
  border: none;
  color: #dc2626;
  font-size: 1.3rem;
  cursor: pointer;
  transition: color 0.2s ease, transform 0.2s ease;
}

.delete-btn:hover {
  color: darkred;
  transform: scale(1.2);
}

/* 📱 Responsive */
@media (max-width: 768px) {
  .usuarios-container {
    width: 100%;
    padding: 1rem;
  }

  .search-section {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-section {
    justify-content: space-between;
    flex-wrap: wrap;
  }

  .search-box {
    flex: 1 1 100%;
  }

  .search-input {
    width: 100%;
    font-size: 1rem;
  }

  .user-main {
    flex-direction: column;
    align-items: flex-start;
  }

  .user-balance {
    text-align: left;
  }

  .delete-btn {
    bottom: 10px;
    right: 10px;
  }

  .btn-volver {
    bottom: 15px;
    right: 15px;
    font-size: 0.9rem;
    padding: 0.6rem 1rem;
  }
}

/* 📱 Extra pequeño (móviles <480px) */
@media (max-width: 480px) {
  .usuarios-container {
    padding: 0.8rem;
  }

  .search-section {
    gap: 0.8rem;
  }

  .search-box {
    width: 100%;
  }

  .search-input {
    padding: 0.7rem 1rem 0.7rem 2.5rem;
    font-size: 0.95rem;
  }

  .filter-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.3rem;
  }

  .filter-select {
    width: 100%;
  }

  .total-users {
    text-align: left;
    font-size: 1rem;
  }

  .user-info h3 {
    font-size: 1.1rem;
  }

  .user-balance .amount {
    font-size: 1.2rem;
  }
}
</style>

