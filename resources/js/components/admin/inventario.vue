<template>
  <div class="cuadre-container">
    <h2>Cuadre de Productos</h2>

    <!-- Encabezados -->
    <div class="cuadre-wrapper encabezados">
      <div class="cuadre-nombre">Nombre</div>
      <div class="cuadre-stock">Stock esperado</div>
      <div>Contado</div>
      <div>Diferencia</div>
    </div>

    <!-- Filas de productos -->
    <div class="cuadre-wrapper">
      <div class="cuadre-item" v-for="producto in productos" :key="producto.id">
        <div class="cuadre-nombre">{{ producto.nombre }}</div>
        <div class="cuadre-stock">{{ producto.stock }}</div>
        <div>
          <input type="number" v-model.number="producto.contado" min="0" />
        </div>
        <div :class="{'positivo': producto.contado === producto.stock, 'negativo': producto.contado !== producto.stock}">
          {{ producto.contado != null ? producto.contado - producto.stock : '' }}
        </div>
      </div>
    </div>

    <button @click="guardarCuadre" class="guardar-cuadre-btn">Guardar Cuadre</button>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      productos: [],
    };
  },
  async created() {
    await this.obtenerProductos();
    await this.obtenerCuadreAnterior();
  },
  methods: {
    async obtenerProductos() {
      try {
        const token = localStorage.getItem("auth_token");
        const res = await axios.get("/api/products", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.productos = res.data.map(p => ({ ...p, contado: null }));
      } catch (err) {
        console.error("Error al obtener productos:", err);
      }
    },

    async obtenerCuadreAnterior() {
      try {
        const token = localStorage.getItem("auth_token");
        const res = await axios.get("/api/products/cuadre", {
          headers: { Authorization: `Bearer ${token}` },
        });
        // Asignamos los valores anteriores de contado si existen
        this.productos = this.productos.map(p => {
          const cuadre = res.data.find(c => c.product_id === p.id);
          if (cuadre) p.contado = cuadre.contado;
          return p;
        });
      } catch (err) {
        console.warn("No se encontró cuadre anterior");
      }
    },

    async guardarCuadre() {
      try {
        const token = localStorage.getItem("auth_token");
        const cuadre = this.productos.map(p => ({
          id: p.id,
          stock: p.stock,
          contado: p.contado
        }));
        await axios.post("/api/products/cuadre", { cuadre }, {
          headers: { Authorization: `Bearer ${token}` },
        });
        alert("Cuadre guardado correctamente");
      } catch (err) {
        alert("Error al guardar cuadre");
        console.error(err);
      }
    },
  },
};
</script>

<style scoped>
.cuadre-container {
  padding: 1.5rem;
  background-color: #043c67;
  border-radius: 12px;
  color: #fff;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

h2 {
  color: #97d569;
  text-align: center;
  margin-bottom: 1rem;
}

.cuadre-wrapper {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 10px;
  padding: 0.5rem;
  border-radius: 8px;
}

.encabezados {
  background-color: #97d569;
  color: #043c67;
  font-weight: bold;
  text-align: center;
}

.cuadre-item {
  display: contents;
}

.cuadre-nombre, .cuadre-stock, .cuadre-item input, .cuadre-item div:last-child {
  padding: 0.4rem;
  background-color: #1e6191;
  border-radius: 6px;
  text-align: center;
  color: #fff;
}

.positivo {
  background-color: #97d569;
  color: #043c67;
  font-weight: bold;
}

.negativo {
  background-color: #ff6b6b;
  font-weight: bold;
}

.guardar-cuadre-btn {
  margin-top: 1rem;
  display: block;
  width: 100%;
  background-color: #043c67;
  color: #97d569;
  font-weight: bold;
  padding: 0.6rem;
  border-radius: 8px;
}

/* Responsive */
@media (max-width: 600px) {
  .cuadre-wrapper {
    display: block;
  }
  .cuadre-item > * {
    margin-bottom: 6px;
  }
  input[type="number"] {
    width: 100%;
  }
}
</style>
