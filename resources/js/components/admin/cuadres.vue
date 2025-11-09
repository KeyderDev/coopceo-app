<template>
  <div class="cash-reconciliations">
    <h2>Todos los Cuadres</h2>

    <div class="table-wrapper">
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
  height: 100vh;
  width: 100%;
  padding: 1rem 2rem;
  background: rgba(14, 17, 23, 0.98);
  color: #f5f7fa;
  font-family: "Inter", sans-serif;
  overflow: hidden;
}

/* 🧭 Título */
h2 {
  color: #9dd86a;
  font-size: 2rem;
  margin-bottom: 1.5rem;
  text-align: center;
  text-shadow: 0 0 6px rgba(157, 216, 106, 0.4);
}

/* 🧾 Contenedor tabla */
.table-wrapper {
  flex: 1;
  overflow: auto;
  background: rgba(30, 30, 30, 0.85);
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

/* 💻 Tabla de escritorio */
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
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

/* 🧩 Encabezado */
thead {
  background: rgba(157, 216, 106, 0.15);
  color: #9dd86a;
  font-weight: 600;
  letter-spacing: 0.5px;
  position: sticky;
  top: 0;
  z-index: 5;
  backdrop-filter: blur(6px);
}

/* 🌗 Filas */
tbody tr:nth-child(even) {
  background: rgba(255, 255, 255, 0.03);
}

tbody tr:hover {
  background: rgba(157, 216, 106, 0.1);
  transform: scale(1.01);
  transition: all 0.2s ease;
}

/* 📱 Vista móvil */
.mobile-cards {
  display: none;
  flex-direction: column;
  padding: 1rem;
  gap: 1rem;
}

/* 🪪 Card móvil */
.card {
  background: rgba(30, 30, 30, 0.9);
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.08);
  transition: all 0.2s ease;
}

.card:hover {
  border-color: rgba(157, 216, 106, 0.3);
  transform: translateY(-3px);
}

.card p {
  margin: 0.3rem 0;
  font-size: 0.9rem;
  color: #e4e6eb;
}

.card strong {
  color: #9dd86a;
}

/* 🏠 Botón volver */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 25px;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  border: none;
  padding: 0.8rem 1.4rem;
  border-radius: 10px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
  transition: all 0.25s ease;
  z-index: 1000;
}

.btn-volver:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-3px);
}

/* 🧠 Scrollbar estilizada */
.table-wrapper::-webkit-scrollbar {
  height: 10px;
  width: 10px;
}

.table-wrapper::-webkit-scrollbar-thumb {
  background-color: rgba(157, 216, 106, 0.3);
  border-radius: 10px;
}

.table-wrapper::-webkit-scrollbar-thumb:hover {
  background-color: rgba(157, 216, 106, 0.6);
}

.table-wrapper::-webkit-scrollbar-track {
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
}

/* 📱 Responsive */
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

  .btn-volver {
    bottom: 15px;
    right: 15px;
    font-size: 0.9rem;
    padding: 0.7rem 1rem;
  }
}
</style>

