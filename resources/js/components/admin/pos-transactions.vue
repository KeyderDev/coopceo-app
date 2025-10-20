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
      <button @click="applyDateFilter">Filtrar</button>
      <button @click="resetFilter">Restablecer</button>
    </div>

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
            <td data-label="Total" class="total">${{ sale.total }}</td>
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
      sales: [],
      allSales: [],
      filter: {
        from: '',
        to: ''
      }
    };
  },
  methods: {
    formatDate(utcDate) {
      const date = new Date(utcDate);
      return date.toLocaleString('es-PR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      });
    },

    applyDateFilter() {
      if (!this.filter.from || !this.filter.to) {
        alert('Selecciona ambas fechas para filtrar.');
        return;
      }

      const from = new Date(this.filter.from);
      const to = new Date(this.filter.to);
      to.setHours(23, 59, 59, 999);

      this.sales = this.allSales.filter(sale => {
        const saleDate = new Date(sale.created_at);
        return saleDate >= from && saleDate <= to;
      });
    },

    resetFilter() {
      this.filter.from = '';
      this.filter.to = '';
      this.sales = [...this.allSales];
    }
  },

  async created() {
    try {
      const token = localStorage.getItem('auth_token');
      const response = await axios.get('https://coopceo.ddns.net:8000/api/sales', {
        headers: { Authorization: `Bearer ${token}` }
      });

      console.log('Respuesta API:', response.data);

      const sorted = response.data.sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at)
      );

      this.sales = sorted;
      this.allSales = sorted; 

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
.filter-container {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
  color: #fff;
}

.filter-container input[type="date"] {
  background-color: #044b7f;
  color: #fff;
  border: 1px solid #97d569;
  padding: 0.4rem;
  border-radius: 5px;
}

.filter-container button {
  background-color: #97d569;
  color: #033760;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

.filter-container button:hover {
  background-color: #7ecf52;
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

@media (max-width: 768px) {
  .filter-container {
    flex-direction: column;
    align-items: stretch;
    gap: 0.5rem;
  }

  .filter-container label,
  .filter-container button {
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
    margin-bottom: 1rem;
    border-radius: 10px;
    background: #044b7f;
    padding: 0.75rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  }

  td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-wrap: wrap;
  }

  td::before {
    content: attr(data-label);
    font-weight: bold;
    color: #97d569;
    flex: 1 1 40%;
    max-width: 40%;
  }

  td:last-child {
    border-bottom: none;
  }

  .table-container {
    overflow-x: auto;
  }
}

</style>