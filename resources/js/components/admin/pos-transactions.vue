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
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
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
  background-color: #03365e;
  border-radius: 12px;
  color: #fff;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
}

h2 {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: #97d569;
  border-bottom: 3px solid #97d569;
  padding-bottom: 0.5rem;
  text-align: center;
}

.filter-container {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 1.2rem;
  margin-bottom: 2rem;
}

.filter-container label {
  display: flex;
  flex-direction: column;
  font-size: 0.95rem;
  color: #d2e8c1;
}

.filter-container input[type="date"],
.filter-container select {
  background-color: #044b7f;
  color: #fff;
  border: 1px solid #97d569;
  padding: 0.6rem 0.8rem;
  border-radius: 6px;
  margin-top: 0.3rem;
  font-size: 0.9rem;
  width: 180px;
}

.filter-container button {
  background-color: #97d569;
  color: #033760;
  border: none;
  padding: 0.7rem 1.4rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}

.reset-btn {
  background-color: #044b7f;
  color: #fff;
  border: 1px solid #97d569;
}

.table-wrapper {
  width: 100%;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  background-color: #03365e;
  border-radius: 10px;
  overflow: hidden;
}

th,
td {
  padding: 1rem 1.5rem;
  text-align: left;
  word-wrap: break-word;
  white-space: normal;
}

th {
  background-color: #97d569;
  color: #03365e;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.9rem;
}

tr:nth-child(odd) {
  background-color: #044b7f;
}

tr:nth-child(even) {
  background-color: #0562a3;
}

.total {
  color: #97d569;
  font-weight: bold;
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
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  cursor: pointer;
}

/* 📱 Vista móvil */
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
    background: #044b7f;
    margin-bottom: 1.5rem;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
  }

  td {
    display: flex;
    justify-content: space-between;
    padding: 0.6rem 0;
    font-size: 0.95rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  }

  td::before {
    content: attr(data-label);
    font-weight: bold;
    color: #97d569;
    margin-right: 1rem;
  }

  td:last-child {
    border-bottom: none;
  }
}
</style>
