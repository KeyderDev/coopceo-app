<template>
  <div class="transactions-container">
    <!-- 🔝 Header -->
    <div class="header">
      <h2><i class="fa-solid fa-scroll"></i> Mis Transacciones</h2>
      <button @click="$router.push('/dashboard')" class="back-btn">
      </button>
    </div>

    <div class="table-wrapper" v-if="transactions.length">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Total</th>
            <th>Método de Pago</th>
            <th>Cajero</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tx in transactions" :key="tx.id">
            <td data-label="ID">{{ tx.id }}</td>
            <td data-label="Total">${{ tx.total }}</td>
            <td data-label="Método de Pago">
              <span :class="['method', tx.metodo_pago]">{{ formatMethod(tx.metodo_pago) }}</span>
            </td>
            <td data-label="Cajero">{{ tx.cajero?.username || tx.cajero?.nombre }}</td>
            <td data-label="Fecha">{{ new Date(tx.created_at).toLocaleString() }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="no-data">No hay transacciones registradas.</p>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      transactions: [],
    };
  },
  async created() {
    try {
      const token = localStorage.getItem("auth_token");
      const res = await axios.get(`${import.meta.env.VITE_APP_URL}/api/my-transactions`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.transactions = res.data;
    } catch (err) {
      console.error("Error cargando transacciones:", err);
    }
  },
  methods: {
    formatMethod(method) {
      return method === "athmovil" ? "ATH Móvil" : "Efectivo";
    },
  },
};
</script>

<style scoped>
.transactions-container {
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  color: #fff;
  min-height: 100vh;
  padding: 2rem;
  font-family: "Inter", sans-serif;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  max-width: 950px;
  margin-bottom: 1.8rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.header h2 {
  font-size: 1.6rem;
  color: #97d569;
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-weight: 600;
}

.back-btn {
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 10px;
  padding: 0.6rem 1.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: 0.25s ease;
}

.back-btn:hover {
  background: rgba(151, 213, 105, 0.25);
  border-color: rgba(151, 213, 105, 0.5);
}

.table-wrapper {
  width: 100%;
  max-width: 950px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 14px;
  border-left: 5px solid #97d569;
  overflow-x: auto;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #03355c;
}

th,
td {
  padding: 0.85rem 1.2rem;
  text-align: left;
  font-size: 0.95rem;
}

th {
  font-weight: 600;
  color: #97d569;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

td {
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  color: #e0e0e0;
}

tbody tr:hover {
  background: rgba(157, 216, 106, 0.08);
  transition: background 0.2s ease;
}

.method {
  font-weight: 600;
  padding: 0.25rem 0.7rem;
  border-radius: 6px;
}

.method.efectivo {
  background: rgba(151, 213, 105, 0.15);
  color: #9dd86a;
}

.method.athmovil {
  background: rgba(3, 59, 101, 0.2);
  color: #33b5e5;
}

.no-data {
  text-align: center;
  margin-top: 2rem;
  color: #ccc;
  font-style: italic;
}

@media (max-width: 768px) {
  .header {
    flex-direction: column;
    align-items: flex-start;
  }

  .back-btn {
    width: 100%;
    justify-content: center;
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
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    margin-bottom: 1rem;
    padding: 1rem;
  }

  td {
    text-align: right;
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    font-size: 0.9rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  td::before {
    content: attr(data-label);
    font-weight: 600;
    color: #97d569;
  }

  td:last-child {
    border-bottom: none;
  }
}
</style>
