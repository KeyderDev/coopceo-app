<template>
  <div class="sales-chart-container">
    <h2><i class="fa-solid fa-chart-column"></i> Ventas por hora</h2>

    <div v-if="loaded">
      <apexchart type="bar" height="350" :series="series" :options="chartOptions" />
    </div>
    <p v-else class="loading-text">Cargando datos de ventas...</p>
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
      chart: {
        id: "sales-chart",
        type: "bar",
        toolbar: { show: false },
        background: "transparent",
      },
      grid: {
        borderColor: "rgba(255,255,255,0.1)",
      },
      xaxis: {
        categories: Array.from({ length: 24 }, (_, i) => `${i}:00`),
        labels: { style: { colors: "#b0bec5", fontSize: "12px" } },
      },
      yaxis: {
        labels: {
          formatter: val => val.toFixed(2),
          style: { colors: "#b0bec5" },
        },
      },
      colors: ["#9dd86a"],
      dataLabels: { enabled: false },
      plotOptions: {
        bar: {
          borderRadius: 6,
          columnWidth: "50%",
        },
      },
      tooltip: {
        theme: "dark",
        y: { formatter: val => `$${val.toFixed(2)}` },
      },
      title: {
        text: "",
      },
    });

    const loadSales = async () => {
      try {
        const token = localStorage.getItem("auth_token");
        const response = await axios.get(
          `${import.meta.env.VITE_APP_URL}/api/sales`,
          { headers: { Authorization: `Bearer ${token}` } }
        );

        const today = new Date().toISOString().split("T")[0];
        const todaysSales = response.data.filter(
          s => s.created_at && s.created_at.startsWith(today)
        );

        const salesPerHour = Array.from({ length: 24 }, (_, hour) => {
          const total = todaysSales
            .filter(s => {
              const date = new Date(s.created_at);
              return date.getHours() === hour;
            })
            .reduce((sum, s) => sum + parseFloat(s.total || 0), 0);
          return Number(total.toFixed(2));
        });

        series.value = [{ name: "Ventas", data: salesPerHour }];
        loaded.value = true;
      } catch (err) {
        console.error("Error cargando ventas:", err);
        loaded.value = true;
      }
    };

    onMounted(loadSales);
    return { series, chartOptions, loaded };
  },
});
</script>

<style scoped>
.sales-chart-container {
  background: #111827;
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 14px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
  padding: 2rem;
  margin: 2rem auto;
  width: 85%;
  max-width: 1000px;
  color: #f5f7fa;
  transition: all 0.3s ease;
}

.sales-chart-container h2 {
  color: #9dd86a;
  font-weight: 600;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.loading-text {
  text-align: center;
  color: #9dd86a;
  font-size: 1rem;
  opacity: 0.8;
}

/* 📱 Responsive */
@media (max-width: 768px) {
  .sales-chart-container {
    width: 95%;
    padding: 1rem;
    margin: 1rem auto;
  }

  .sales-chart-container h2 {
    font-size: 1.1rem;
    text-align: center;
  }
}
</style>
