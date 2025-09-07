<template>
  <div class="cash-reconciliations">
    <h2>Todos los Cuadres</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Petty</th>
          <th>20</th>
          <th>10</th>
          <th>5</th>
          <th>1</th>
          <th>Coin 10</th>
          <th>Coin 5</th>
          <th>Coin 1</th>
          <th>Coin 25</th>
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
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      cuadrés: []
    };
  },

  async created() {
    try {
      const token = localStorage.getItem('auth_token'); // o como guardes el token
      const response = await axios.get('/api/cash-reconciliations', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
      // Convertir fechas a hora local antes de asignarlas
      this.cuadrés = response.data.map(c => ({
        ...c,
        created_at_local: this.formatLocalDate(c.created_at)
      }));
    } catch (error) {
      console.error('Error al obtener los cuadres:', error);
    }
  },

  methods: {
    formatLocalDate(utcDate) {
      return new Date(utcDate).toLocaleString('es-PR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
      });
    }
  }
};
</script>

<style scoped>
.cash-reconciliations {
  padding: 1.5rem;
  background-color: #03365e; /* fondo azul oscuro */
  color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

h2 {
  color: #97d569; /* verde claro */
  margin-bottom: 1rem;
  font-size: 1.8rem;
  letter-spacing: 0.5px;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background-color: #044b7f; /* azul para filas */
  border-radius: 8px;
  overflow: hidden;
  box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
}

th,
td {
  padding: 0.7rem 1rem;
  text-align: center;
  color: #fff;
  font-size: 0.95rem;
}

thead {
  background-color: #97d569; /* verde encabezado */
  color: #03365e; /* texto azul oscuro */
  font-weight: 600;
  letter-spacing: 0.5px;
}

tbody tr {
  transition: background 0.3s, transform 0.2s;
}

tbody tr:nth-child(even) {
  background-color: #0562a3; /* azul más claro filas pares */
}

tbody tr:hover {
  background-color: #0971c2; /* azul brillante al pasar */
  transform: scale(1.01);
}

th {
  position: sticky;
  top: 0;
  z-index: 1;
}
</style>

