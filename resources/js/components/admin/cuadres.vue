<template>
  <div class="cash-reconciliations">
    <h2>Todos los Cuadres</h2>

    <div class="table-wrapper">
      <!-- 💻 Tabla de escritorio -->
      <table class="desktop-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Petty</th>
            <th>$20</th>
            <th>$10</th>
            <th>$5</th>
            <th>$1</th>
            <th>Coin 0.10</th>
            <th>Coin 0.05</th>
            <th>Coin 0.01</th>
            <th>Coin 0.25</th>
            <th>Total Contado</th>
            <th>Total Ventas Efectivo</th>
            <th>Diferencia</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cuadre in cuadrés" :key="cuadre.id">
            <td>{{ cuadre.id }}</td>
            <td>{{ cuadre.petty }}</td>
            <td>{{ cuadre.bill_20 }}</td>
            <td>{{ cuadre.bill_10 }}</td>
            <td>{{ cuadre.bill_5 }}</td>
            <td>{{ cuadre.bill_1 }}</td>
            <td>{{ cuadre.coin_10 }}</td>
            <td>{{ cuadre.coin_5 }}</td>
            <td>{{ cuadre.coin_1 }}</td>
            <td>{{ cuadre.coin_25 }}</td>
            <td>{{ cuadre.total_counted }}</td>
            <td>{{ cuadre.total_sales_cash }}</td>
            <td>{{ cuadre.difference }}</td>
            <td>{{ cuadre.created_at_local }}</td>
          </tr>
        </tbody>
      </table>

      <!-- 📱 Vista móvil -->
      <div class="mobile-cards">
        <div class="card" v-for="cuadre in cuadrés" :key="cuadre.id">
          <p><strong>ID:</strong> {{ cuadre.id }}</p>
          <p><strong>Petty:</strong> {{ cuadre.petty }}</p>
          <p><strong>$20:</strong> {{ cuadre.bill_20 }}</p>
          <p><strong>$10:</strong> {{ cuadre.bill_10 }}</p>
          <p><strong>$5:</strong> {{ cuadre.bill_5 }}</p>
          <p><strong>$1:</strong> {{ cuadre.bill_1 }}</p>
          <p><strong>Coin 0.10:</strong> {{ cuadre.coin_10 }}</p>
          <p><strong>Coin 0.05:</strong> {{ cuadre.coin_5 }}</p>
          <p><strong>Coin 0.01:</strong> {{ cuadre.coin_1 }}</p>
          <p><strong>Coin 0.25:</strong> {{ cuadre.coin_25 }}</p>
          <p><strong>Total Contado:</strong> {{ cuadre.total_counted }}</p>
          <p><strong>Total Ventas Efectivo:</strong> {{ cuadre.total_sales_cash }}</p>
          <p><strong>Diferencia:</strong> {{ cuadre.difference }}</p>
          <p><strong>Fecha:</strong> {{ cuadre.created_at_local }}</p>
        </div>
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
    return { cuadrés: [] };
  },
  async created() {
    try {
      const token = localStorage.getItem("auth_token");
      const response = await axios.get("/api/cash-reconciliations", {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.cuadrés = response.data.map((c) => ({
        ...c,
        created_at_local: this.formatLocalDate(c.created_at),
      }));
    } catch (error) {
      console.error("Error al obtener los cuadres:", error);
    }
  },
  methods: {
    formatLocalDate(utcDate) {
      return new Date(utcDate).toLocaleString("es-PR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      });
    },
    volverMenu() {
      this.$router.push("/");
    },
  },
};
</script>

<style scoped>
.cash-reconciliations {
  display: flex;
  flex-direction: column;
  height: 100vh; /* Ocupa todo el alto visible */
  width: 100%;
  padding: 1rem 2rem;
  background: linear-gradient(160deg, #022744, #044b7f);
  color: #fff;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  overflow: hidden;
}

h2 {
  color: #a8e060;
  font-size: 2rem;
  margin-bottom: 1rem;
  text-align: center;
}

.table-wrapper {
  flex: 1;
  overflow: auto;
  background: #044b7f;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
}

.desktop-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 1200px;
}

th,
td {
  padding: 0.8rem;
  text-align: center;
  font-size: 0.9rem;
  white-space: nowrap;
}

thead {
  background: #a8e060;
  color: #022744;
  position: sticky;
  top: 0;
  z-index: 5;
}

tbody tr:nth-child(even) {
  background: #055b95;
}

tbody tr:hover {
  background: #0a7ac2;
  transform: scale(1.01);
  transition: 0.2s;
}

/* === Mobile cards === */
.mobile-cards {
  display: none;
  flex-direction: column;
  padding: 1rem;
  gap: 1rem;
}

.btn-volver {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 0.7rem 1.2rem;
  border-radius: 10px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
  transition: all 0.25s ease;
  z-index: 1000;
}

.card {
  background: #055b95;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

.card p {
  margin: 0.3rem 0;
  font-size: 0.9rem;
}

/* === Responsive === */
@media (max-width: 900px) {
  .desktop-table {
    display: none;
  }
  .mobile-cards {
    display: flex;
  }
  h2 {
    font-size: 1.6rem;
  }
}
</style>
