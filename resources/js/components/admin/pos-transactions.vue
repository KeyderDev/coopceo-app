<template>
  <div class="sales-list">
    <h2>Transacciones</h2>

    <div class="filter-container">
      <label>
        Desde:
        <input type="date" v-model="filter.from" />
      </label>
      <label>
        Hasta:
        <input type="date" v-model="filter.to" />
      </label>

      <label>
        Método de pago:
        <select v-model="filter.metodo_pago">
          <option value="">Todos</option>
          <option value="efectivo">Efectivo</option>
          <option value="athmovil">ATH Móvil</option>
        </select>
      </label>

      <button @click="applyFilters">Filtrar</button>
      <button @click="resetFilter" class="reset-btn">Restablecer</button>
    </div>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Cajero</th>
            <th>Total</th>
            <th>Método de Pago</th>
            <th>Creado</th>
            <th>Actualizado</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sale in sales" :key="sale.id">
            <td data-label="ID">{{ sale.id }}</td>
            <td data-label="Cliente">{{ sale.cliente?.username || sale.cliente?.nombre || 'Sin nombre' }}</td>
            <td data-label="Cajero">{{ sale.cajero?.username || sale.cajero?.nombre || 'Sistema' }}</td>
            <td data-label="Total" class="total">${{ sale.total }}</td>
            <td data-label="Método de Pago">{{ sale.metodo_pago }}</td>
            <td data-label="Creado">{{ formatDate(sale.created_at) }}</td>
            <td data-label="Actualizado">{{ formatDate(sale.updated_at) }}</td>
          </tr>
        </tbody>
      </table>
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
      sales: [],
      allSales: [],
      filter: { from: "", to: "", metodo_pago: "" },
    };
  },
  methods: {
    formatDate(utcDate) {
      const date = new Date(utcDate);
      return date.toLocaleString("es-PR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
      });
    },
    volverMenu() {
      this.$router.push("/");
    },
    applyFilters() {
      let filtered = [...this.allSales];
      if (this.filter.from && this.filter.to) {
        const from = new Date(this.filter.from);
        const to = new Date(this.filter.to);
        to.setHours(23, 59, 59, 999);
        filtered = filtered.filter((sale) => {
          const saleDate = new Date(sale.created_at);
          return saleDate >= from && saleDate <= to;
        });
      }
      if (this.filter.metodo_pago) {
        filtered = filtered.filter(
          (sale) =>
            sale.metodo_pago?.toLowerCase() ===
            this.filter.metodo_pago.toLowerCase()
        );
      }
      this.sales = filtered;
    },
    resetFilter() {
      this.filter = { from: "", to: "", metodo_pago: "" };
      this.sales = [...this.allSales];
    },
  },
  async created() {
    try {
      const token = localStorage.getItem("auth_token");
      const response = await axios.get(
        `${import.meta.env.VITE_APP_URL}/api/sales`,
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );
      const sorted = response.data.sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at)
      );
      this.sales = sorted;
      this.allSales = sorted;
    } catch (error) {
      console.error("Error al obtener las transacciones:", error);
    }
  },
};
</script>

<style scoped>
.sales-list {
  width: 95%;
  max-width: 1600px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: rgba(30, 30, 30, 0.85);
  border-radius: 16px;
  color: #f5f7fa;
  font-family: "Inter", sans-serif;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(8px);
}

/* 🧾 Título */
h2 {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: #9dd86a;
  border-bottom: 3px solid rgba(157, 216, 106, 0.3);
  padding-bottom: 0.5rem;
  text-align: center;
  text-shadow: 0 0 8px rgba(157, 216, 106, 0.3);
}

/* 🔎 Filtros */
.filter-container {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
}

.filter-container label {
  display: flex;
  flex-direction: column;
  font-size: 0.95rem;
  color: #dcdcdc;
}

.filter-container input[type="date"],
.filter-container select {
  background-color: rgba(14, 17, 23, 0.9);
  color: #f5f7fa;
  border: 1px solid rgba(157, 216, 106, 0.4);
  padding: 0.6rem 0.8rem;
  border-radius: 8px;
  margin-top: 0.3rem;
  font-size: 0.9rem;
  width: 180px;
  transition: 0.3s ease;
}

.filter-container input[type="date"]:focus,
.filter-container select:focus {
  outline: none;
  border-color: #9dd86a;
  box-shadow: 0 0 8px rgba(157, 216, 106, 0.3);
}

/* 🧩 Botones filtro */
.filter-container button {
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  border: none;
  padding: 0.7rem 1.4rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}

.filter-container button:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-2px);
}

.reset-btn {
  background: transparent;
  color: #f5f7fa;
  border: 1px solid rgba(157, 216, 106, 0.4);
}

.reset-btn:hover {
  background: rgba(157, 216, 106, 0.15);
  color: #fff;
}

/* 📋 Tabla */
.table-wrapper {
  width: 100%;
  overflow-x: auto;
  border-radius: 10px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
}

table {
  width: 100%;
  border-collapse: collapse;
  background-color: rgba(14, 17, 23, 0.9);
  border-radius: 10px;
  overflow: hidden;
}

th,
td {
  padding: 1rem 1.5rem;
  text-align: left;
  word-wrap: break-word;
  white-space: normal;
  font-size: 0.95rem;
}

/* 🔝 Encabezado */
th {
  background: rgba(157, 216, 106, 0.15);
  color: #9dd86a;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.9rem;
  position: sticky;
  top: 0;
  backdrop-filter: blur(6px);
  z-index: 2;
}

/* 🌗 Filas */
tr:nth-child(odd) {
  background-color: rgba(255, 255, 255, 0.02);
}

tr:nth-child(even) {
  background-color: rgba(255, 255, 255, 0.05);
}

tbody tr:hover {
  background-color: rgba(157, 216, 106, 0.1);
  transition: all 0.2s ease;
}

/* 💲 Total */
.total {
  color: #b9f089;
  font-weight: bold;
}

/* 🏠 Botón volver */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 30px;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: white;
  border: none;
  padding: 0.8rem 1.3rem;
  border-radius: 10px;
  font-weight: bold;
  font-size: 1rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
  cursor: pointer;
  transition: all 0.25s ease;
  z-index: 1000;
}

.btn-volver:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-3px);
}

/* 🧠 Scrollbar */
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
@media (max-width: 768px) {
  .sales-list {
    padding: 1rem;
    width: 100%;
  }

  table,
  thead,
  tbody,
  th,
  td,
  tr {
    display: block;
  }

  thead {
    display: none;
  }

  tr {
    background: rgba(30, 30, 30, 0.85);
    margin-bottom: 1.2rem;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
  }

  td {
    display: flex;
    justify-content: space-between;
    padding: 0.6rem 0;
    font-size: 0.95rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  td::before {
    content: attr(data-label);
    font-weight: bold;
    color: #9dd86a;
    margin-right: 1rem;
  }

  td:last-child {
    border-bottom: none;
  }
}
</style>

