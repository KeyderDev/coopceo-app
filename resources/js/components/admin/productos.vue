<template>
  <div class="products-container">
    <h2>Productos</h2>

    <!-- Formulario -->
    <form @submit.prevent="crearProducto" class="product-form">
      <div class="form-row">
        <div>
          <label>Nombre:</label>
          <input type="text" v-model="nuevoProducto.nombre" required />
        </div>

        <div>
          <label>Precio:</label>
          <input
            type="number"
            v-model.number="nuevoProducto.precio"
            step="0.01"
            required
          />
        </div>

        <div>
          <label>Categoría:</label>
          <input type="text" v-model="nuevoProducto.categoria" required />
        </div>

        <div>
          <label>Stock:</label>
          <input
            type="number"
            v-model.number="nuevoProducto.stock"
            min="0"
            required
          />
        </div>

        <button type="submit">Agregar Producto</button>
      </div>
    </form>

    <!-- Lista de productos -->
    <h3>Productos existentes</h3>

    <!-- Vista tipo tabla en pantallas grandes -->
    <div class="table-wrapper desktop-only">
      <table class="products-table">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Categoría</th>
            <th>Stock</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="producto in productos" :key="producto.id">
            <td>{{ producto.nombre }}</td>
            <td>${{ Number(producto.precio).toFixed(2) }}</td>
            <td>{{ producto.categoria }}</td>
            <td>
              <input
                type="number"
                v-model.number="producto.stock"
                min="0"
                class="stock-input"
              />
            </td>
            <td>
              <button
                class="save-btn"
                @click="actualizarStock(producto)"
              >
                Guardar
              </button>
              <button class="delete-btn" @click="eliminarProducto(producto.id)">
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Vista tipo tarjetas en móviles -->
    <div class="mobile-cards mobile-only">
      <div class="cards-scroll">
        <div
          v-for="producto in productos"
          :key="producto.id"
          class="product-card"
        >
          <div class="info"><strong>Nombre:</strong> {{ producto.nombre }}</div>
          <div class="info">
            <strong>Precio:</strong> ${{ Number(producto.precio).toFixed(2) }}
          </div>
          <div class="info">
            <strong>Categoría:</strong> {{ producto.categoria }}
          </div>
          <div class="info">
            <strong>Stock:</strong>
            <input
              type="number"
              v-model.number="producto.stock"
              min="0"
              class="stock-input"
            />
          </div>
          <button
            class="save-btn"
            @click="actualizarStock(producto)"
          >
            Guardar
          </button>
          <button class="delete-btn" @click="eliminarProducto(producto.id)">
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      productos: [],
      nuevoProducto: { nombre: "", precio: 0, categoria: "", stock: 0 },
    };
  },
  created() {
    this.obtenerProductos();
  },
  methods: {
    async obtenerProductos() {
      try {
        const res = await axios.get("/api/products");
        this.productos = res.data;
      } catch (error) {
        console.error("Error al obtener productos:", error);
      }
    },
    async crearProducto() {
      try {
        const res = await axios.post("/api/products", this.nuevoProducto);
        this.productos.push(res.data);
        this.nuevoProducto = { nombre: "", precio: 0, categoria: "", stock: 0 };
      } catch (error) {
        console.error("Error al crear producto:", error);
      }
    },
    async eliminarProducto(id) {
      if (!confirm("¿Seguro que deseas eliminar este producto?")) return;
      try {
        await axios.delete(`/api/products/${id}`);
        this.productos = this.productos.filter((p) => p.id !== id);
      } catch (error) {
        console.error("Error al eliminar producto:", error);
      }
    },
    async actualizarStock(producto) {
      try {
        producto.loading = true;
        const token = localStorage.getItem("auth_token");
        await axios.put(`/api/products/${producto.id}`, { stock: producto.stock }, {
          headers: { Authorization: `Bearer ${token}` },
        });
        alert(`Stock actualizado para ${producto.nombre}`);
      } catch (err) {
        alert("Error al actualizar stock");
        console.error(err);
      } finally {
        producto.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.products-container {
  max-width: 1200px; /* 🔥 Más ancho en desktop */
  margin: 3rem auto;
  padding: 2.5rem;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  background-color: #ffffff;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(3, 56, 97, 0.08);
  color: #033861;
  transition: all 0.3s ease;
}

h2,
h3 {
  color: #033861;
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.stock-input {
  width: 80px;
  padding: 0.4rem;
  border-radius: 6px;
  border: 1px solid #cfd8dc;
}

.save-btn {
  padding: 0.4rem 0.8rem;
  background-color: #097969;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  margin-right: 0.5rem;
  cursor: pointer;
  transition: 0.3s;
}

.save-btn:hover {
  background-color: #064d3f;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 77, 63, 0.3);
}

/* === FORMULARIO (desktop mejorado) === */
.product-form {
  margin-bottom: 2.5rem;
}

.form-row {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  align-items: flex-end;
}

.form-row > div {
  flex: 1;
  min-width: 220px;
}

.product-form label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #033861;
}

.product-form input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #cfd8dc;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: 0.3s;
}

.product-form input:focus {
  border-color: #97d569;
  box-shadow: 0 0 8px rgba(151, 213, 105, 0.3);
  outline: none;
}

button {
  padding: 0.75rem 1.5rem;
  background-color: #97d569;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s ease;
}

button:hover {
  background-color: #033861;
  transform: translateY(-2px);
  box-shadow: 0 6px 14px rgba(3, 56, 97, 0.25);
}

/* === TABLA (desktop) === */
.table-wrapper {
  overflow-x: auto;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  background: #fdfdfd;
}

.products-table {
  width: 100%;
  border-collapse: collapse;
}

.products-table th {
  background-color: #033861;
  color: #97d569;
  padding: 1rem;
  font-weight: 600;
  text-align: left;
  font-size: 1rem;
}

.products-table td {
  padding: 1rem;
  border-bottom: 1px solid #e6e6e6;
  font-size: 0.95rem;
}

.products-table tr:hover {
  background-color: #f5fbf2;
  transition: background-color 0.3s;
}

.delete-btn {
  padding: 0.5rem 1rem;
  background-color: #ff4d4f;
  color: #ffffff;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.delete-btn:hover {
  background-color: #c41d1d;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(196, 29, 29, 0.3);
}

/* === TARJETAS (móvil) === */
.mobile-cards {
  display: none;
}

.cards-scroll {
  max-height: 60vh;
  overflow-y: auto;
  padding-right: 0.5rem;
  scrollbar-width: thin;
  scrollbar-color: #97d569 #f5f5f5;
}

.cards-scroll::-webkit-scrollbar {
  width: 8px;
}

.cards-scroll::-webkit-scrollbar-thumb {
  background-color: #97d569;
  border-radius: 8px;
}

.cards-scroll::-webkit-scrollbar-track {
  background-color: #f5f5f5;
}

.product-card {
  background: #f8f9fa;
  border: 1px solid #dcdcdc;
  border-radius: 10px;
  padding: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.product-card .info {
  margin-bottom: 0.5rem;
  color: #033861;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .desktop-only {
    display: none;
  }

  .mobile-cards {
    display: block;
  }

  .form-row {
    flex-direction: column;
  }

  .products-container {
    padding: 1.5rem;
    margin: 1rem;
  }

  button {
    width: 100%;
    text-align: center;
  }
}
</style>
