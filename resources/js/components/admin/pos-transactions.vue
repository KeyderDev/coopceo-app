<template>
  <div class="sales-list">
    <h2>Transacciones</h2>
    <div class="table-container">
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
            <td data-label="Cliente">
              {{ sale.cliente?.username || sale.cliente?.nombre || 'Sin nombre' }}
            </td>
            <td data-label="Cajero">
              {{ sale.cajero?.username || sale.cajero?.nombre || 'Sistema' }}
            </td>
            <td data-label="Total" class="total">{{ sale.total }}</td>
            <td data-label="Método de Pago">{{ sale.metodo_pago }}</td>
            <td data-label="Creado">{{ formatDate(sale.created_at) }}</td>
            <td data-label="Actualizado">{{ formatDate(sale.updated_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      sales: []
    };

  },
  methods: {
    formatDate(utcDate) {
      const date = new Date(utcDate); // interpreta UTC
      return date.toLocaleString('es-PR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      });
    }
  },
  async created() {
    try {
      const token = localStorage.getItem('auth_token');
      const response = await axios.get('https://coopceo.ddns.net:8000/api/sales', {
        headers: { Authorization: `Bearer ${token}` }
      });
      console.log('Respuesta API:', response.data);

      this.sales = response.data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    } catch (error) {
      console.error('Error al obtener las transacciones:', error);
    }
  }


};
</script>

<style scoped>
.sales-list {
  padding: 1rem;
  background-color: #03365e;
  border-radius: 10px;
  color: #033760;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

h2 {
  font-size: 1.8rem;
  margin-bottom: 1rem;
  color: #97d569;
  border-bottom: 2px solid #97d569;
  padding-bottom: 0.3rem;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
  background-color: #03365e;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

th,
td {
  padding: 0.75rem 1rem;
  text-align: left;
  white-space: nowrap;
}

th {
  background-color: #97d569;
  color: #fff;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

tr:nth-child(odd) {
  background-color: #044b7f;
}

tr:nth-child(even) {
  background-color: #0562a3;
}

tr:hover {
  background-color: #97d569;
  color: #033760;
  transition: background-color 0.3s;
}

td {
  color: #fff;
}

.total {
  color: #97d569;
  font-weight: bold;
}

/* ---- RESPONSIVE ---- */
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
    /* Ocultamos encabezados en móvil */
  }

  tr {
    margin-bottom: 1rem;
    border-radius: 8px;
    background: #044b7f;
    padding: 0.5rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  }

  td {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }

  td::before {
    content: attr(data-label);
    font-weight: bold;
    color: #97d569;
    margin-right: 0.5rem;
  }

  td:last-child {
    border-bottom: none;
  }
}
</style>