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
              <td>${{ formatCurrency(totalAth) }} (Aprox.)</td>
            </tr>
            <tr>
              <td>Total Ventas Efectivo</td>
              <td>${{ formatCurrency(totalEfectivo) }}</td>
            </tr>

            <tr>
              <td>Producto más vendido (general)</td>
              <td>
                {{ prodMasVendido?.producto || '—' }}
                <span v-if="prodMasVendido">({{ prodMasVendido.cantidad }} vendidos)</span>
              </td>
            </tr>

            <tr>
              <td>Producto más vendido del día</td>
              <td>
                {{ prodMasVendidoDia?.producto || '—' }}
                <span v-if="prodMasVendidoDia">({{ prodMasVendidoDia.cantidad }} vendidos)</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="mostrarGraficas" class="charts-container">
        <SalesChart />
        <WeeklySalesChart />
      </div>
    </div>
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
      prodMasVendido: null,
      prodMasVendidoDia: null,
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
        this.prodMasVendido = data.producto_mas_vendido || null;
        this.prodMasVendidoDia = data.producto_mas_vendido_dia || null;
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
    }
  },
};
</script>

<style scoped>
.ganancias-wrapper {
  min-height: 100vh;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem 1rem 6rem;
  font-family: "Inter", sans-serif;
}

.ganancias-header {
  width: 95%;
  max-width: 1100px;
  background: rgba(25, 27, 31, 0.85);
  backdrop-filter: blur(12px);
  border-left: 5px solid #9dd86a;
  border-radius: 16px;
  padding: 1.2rem 1.6rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
  margin-bottom: 1.5rem;
}

.ganancias-header h1 {
  font-size: 1.6rem;
  font-weight: 700;
  color: #9dd86a;
  flex: 1;
  margin-bottom: 0.5rem;
}

.filtros {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  padding: 0.5rem 0.8rem;
}

.filtros label {
  font-weight: 600;
  color: #9dd86a;
}

.filtros input {
  padding: 0.3rem 0.6rem;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(157, 216, 106, 0.3);
  border-radius: 6px;
  color: #fff;
  outline: none;
}

.ganancias-container {
  width: 95%;
  max-width: 1100px;
  background: rgba(25, 27, 31, 0.8);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
  padding: 1.5rem;
}

.tabla-container {
  width: 100%;
  overflow-x: auto;
}

.tabla-ganancias {
  width: 100%;
  border-collapse: collapse;
  font-size: 1rem;
}

.tabla-ganancias thead {
  background: #9dd86a;
  color: #1a1a1a;
}

.tabla-ganancias th,
.tabla-ganancias td {
  padding: 0.9rem 1.2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  text-align: left;
}

.tabla-ganancias tr:hover {
  background: rgba(255, 255, 255, 0.05);
}

.highlight {
  background: rgba(157, 216, 106, 0.1);
  font-weight: bold;
}

.positivo {
  color: #9dd86a;
}

.negativo {
  color: #f44336;
}

.charts-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-top: 1.5rem;
  justify-content: center;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 12px;
  padding: 1rem;
}
</style>
