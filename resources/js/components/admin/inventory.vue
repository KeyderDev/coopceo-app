<template>
  <div class="inventory-wrapper">
    <div class="inventory-container">
      <h2><i class="fa-solid fa-boxes-stacked"></i> Gestión de Inventario</h2>

      <!-- === CREAR PRODUCTO === -->
      <section class="product-form-section">
        <h3>Agregar Nuevo Producto</h3>
        <form @submit.prevent="crearProducto" class="product-form">
          <div class="form-grid">
            <div class="form-group">
              <label>Nombre:</label>
              <input type="text" v-model="nuevoProducto.nombre" required />
            </div>

            <div class="form-group">
              <label>Precio:</label>
              <input type="number" step="0.01" v-model.number="nuevoProducto.precio" required />
            </div>

            <div class="form-group">
              <label>Categoría:</label>
              <input type="text" v-model="nuevoProducto.categoria" required />
            </div>

            <div class="form-group">
              <label>Stock:</label>
              <input type="number" v-model.number="nuevoProducto.stock" required />
            </div>
          </div>

          <button type="submit" class="btn-primary">Agregar Producto</button>
        </form>
      </section>

      <hr class="divider" />

      <!-- === LISTA DE PRODUCTOS === -->
      <section class="product-list-section">
        <h3>Productos Existentes</h3>

        <!-- Tabla escritorio -->
        <table class="product-table desktop-only">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Categoría</th>
              <th>Precio</th>
              <th>Stock</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="producto in productos" :key="producto.id">
              <td>{{ producto.nombre }}</td>
              <td>{{ producto.categoria }}</td>
              <td>${{ Number(producto.precio || 0).toFixed(2) }}</td>
              <td>
                <input type="number" v-model.number="producto.stock" min="0" class="stock-input" />
              </td>
              <td class="actions">
                <button class="btn-primary" @click="actualizarStock(producto)" :disabled="producto.loading">
                  {{ producto.loading ? "Guardando..." : "Actualizar" }}
                </button>
                <button class="btn-danger" @click="eliminarProducto(producto.id)">
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Vista móvil -->
        <div class="mobile-cards">
          <div class="product-card" v-for="producto in productos" :key="'m-' + producto.id">
            <h4>{{ producto.nombre }}</h4>
            <p><strong>Categoría:</strong> {{ producto.categoria }}</p>
            <p><strong>Precio:</strong> ${{ Number(producto.precio || 0).toFixed(2) }}</p>
            <p><strong>Stock:</strong></p>
            <input type="number" v-model.number="producto.stock" min="0" class="stock-input" />
            <div class="card-actions">
              <button class="btn-primary" @click="actualizarStock(producto)" :disabled="producto.loading">
                {{ producto.loading ? "Guardando..." : "Actualizar" }}
              </button>
              <button class="btn-danger" @click="eliminarProducto(producto.id)">
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </section>

      <hr class="divider" />

      <!-- === CUADRE === -->
      <section class="inventory-check-section">
        <h3>Cuadre de Inventario</h3>
        <div class="inventory-check-table">
          <div class="header-row">
            <span>Nombre</span>
            <span>Stock Esperado</span>
            <span>Contado</span>
            <span>Diferencia</span>
          </div>

          <div class="item-row" v-for="producto in productos" :key="'cuadre-' + producto.id">
            <span>{{ producto.nombre }}</span>
            <span>{{ producto.stock }}</span>
            <input type="number" v-model.number="producto.contado" placeholder="Contado" min="0" />
            <span :class="{
              match: producto.contado === producto.stock,
              mismatch:
                producto.contado !== producto.stock && producto.contado != null,
            }">
              {{ producto.contado != null ? producto.contado - producto.stock : "-" }}
            </span>
          </div>
        </div>

        <button @click="guardarCuadre" class="btn-primary">Guardar Cuadre</button>
      </section>

    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "InventarioView",
  data() {
    return {
      productos: [],
      nuevoProducto: {
        nombre: "",
        precio: null,
        categoria: "",
        stock: null,
      },
    };
  },
  async created() {
    await this.cargarProductos();
  },
  methods: {
    async cargarProductos() {
      const token = localStorage.getItem("auth_token");
      try {
        const response = await axios.get(`${import.meta.env.VITE_APP_URL}/api/products`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.productos = response.data.map((p) => ({ ...p, loading: false }));
      } catch (error) {
        console.error("Error al cargar productos:", error);
      }
    },

    async crearProducto() {
      const token = localStorage.getItem("auth_token");
      try {
        await axios.post(`${import.meta.env.VITE_APP_URL}/api/products`, this.nuevoProducto, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.nuevoProducto = { nombre: "", precio: null, categoria: "", stock: null };
        await this.cargarProductos();
      } catch (error) {
        console.error("Error al crear producto:", error);
      }
    },

    async eliminarProducto(id) {
      if (!confirm("¿Seguro que deseas eliminar este producto?")) return;
      const token = localStorage.getItem("auth_token");
      try {
        await axios.delete(`${import.meta.env.VITE_APP_URL}/api/products/${id}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.productos = this.productos.filter((p) => p.id !== id);
      } catch (error) {
        console.error("Error al eliminar producto:", error);
      }
    },

    volverMenu() {
      this.$router.push("/");
    },

    async actualizarStock(producto) {
      try {
        producto.loading = true;
        const token = localStorage.getItem("auth_token");
        await axios.put(
          `${import.meta.env.VITE_APP_URL}/api/products/${producto.id}`,
          { stock: producto.stock },
          { headers: { Authorization: `Bearer ${token}` } }
        );
        alert(`Stock actualizado para ${producto.nombre}`);
      } catch (err) {
        alert("Error al actualizar stock");
        console.error(err);
      } finally {
        producto.loading = false;
      }
    },

    async guardarCuadre() {
      const token = localStorage.getItem("auth_token");

      const cuadrePayload = this.productos.map((p) => ({
        id: p.id,
        stock: p.stock,
        contado: p.contado ?? null,
      }));

      try {
        await axios.post(
          `${import.meta.env.VITE_APP_URL}/api/products/cuadre`,
          { cuadre: cuadrePayload },
          { headers: { Authorization: `Bearer ${token}` } }
        );

        alert("Cuadre de inventario guardado correctamente ✅");
      } catch (error) {
        console.error("Error al guardar el cuadre:", error);
        alert("Hubo un error guardando el cuadre ❌");
      }
    },
  },
};
</script>

<style scoped>
/* 🌌 Fondo general */
.inventory-wrapper {
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  min-height: 100vh;
  padding: 2rem;
  color: #fff;
  font-family: "Inter", sans-serif;
  box-sizing: border-box;
}

/* 🧩 Contenedor principal */
.inventory-container {
  background: rgba(25, 27, 31, 0.9);
  backdrop-filter: blur(12px);
  border-radius: 18px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
  padding: 2rem;
  animation: fadeIn 0.6s ease-in-out;
}

/* ✨ Títulos */
h2 {
  text-align: center;
  color: #9dd86a;
  margin-bottom: 1.8rem;
  font-size: 1.8rem;
}

h3 {
  color: #9dd86a;
  margin-bottom: 1rem;
  font-size: 1.2rem;
}

/* === FORMULARIO === */
.product-form-section {
  background: rgba(157, 216, 106, 0.05);
  border-radius: 15px;
  padding: 1.3rem;
  border: 1px solid rgba(157, 216, 106, 0.15);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1rem;
}

.form-group label {
  font-weight: 600;
  color: #9dd86a;
  display: block;
  margin-bottom: 0.3rem;
}

input[type="text"],
input[type="number"] {
  border: 1px solid rgba(157, 216, 106, 0.25);
  border-radius: 8px;
  padding: 0.7rem;
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  width: 100%;
  font-size: 0.95rem;
}

input:focus {
  outline: none;
  border-color: #9dd86a;
  box-shadow: 0 0 6px rgba(157, 216, 106, 0.5);
}

/* === BOTONES === */
.btn-primary,
.btn-danger {
  border: none;
  border-radius: 8px;
  padding: 0.7rem 1.2rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
  font-size: 0.95rem;
}

.btn-primary {
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-2px);
}

.btn-danger {
  background: linear-gradient(135deg, #e53935, #b71c1c);
  color: #fff;
}

.btn-danger:hover {
  filter: brightness(1.2);
  transform: translateY(-2px);
}

/* === TABLA (desktop) === */
.product-list-section {
  margin-top: 2rem;
  background: rgba(157, 216, 106, 0.05);
  border-radius: 15px;
  padding: 1.5rem;
  border: 1px solid rgba(157, 216, 106, 0.15);
}

.product-table {
  width: 100%;
  border-collapse: collapse;
  color: #fff;
}

.product-table th {
  background: rgba(157, 216, 106, 0.15);
  color: #9dd86a;
  text-transform: uppercase;
}

.product-table th,
.product-table td {
  padding: 0.8rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  text-align: left;
}

/* === STOCK INPUT === */
.stock-input {
  width: 80px;
  padding: 0.4rem;
  border-radius: 6px;
  border: 1px solid rgba(157, 216, 106, 0.3);
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  text-align: center;
  font-size: 0.9rem;
}

/* === CARDS (móvil) === */
.mobile-cards {
  display: none;
}

.product-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.product-card h4 {
  color: #9dd86a;
  font-size: 1.1rem;
  margin-bottom: 0.3rem;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: space-between;
  flex-wrap: wrap;
}

/* === CUADRE === */
.inventory-check-section {
  margin-top: 2rem;
  background: rgba(157, 216, 106, 0.05);
  border-radius: 15px;
  padding: 1.5rem;
  border: 1px solid rgba(157, 216, 106, 0.15);
}

.inventory-check-table {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  overflow-x: auto;
}

.header-row,
.item-row {
  display: grid;
  grid-template-columns: repeat(4, minmax(80px, 1fr));
  gap: 0.6rem;
  align-items: center;
}

.header-row {
  font-weight: bold;
  border-bottom: 2px solid #9dd86a;
  padding-bottom: 0.5rem;
  color: #9dd86a;
  font-size: 0.9rem;
}

.item-row span,
.item-row input {
  font-size: 0.9rem;
}

.match {
  color: #9dd86a;
  font-weight: 600;
}

.mismatch {
  color: #f44336;
  font-weight: 600;
}

/* === BOTÓN VOLVER === */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 25px;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  border: none;
  padding: 0.9rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
  transition: all 0.3s ease;
  z-index: 1000;
}

.btn-volver:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-3px);
}

/* === DIVIDER === */
.divider {
  border: none;
  height: 1px;
  margin: 2rem 0;
  background: rgba(255, 255, 255, 0.1);
}

/* 📱 RESPONSIVE DESIGN */
@media (max-width: 900px) {
  .desktop-only {
    display: none;
  }

  .mobile-cards {
    display: block;
  }

  .inventory-wrapper {
    padding: 1.2rem;
  }

  .inventory-container {
    padding: 1.2rem;
    border-radius: 15px;
  }

  h2 {
    font-size: 1.5rem;
  }

  h3 {
    font-size: 1rem;
  }

  input[type="text"],
  input[type="number"] {
    padding: 0.8rem;
    font-size: 0.95rem;
  }

  .btn-primary,
  .btn-danger {
    width: 100%;
    margin-top: 0.5rem;
    padding: 0.8rem;
    font-size: 1rem;
  }

  .card-actions {
    flex-direction: column;
  }

  .inventory-check-table {
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .inventory-wrapper {
    padding: 0.8rem;
  }

  .inventory-container {
    padding: 1rem;
  }

  h2 {
    font-size: 1.3rem;
  }

  h3 {
    font-size: 0.95rem;
  }

  .product-card {
    padding: 0.9rem;
  }

  .stock-input {
    width: 100%;
  }

  .btn-volver {
    position: static;
    display: block;
    width: 100%;
    margin-top: 1rem;
    transform: none;
  }
}

/* ✨ Animación */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
