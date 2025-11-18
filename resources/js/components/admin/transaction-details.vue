<template>
  <div class="details-container">
    <div class="card">

      <h2>Transacción #{{ venta.id }}</h2>

      <div class="info-box">
        <p><strong>Cliente:</strong> {{ venta.cliente?.nombre || "Sin nombre" }}</p>
        <p><strong>Cajero:</strong> {{ venta.cajero?.nombre || "Sistema" }}</p>
        <p><strong>Método de Pago:</strong> {{ venta.metodo_pago }}</p>
        <p><strong>Total:</strong> ${{ Number(venta.total).toFixed(2) }}</p>
        <p><strong>Fecha:</strong> {{ formatDate(venta.created_at) }}</p>
      </div>

      <h3 class="subtitle">Productos Comprados</h3>

      <div class="productos-list">
        <div class="producto" v-for="item in items" :key="item.id">
          <span class="nombre">{{ item.nombre }}</span>
          <span class="cantidad">x{{ item.quantity }}</span>
          <span class="subtotal">${{ calcularSubtotal(item) }}</span>
        </div>
      </div>

      <button class="volver" @click="$router.push('/pos-transactions')">
        Volver
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      venta: {},
      items: []
    };
  },

  methods: {
    async cargarVenta() {
      try {
        const id = this.$route.params.id;
        const token = localStorage.getItem("auth_token");

        const response = await axios.get(
          `${import.meta.env.VITE_APP_URL}/api/sales/${id}`,
          { headers: { Authorization: `Bearer ${token}` } }
        );

        this.venta = response.data.sale;
        this.items = response.data.items;
      } catch (error) {
        console.error("Error cargando detalles:", error);
      }
    },

    calcularSubtotal(item) {
      return (Number(item.quantity) * Number(item.precio || 0)).toFixed(2);
    },

    formatDate(date) {
      return new Date(date).toLocaleString("es-PR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit"
      });
    }
  },

  created() {
    this.cargarVenta();
  }
};
</script>

<style scoped>
.details-container {
  width: 100%;
  display: flex;
  justify-content: center;
  padding: 2rem;
}

.card {
  width: 100%;
  max-width: 600px;
  background: rgba(30, 30, 30, 0.9);
  padding: 2rem;
  border-radius: 18px;
  color: white;
  backdrop-filter: blur(8px);
  box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
}

h2 {
  text-align: center;
  font-size: 1.8rem;
  color: #9dd86a;
  margin-bottom: 1rem;
}

.subtitle {
  margin-top: 2rem;
  color: #9dd86a;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  padding-bottom: 4px;
}

.info-box {
  background: rgba(255,255,255,0.05);
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 1rem;
  border: 1px solid rgba(255,255,255,0.1);
}

.productos-list {
  margin-top: 1rem;
}

.producto {
  display: grid;
  grid-template-columns: 1fr 50px 80px;
  padding: 0.7rem 0;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.producto .nombre {
  color: #c7f5a4;
}

.producto .cantidad {
  text-align: center;
}

.producto .subtotal {
  text-align: right;
  color: #b9f089;
  font-weight: bold;
}

.volver {
  margin-top: 2rem;
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  border: none;
  color: black;
  font-weight: bold;
  font-size: 1.1rem;
  border-radius: 12px;
  cursor: pointer;
  transition: 0.2s;
}

.volver:hover {
  transform: translateY(-2px);
}
</style>
