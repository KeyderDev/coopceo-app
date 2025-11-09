<template>
  <div class="sales-chart-container">
    <h2><i class="fa-solid fa-calendar-week"></i> Ventas por día de la semana</h2>

    <div v-if="loaded">
      <apexchart type="bar" height="350" :series="series" :options="chartOptions" />
    </div>
    <p v-else class="loading-text">Cargando datos de ventas de la semana...</p>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";

export default defineComponent({
  name: "WeeklySalesChart",
  components: { apexchart: VueApexCharts },
  setup() {
    const loaded = ref(false);
    const series = ref([{ name: "Ventas", data: Array(7).fill(0) }]);

    const chartOptions = ref({
      chart: {
        id: "weekly-sales-chart",
        type: "bar",
        toolbar: { show: false },
        background: "transparent",
      },
      grid: {
        borderColor: "rgba(255,255,255,0.1)",
      },
      xaxis: {
        categories: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
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
    });

    const loadWeeklySales = async () => {
      try {
        const token = localStorage.getItem("auth_token");
        const response = await axios.get(
          `${import.meta.env.VITE_APP_URL}/api/sales`,
          { headers: { Authorization: `Bearer ${token}` } }
        );

        const sales = Array(7).fill(0);
        const today = new Date();
        const dayOfWeekToday = today.getDay();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - dayOfWeekToday);

        response.data.forEach(sale => {
          const saleDate = new Date(sale.created_at);
          const diffTime = saleDate.getTime() - startOfWeek.getTime();
          const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

          if (diffDays >= 0 && diffDays < 7) {
            const dayIndex = saleDate.getDay();
            sales[dayIndex] += parseFloat(sale.total || 0);
          }
        });

        series.value = [{ name: "Ventas", data: sales.map(v => Number(v.toFixed(2))) }];
        loaded.value = true;
      } catch (error) {
        console.error("❌ Error cargando ventas semanales:", error);
        loaded.value = true;
      }
    };

    onMounted(loadWeeklySales);
    return { loaded, series, chartOptions };
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
