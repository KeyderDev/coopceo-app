<template>
  <div class="ganancias-wrapper">
    <div class="ganancias-header">
      <h2>Ganancias / Pérdidas</h2>
    </div>

    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>Cargando datos...</p>
    </div>

    <div v-else class="ganancias-content">
      <div class="card" v-for="(item, index) in cards" :key="index" :class="item.type">
        <div class="card-info">
          <p>{{ item.label }}</p>
          <span class="amount">
            {{ item.value.toFixed(2) }} USD
            <i v-if="item.type==='ganancia'" class="fas fa-arrow-up"></i>
            <i v-else-if="item.type==='perdida'" class="fas fa-arrow-down"></i>
          </span>
        </div>
        <div class="sparkline">
          <!-- Aquí podrías integrar un pequeño chart tipo sparkline -->
        </div>
      </div>

      <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      totalCompras: 0,
      totalVentas: 0,
      gananciaNeta: 0,
      isLoading: true,
      errorMessage: "",
    };
  },
  computed: {
    cards() {
      return [
        { label: "Total Compras", value: this.totalCompras, type: "neutral" },
        { label: "Total Ventas", value: this.totalVentas, type: "neutral" },
        { 
          label: "Ganancia Neta", 
          value: this.gananciaNeta, 
          type: this.gananciaNeta >= 0 ? "ganancia" : "perdida" 
        },
      ];
    }
  },
  async created() {
    try {
      const token = localStorage.getItem('auth_token');
      const res = await fetch(`${import.meta.env.VITE_APP_URL}/api/ganancias`, {
        headers: { Authorization: `Bearer ${token}` },
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.message || "Error al obtener las ganancias.");

      this.totalCompras = Number(data.total_compras) || 0;
      this.totalVentas = Number(data.total_ventas) || 0;
      this.gananciaNeta = Number(data.ganancia_neta) || 0;
    } catch (e) {
      console.error(e);
      this.errorMessage = e.message;
    } finally {
      this.isLoading = false;
    }
  },
};
</script>

<style scoped>
.ganancias-wrapper {
  background: linear-gradient(135deg, #033961, #041c36);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 3rem 1rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Header */
.ganancias-header h2 {
  color: #fff;
  font-size: 2rem;
  margin-bottom: 2rem;
  text-shadow: 1px 1px 5px rgba(0,0,0,0.6);
}

/* Loading */
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 6px solid rgba(255,255,255,0.2);
  border-top-color: #97d569;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* Cards */
.ganancias-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  width: 100%;
  max-width: 600px;
}

.card {
  background: rgba(255,255,255,0.05);
  backdrop-filter: blur(6px);
  padding: 1.5rem 2rem;
  border-radius: 1rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-shadow: 0 6px 20px rgba(0,0,0,0.35);
  transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.45);
}

.card-info p {
  font-size: 1.1rem;
  color: #ddd;
}

.amount {
  font-size: 1.5rem;
  font-weight: 700;
  color: #fff;
}

.amount i {
  margin-left: 0.5rem;
}

/* Colores por tipo */
.card.ganancia .amount { color: #97d569; }
.card.perdida .amount { color: #ff4d4d; }
.card.neutral .amount { color: #fff; }

/* Error */
.error {
  color: #ff4d4d;
  font-weight: 500;
  margin-top: 1rem;
  text-align: center;
}
</style>
