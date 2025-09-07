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
  background-color: #03365e; /* fondo claro neutro */
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
  background-color: #03365e; /* fondo blanco para tabla */
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* sombra sutil */
}

th, td {
  padding: 0.75rem 1rem;
  text-align: left;
  white-space: nowrap;
}

th {
  background-color: #97d569; /* azul oscuro */
  color: #fff;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

tr:nth-child(odd) {
  background-color: #044b7f; /* fila impar */
}

tr:nth-child(even) {
  background-color: #0562a3; /* fila par */
}

tr:hover {
  background-color: #97d569; /* verde de tu paleta al pasar el mouse */
  color: #033760;
  transition: background-color 0.3s;
}

td {
  border-bottom: none; /* o border-bottom: 0; */
  color: #fff;
}

.total {
  color: #97d569; /* verde destacado */
  font-weight: bold;
}
</style>
