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
            <td>{{ sale.id }}</td>
            <td>{{ sale.cliente_id }}</td>
            <td>{{ sale.cajero_id }}</td>
            <td class="total">{{ sale.total }}</td>
            <td>{{ sale.metodo_pago }}</td>
            <td>{{ formatDate(sale.created_at) }}</td>
            <td>{{ formatDate(sale.updated_at) }}</td>

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
      console.log('Respuesta API:', response.data); // <--- revisa aquí
      this.sales = response.data; // o response.data.data si tu API lo devuelve así
    } catch (error) {
      console.error('Error al obtener las transacciones:', error);
    }
  }

};
</script>

<style scoped>
.sales-list {
  padding: 1rem;
  background-color: #1e1e1e;
  border-radius: 8px;
  color: #fff;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

th,
td {
  border: 1px solid #2c2c2c;
  padding: 0.5rem;
  text-align: left;
  white-space: nowrap;
}

th {
  background-color: #03365e;
  color: #fff;
}

tr:nth-child(even) {
  background-color: #2a2a2a;
}

.total {
  color: #97d569;
  font-weight: bold;
}
</style>
