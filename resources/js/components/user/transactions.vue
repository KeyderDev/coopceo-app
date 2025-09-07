<template>
  <div class="transactions-container">
    <h2>Mis Transacciones</h2>
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
          <td>{{ tx.id }}</td>
          <td>${{ tx.total }}</td>
          <td>{{ tx.metodo_pago }}</td>
          <td>{{ tx.cajero?.username || tx.cajero?.nombre }}</td>
          <td>{{ new Date(tx.created_at).toLocaleString() }}</td>
        </tr>
      </tbody>
    </table>
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
      const res = await axios.get("https://coopceo.ddns.net:8000/api/my-transactions", {
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
  padding: 2rem;
  background: #f9fafb;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.transactions-container h2 {
  margin-bottom: 1rem;
  font-size: 1.5rem;
  color: #033b65;
  border-left: 6px solid #97d569;
  padding-left: 0.5rem;
  font-weight: 600;
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

th, td {
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
</style>
