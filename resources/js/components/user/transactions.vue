<template>
  <div class="transactions-container">
    <h2>Mis Transacciones</h2>
    <div class="table-wrapper">
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
            <td data-label="Método de Pago">{{ tx.metodo_pago }}</td>
            <td data-label="Cajero">{{ tx.cajero?.username || tx.cajero?.nombre }}</td>
            <td data-label="Fecha">{{ new Date(tx.created_at).toLocaleString() }}</td>
          </tr>
        </tbody>
      </table>
    </div>
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
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    this.transactions = res.data;
  } catch (err) {
    console.error("Error cargando transacciones:", err);
  }
},
};
</script>

<style scoped>
.transactions-container {
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

.transactions-container h2 {
  margin-bottom: 1rem;
  font-size: 1.5rem;
  color: #033b65;
  border-left: 6px solid #97d569;
  padding-left: 0.5rem;
  font-weight: 600;
}

.table-wrapper {
  width: 100%;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
}

thead {
  background: #033b65;
  color: white;
}

th,
td {
  padding: 0.75rem 1rem;
  text-align: left;
  font-size: 0.95rem;
}

tbody tr {
  transition: background 0.2s ease;
}

tbody tr:nth-child(even) {
  background: #f3f7f1;
}

tbody tr:hover {
  background: #e8f4e0;
}

td {
  color: #333;
}

th {
  font-weight: 600;
}

td:nth-child(2) {
  font-weight: bold;
  color: #033b65;
}

td:nth-child(3) {
  color: #97d569;
  font-weight: 500;
}

@media (max-width: 768px) {
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
    margin-bottom: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    padding: 1rem;
  }

  td {
    text-align: right;
    padding: 0.5rem 0;
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    border-bottom: 1px solid #eee;
  }

  td::before {
    content: attr(data-label);
    font-weight: 600;
    color: #033b65;
    text-transform: capitalize;
  }

  td:last-child {
    border-bottom: none;
  }
}
</style>
