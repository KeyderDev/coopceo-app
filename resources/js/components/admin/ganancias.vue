<template>
  <div class="ganancias-wrapper">
    <div class="ganancias-header">
      <h1>Resumen de Ganancias</h1>
      <div class="filtros">
        <label for="fecha">Filtrar:</label>
        <input type="date" id="fecha" v-model="fecha" @change="cargarDatos" />
      </div>
    </div>

    <div class="ganancias-container">
      <div v-if="loading" class="loading">Cargando datos...</div>

      <div v-else class="tabla-container">
        <table class="tabla-ganancias">
          <thead>
            <tr>
              <th>Concepto</th>
              <th>Monto / Cantidad</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Total Compras</td>
              <td>${{ formatCurrency(totalCompras) }}</td>
            </tr>
            <tr>
              <td>Total Ventas</td>
              <td>${{ formatCurrency(totalVentas) }}</td>
            </tr>
            <tr class="highlight">
              <td>Ganancia Neta</td>
              <td :class="{ positivo: gananciaNeta >= 0, negativo: gananciaNeta < 0 }">
                ${{ formatCurrency(gananciaNeta) }}
              </td>
            </tr>
            <tr>
              <td>Transacciones ATH Móvil</td>
              <td>{{ transaccionesAth }}</td>
            </tr>
            <tr>
              <td>Transacciones Efectivo</td>
              <td>{{ transaccionesEfectivo }}</td>
            </tr>
            <tr>
              <td>Total Ventas ATH Móvil</td>
              <td>${{ formatCurrency(totalAth) }}</td>
            </tr>
            <tr>
              <td>Total Ventas Efectivo</td>
              <td>${{ formatCurrency(totalEfectivo) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 📈 GRÁFICAS -->
      <div v-if="mostrarGraficas" class="charts-container">
        <SalesChart />
        <WeeklySalesChart />
      </div>
    </div>

    <button class="btn-volver" @click="volverMenu">
      <i class="fa-solid fa-house"></i> Menú Principal
    </button>
  </div>
</template>

<script>
import axios from "axios";
import SalesChart from "../SalesChart.vue";
import WeeklySalesChart from "../WeeklySalesChart.vue";

export default {
  name: "Ganancias",
  components: { SalesChart, WeeklySalesChart },
  data() {
    return {
      fecha: "",
      totalCompras: 0,
      totalVentas: 0,
      gananciaNeta: 0,
      transaccionesAth: 0,
      transaccionesEfectivo: 0,
      totalAth: 0,
      totalEfectivo: 0,
      loading: true,
      mostrarGraficas: true,
    };
  },
  async created() {
    await this.cargarDatos();
  },
  methods: {
    async cargarDatos() {
      this.loading = true;
      const token = localStorage.getItem("auth_token");
      try {
        const res = await axios.get(`${import.meta.env.VITE_APP_URL}/api/ganancias`, {
          params: { fecha: this.fecha || null },
          headers: { Authorization: `Bearer ${token}` },
        });
        const data = res.data;
        this.totalCompras = data.total_compras || 0;
        this.totalVentas = data.total_ventas || 0;
        this.totalEfectivo = data.total_efectivo || 0;
        this.totalAth = data.total_ath_movil || 0;
        this.transaccionesEfectivo = data.transacciones_efectivo || 0;
        this.transaccionesAth = data.transacciones_ath_movil || 0;
        this.gananciaNeta = data.ganancia_neta || 0;
      } catch (error) {
        console.error("Error al cargar datos de ganancias:", error);
      } finally {
        this.loading = false;
      }
    },
    formatCurrency(value) {
      return value.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
    volverMenu() {
      this.$router.push("/");
    },
  },
};
</script>

<style scoped>
.ganancias-wrapper {
  min-height: 100vh;
  background-color: #e0e0e0;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem 0 5rem 0;
}

.ganancias-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 90%;
  max-width: 1200px;
  margin-bottom: 1rem;
  background-color: #4caf50;
  color: white;
  border-radius: 12px;
  padding: 1rem 1.2rem;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
  flex-wrap: wrap;
  gap: 0.8rem;
}

.ganancias-header h1 {
  font-size: 1.5rem;
  font-weight: 700;
  flex: 1;
}

.filtros {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  background-color: white;
  padding: 0.4rem 0.8rem;
  border-radius: 8px;
}

.filtros label {
  color: #333;
  font-weight: 600;
  font-size: 0.9rem;
}

.filtros input {
  padding: 0.3rem 0.5rem;
  border: 1px solid #bbb;
  border-radius: 6px;
}

.ganancias-container {
  width: 90%;
  max-width: 1200px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  padding: 1.2rem;
}

.tabla-container {
  width: 100%;
  overflow-x: auto;
}

.tabla-ganancias {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}

.tabla-ganancias thead {
  background-color: #4caf50;
  color: white;
}

.tabla-ganancias th,
.tabla-ganancias td {
  padding: 0.8rem 1rem;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.highlight {
  background-color: #f0f9f0;
  font-weight: bold;
}

.positivo {
  color: #2e7d32;
  font-weight: 600;
}

.negativo {
  color: #c62828;
  font-weight: 600;
}

.charts-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-top: 1.5rem;
  justify-content: center;
  background: #f9f9f9;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
}

.btn-volver {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 0.7rem 1.2rem;
  border-radius: 10px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
  transition: all 0.25s ease;
  z-index: 1000;
}

.btn-volver:hover {
  background-color: #43a047;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .ganancias-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.8rem;
  }

  .ganancias-header h1 {
    font-size: 1.3rem;
    text-align: center;
    width: 100%;
  }

  .filtros {
    width: 100%;
    justify-content: center;
  }

  .ganancias-container {
    width: 95%;
    padding: 1rem;
  }

  .tabla-ganancias th,
  .tabla-ganancias td {
    padding: 0.7rem;
    font-size: 0.9rem;
  }

  .charts-container {
    padding: 0.8rem;
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .ganancias-header {
    padding: 0.8rem;
  }

  .ganancias-header h1 {
    font-size: 1.1rem;
  }

  .filtros {
    flex-direction: column;
    gap: 0.4rem;
    padding: 0.5rem;
  }

  .tabla-ganancias th,
  .tabla-ganancias td {
    font-size: 0.85rem;
  }

  .btn-volver {
    bottom: 15px;
    right: 15px;
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
  }
}
</style>
