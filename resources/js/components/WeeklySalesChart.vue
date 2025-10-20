<template>
    <div class="sales-chart-container">
        <div v-if="loaded">
            <apexchart type="bar" height="350" :series="series" :options="chartOptions" />
        </div>
        <p v-else>Cargando datos de ventas de la semana...</p>
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
            chart: { id: "weekly-sales-chart", type: "bar" },
            xaxis: { categories: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"] },
            colors: ["#97d569"],
            title: { text: "Ventas por día de la semana", style: { color: "#044271", fontSize: "16px" } },
            dataLabels: { enabled: false },
            yaxis: { labels: { formatter: val => val.toFixed(2) } },
        });

        const loadWeeklySales = async () => {
            try {
                const token = localStorage.getItem("auth_token");
                const response = await axios.get("https://coopceo.ddns.net:8000/api/sales", {
                    headers: { Authorization: `Bearer ${token}` }
                });

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
                console.error("Error cargando ventas semanales:", error);
                loaded.value = true;
            }
        };

        onMounted(loadWeeklySales);

        return { loaded, series, chartOptions };
    }
});
</script>

<style>
.sales-chart-container {
    background: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    margin: 2rem 0 0 2rem;
    width: 80%;
    max-width: 1000px;
}

.sales-chart-container h2 {
    text-align: left;
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
