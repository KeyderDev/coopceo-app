<template>
  <div class="ganancias-wrapper">
    <div class="ganancias-header">
      <h2>Ganancias</h2>
    </div>

    <!-- 🔹 Filtro por fecha -->
    <div class="filtro-fecha">
      <label for="fecha">Seleccionar fecha:</label>
      <input
        type="date"
        id="fecha"
        v-model="fechaSeleccionada"
        @change="fetchGananciasPorFecha"
      />
    </div>

    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>Cargando datos...</p>
    </div>

    <div v-else class="ganancias-secciones">
      <!-- 🔹 Totales principales -->
      <div class="ganancias-content principal">
        <div
          class="card"
          v-for="(item, index) in cardsPrincipales"
          :key="index"
          :class="item.type"
        >
          <div class="card-info">
            <p>{{ item.label }}</p>
            <span class="amount">
              {{ item.value.toFixed(2) }} USD
              <i v-if="item.type === 'ganancia'" class="fas fa-arrow-up"></i>
              <i v-else-if="item.type === 'perdida'" class="fas fa-arrow-down"></i>
            </span>
          </div>
        </div>
      </div>

      <!-- 🔹 Totales por método -->
      <div class="ganancias-content metodos">
        <div
          class="card"
          v-for="(item, index) in cardsMetodos"
          :key="index"
          :class="item.type"
        >
          <div class="card-info">
            <p>{{ item.label }}</p>
            <span class="amount">{{ item.value.toFixed(2) }} USD</span>
          </div>
        </div>
      </div>

      <!-- 🔹 Transacciones por método -->
      <div class="ganancias-content transacciones">
        <div
          class="card"
          v-for="(item, index) in cardsTransacciones"
          :key="index"
          :class="item.type"
        >
          <div class="card-info">
            <p>{{ item.label }}</p>
            <span class="amount">{{ item.value }}</span>
          </div>
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
      totalEfectivo: 0,
      totalAthMovil: 0,
      transaccionesEfectivo: 0,
      transaccionesAthMovil: 0,
      gananciaNeta: 0,
      fechaSeleccionada: "",
      isLoading: true,
      errorMessage: "",
    };
  },
  computed: {
    cardsPrincipales() {
      return [
        { label: "Total Compras", value: this.totalCompras, type: "neutral" },
        { label: "Total Ventas", value: this.totalVentas, type: "neutral" },
        {
          label: "Ganancia Neta",
          value: this.gananciaNeta,
          type: this.gananciaNeta >= 0 ? "ganancia" : "perdida",
        },
      ];
    },
    cardsMetodos() {
      return [
        { label: "Ventas en Efectivo", value: this.totalEfectivo, type: "neutral" },
        { label: "Ventas por ATH Móvil", value: this.totalAthMovil, type: "neutral" },
      ];
    },
    cardsTransacciones() {
      return [
        { label: "Transacciones en Efectivo", value: this.transaccionesEfectivo, type: "neutral" },
        { label: "Transacciones por ATH Móvil", value: this.transaccionesAthMovil, type: "neutral" },
      ];
    },
  },
  async created() {
    await this.fetchGanancias();
  },
  methods: {
    async fetchGanancias(fecha = null) {
      try {
        this.isLoading = true;
        const token = localStorage.getItem("auth_token");
        let url = `${import.meta.env.VITE_APP_URL}/api/ganancias`;

        if (fecha) {
          url += `?fecha=${fecha}`;
        }

        const res = await fetch(url, {
          headers: { Authorization: `Bearer ${token}` },
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.message || "Error al obtener las ganancias.");

        this.totalCompras = Number(data.total_compras) || 0;
        this.totalVentas = Number(data.total_ventas) || 0;
        this.totalEfectivo = Number(data.total_efectivo) || 0;
        this.totalAthMovil = Number(data.total_ath_movil) || 0;
        this.transaccionesEfectivo = Number(data.transacciones_efectivo) || 0;
        this.transaccionesAthMovil = Number(data.transacciones_ath_movil) || 0;
        this.gananciaNeta = Number(data.ganancia_neta) || 0;
      } catch (e) {
        console.error(e);
        this.errorMessage = e.message;
      } finally {
        this.isLoading = false;
      }
    },

    async fetchGananciasPorFecha() {
      if (!this.fechaSeleccionada) return;
      await this.fetchGanancias(this.fechaSeleccionada);
    },
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
  padding: 3rem 1.2rem;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  box-sizing: border-box;
}

/* Header */
.ganancias-header h2 {
  color: #fff;
  font-size: 2rem;
  margin-bottom: 2rem;
  text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.6);
  text-align: center;
}

/* Filtro */
.filtro-fecha {
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  color: #fff;
}

.filtro-fecha label {
  font-size: 1rem;
}

.filtro-fecha input {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: #fff;
  padding: 0.5rem;
  border-radius: 0.5rem;
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
  border: 6px solid rgba(255, 255, 255, 0.2);
  border-top-color: #97d569;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Secciones */
.ganancias-secciones {
  display: flex;
  flex-direction: column;
  gap: 3rem;
  width: 100%;
  max-width: 850px;
}

/* Layout grid */
.ganancias-content {
  display: grid;
  gap: 1.5rem;
}
.ganancias-content.principal {
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
}
.ganancias-content.metodos,
.ganancias-content.transacciones {
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

/* Cards */
.card {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(8px);
  padding: 1.7rem 2rem;
  border-radius: 1rem;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
  transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.5);
}

.card-info p {
  font-size: 1.15rem;
  color: #ddd;
}

.amount {
  font-size: 1.7rem;
  font-weight: 700;
  color: #fff;
  margin-top: 0.5rem;
}

/* Colores */
.card.ganancia .amount {
  color: #97d569;
}
.card.perdida .amount {
  color: #ff4d4d;
}

/* Error */
.error {
  color: #ff4d4d;
  font-weight: 500;
  margin-top: 1rem;
  text-align: center;
}

/* 📱 Responsivo */
@media (max-width: 768px) {
  .ganancias-wrapper {
    padding: 2rem 1rem;
  }

  .ganancias-header h2 {
    font-size: 1.6rem;
  }

  .ganancias-content {
    grid-template-columns: 1fr;
  }

  .card {
    padding: 1.2rem 1.4rem;
  }

  .amount {
    font-size: 1.4rem;
  }
}
</style>
