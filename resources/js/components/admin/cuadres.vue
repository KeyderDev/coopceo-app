<template>
  <div class="cash-reconciliations">
    <h2>Todos los Cuadres</h2>
    <div class="table-container">
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

      <div class="mobile-cards">
        <div class="card" v-for="cuadre in cuadrés" :key="cuadre.id">
          <p><strong>ID:</strong> {{ cuadre.id }}</p>
          <p><strong>Petty:</strong> {{ cuadre.petty }}</p>
          <p><strong>$20:</strong> {{ cuadre.bill_20 }}</p>
          <p><strong>$10:</strong> {{ cuadre.bill_10 }}</p>
          <p><strong>$5:</strong> {{ cuadre.bill_5 }}</p>
          <p><strong>$1:</strong> {{ cuadre.bill_1 }}</p>
          <p><strong>Coin 10:</strong> {{ cuadre.coin_10 }}</p>
          <p><strong>Coin 5:</strong> {{ cuadre.coin_5 }}</p>
          <p><strong>Coin 1:</strong> {{ cuadre.coin_1 }}</p>
          <p><strong>Coin 25:</strong> {{ cuadre.coin_25 }}</p>
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
import axios from 'axios';

export default {
  data() {
    return {
      cuadrés: []
    };
  },

  async created() {
    try {
      const token = localStorage.getItem('auth_token'); 
      const response = await axios.get('/api/cash-reconciliations', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
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
  padding: 1rem;
  background-color: #03365e;
  color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

h2 {
  color: #97d569;
  margin-bottom: 1rem;
  font-size: 1.8rem;
  letter-spacing: 0.5px;
}

.table-container {
  width: 100%;
  overflow-x: auto;
}

.desktop-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background-color: #044b7f;
  border-radius: 8px;
}

th,
td {
  padding: 0.6rem 0.8rem;
  text-align: center;
  color: #fff;
  font-size: 0.85rem;
  white-space: nowrap;
}

thead {
  background-color: #97d569;
  color: #03365e;
  font-weight: 600;
  letter-spacing: 0.5px;
  position: sticky;
  top: 0;
  z-index: 1;
}

tbody tr:nth-child(even) {
  background-color: #0562a3;
}

tbody tr {
  transition: background 0.3s, transform 0.2s;
  transform: translateY(0);
}

tbody tr:hover {
  background-color: #0971c2;
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2); 
}

.mobile-cards {
  display: none;
  flex-direction: column;
  gap: 1rem;
}

.card {
  background-color: #044b7f;
  padding: 0.8rem;
  border-radius: 8px;
  box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
}

.card p {
  margin: 0.2rem 0;
  font-size: 0.85rem;
}

/* Responsive */
@media (max-width: 768px) {
  .desktop-table {
    display: none;
  }
  .mobile-cards {
    display: flex; 
  }
  h2 {
    font-size: 1.4rem;
  }
  .card p {
    font-size: 0.8rem;
  }
}
</style>

