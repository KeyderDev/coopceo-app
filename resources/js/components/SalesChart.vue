<template>
  <div class="sales-chart-container">
    <div v-if="loaded">
      <apexchart
        type="bar"
        height="350"
        :series="series"
        :options="chartOptions"
      />
    </div>
    <p v-else>Cargando datos de ventas...</p>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";

export default defineComponent({
  name: "SalesChart",
  components: { apexchart: VueApexCharts },
  setup() {
    const loaded = ref(false);
    const series = ref([{ name: "Ventas", data: Array(24).fill(0) }]);
    const chartOptions = ref({
      chart: { id: "sales-chart", type: "bar" },
      xaxis: { categories: Array.from({ length: 24 }, (_, i) => `${i}:00`) },
      colors: ["#97d569"],
      title: { text: "Ventas por hora", style: { color: "#044271", fontSize: "16px" } },
      dataLabels: { enabled: false },
      yaxis: { labels: { formatter: val => val.toFixed(2) } },
    });

    const loadSales = async () => {
      try {
        console.log("🔹 Cargando ventas...");
        const token = localStorage.getItem("auth_token");

        const response = await axios.get(
          "https://coopceo.ddns.net:8000/api/sales",
          { headers: { Authorization: `Bearer ${token}` } }
        );

        console.log("🔹 Datos recibidos de la API:", response.data);

        if (!Array.isArray(response.data)) {
          console.error("❌ La respuesta no es un array:", response.data);
          loaded.value = true;
          return;
        }

        const today = new Date().toISOString().split("T")[0];
        const todaysSales = response.data.filter(s => {
          if (!s.created_at) return false;
          return s.created_at.startsWith(today);
        });

        console.log("🔹 Ventas de hoy:", todaysSales);

        const salesPerHour = Array.from({ length: 24 }, (_, hour) => {
          const total = todaysSales
            .filter(s => {
              const date = new Date(s.created_at);
              if (isNaN(date)) {
                console.warn("⚠️ Fecha inválida:", s.created_at);
                return false;
              }
              return date.getHours() === hour;
            })
            .reduce((sum, s) => sum + parseFloat(s.total || 0), 0);
          return Number(total.toFixed(2));
        });

        console.log("🔹 Ventas por hora calculadas:", salesPerHour);

        series.value = [{ name: "Ventas", data: salesPerHour }];
        chartOptions.value = {
          ...chartOptions.value,
          xaxis: { categories: Array.from({ length: 24 }, (_, i) => `${i}:00`) }
        };

        loaded.value = true;
        console.log("✅ Gráfica lista para mostrar");
      } catch (err) {
        console.error("❌ Error cargando ventas:", err);
        loaded.value = true;
      }
    };

    onMounted(loadSales);

    return { series, chartOptions, loaded };
  },
});
</script>

<style>
.sales-chart-container {
  background: #fff;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  margin: 2rem 0 0 2rem; /* sube el div y lo mueve a la izquierda */
  width: 80%; /* un poco más ancho si quieres */
  max-width: 1000px;
}
.sales-chart-container h2 {
  text-align: left; /* alinea el título a la izquierda */
  margin-bottom: 1rem;
  color: #044271;
}

@media (max-width: 600px) {
  .sales-chart-container {
    padding: 1rem;
    margin: 1rem 0;
  }
}
</style>
