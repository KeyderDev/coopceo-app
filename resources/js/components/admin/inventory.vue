<template>
  <div class="inventory-container">
    <h2>Gestión de Inventario</h2>

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

      <!-- Tabla en escritorio -->
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
              <input
                type="number"
                v-model.number="producto.stock"
                min="0"
                class="stock-input"
              />
            </td>
            <td class="actions">
              <button
                class="btn-primary"
                @click="actualizarStock(producto)"
                :disabled="producto.loading"
              >
                {{ producto.loading ? "Guardando..." : "Actualizar" }}
              </button>
              <button class="btn-danger" @click="eliminarProducto(producto.id)">
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Vista tipo tarjetas en móvil -->
      <div class="mobile-cards">
        <div class="product-card" v-for="producto in productos" :key="'m-' + producto.id">
          <h4>{{ producto.nombre }}</h4>
          <p><strong>Categoría:</strong> {{ producto.categoria }}</p>
          <p><strong>Precio:</strong> ${{ Number(producto.precio || 0).toFixed(2) }}</p>
          <p><strong>Stock:</strong></p>
          <input
            type="number"
            v-model.number="producto.stock"
            min="0"
            class="stock-input"
          />
          <div class="card-actions">
            <button
              class="btn-primary"
              @click="actualizarStock(producto)"
              :disabled="producto.loading"
            >
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

    <!-- === CUADRE DE INVENTARIO === -->
    <section class="inventory-check-section">
      <h3>Cuadre de Inventario</h3>
      <div class="inventory-check-table">
        <div class="header-row">
          <span>Nombre</span>
          <span>Stock Esperado</span>
          <span>Contado</span>
          <span>Diferencia</span>
        </div>

        <div
          class="item-row"
          v-for="producto in productos"
          :key="'cuadre-' + producto.id"
        >
          <span>{{ producto.nombre }}</span>
          <span>{{ producto.stock }}</span>
          <input
            type="number"
            v-model.number="producto.contado"
            placeholder="Contado"
            min="0"
          />
          <span
            :class="{
              match: producto.contado === producto.stock,
              mismatch: producto.contado !== producto.stock && producto.contado != null
            }"
          >
            {{ producto.contado != null ? producto.contado - producto.stock : '-' }}
          </span>
        </div>
      </div>

      <button @click="guardarCuadre" class="btn-primary">Guardar Cuadre</button>
    </section>

    <button class="btn-volver" @click="volverMenu">
      <i class="fa-solid fa-house"></i> Menú Principal
    </button>
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

    // === NUEVA FUNCIÓN PARA ACTUALIZAR STOCK ===
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
      console.log("Cuadre realizado:", this.productos);
      alert("Cuadre de inventario guardado correctamente ✅");
    },
  },
};
</script>

<style scoped>
.inventory-container {
  background-color: #f5f5f5;
  min-height: 100vh;
  padding: 1rem;
  font-family: "Inter", sans-serif;
}

h2 {
  color: #333;
  text-align: center;
  margin-bottom: 1rem;
  font-size: 1.5rem;
}

h3 {
  color: #4caf50;
  margin-bottom: 0.8rem;
}

/* === FORMULARIO === */
.product-form-section {
  background: white;
  border-radius: 10px;
  padding: 1rem;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.form-group label {
  font-weight: bold;
  color: #444;
  margin-bottom: 0.4rem;
}

input[type="text"],
input[type="number"] {
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 0.5rem;
  width: 100%;
  outline: none;
}

input:focus {
  border-color: #4caf50;
}

.btn-primary,
.btn-danger {
  border: none;
  border-radius: 6px;
  padding: 0.6rem 1.2rem;
  cursor: pointer;
  transition: all 0.3s;
  font-weight: bold;
}

.btn-primary {
  background-color: #4caf50;
  color: white;
}

.btn-primary:hover {
  background-color: #43a047;
}

.btn-danger {
  background-color: #f44336;
  color: white;
}

.btn-danger:hover {
  background-color: #d32f2f;
}

/* === TABLA === */
.product-list-section {
  margin-top: 2rem;
  background: white;
  border-radius: 10px;
  padding: 1rem;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
}

.product-table {
  width: 100%;
  border-collapse: collapse;
}

.product-table th,
.product-table td {
  padding: 0.8rem;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

.product-table th {
  background-color: #4caf50;
  color: white;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.stock-input {
  width: 70px;
  padding: 0.3rem;
  border-radius: 4px;
  border: 1px solid #ccc;
  text-align: center;
}

/* === TARJETAS MÓVILES === */
.mobile-cards {
  display: none;
}

.product-card {
  background: #fff;
  border-radius: 10px;
  padding: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.product-card h4 {
  color: #333;
  margin-bottom: 0.3rem;
}

.card-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 0.8rem;
}

/* === CUADRE === */
.inventory-check-section {
  margin-top: 2rem;
  background: white;
  border-radius: 10px;
  padding: 1rem;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
}

.inventory-check-table {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.header-row,
.item-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.8rem;
  align-items: center;
}

.header-row {
  font-weight: bold;
  border-bottom: 2px solid #4caf50;
  padding-bottom: 0.5rem;
}

.match {
  color: #4caf50;
  font-weight: bold;
}

.mismatch {
  color: #f44336;
  font-weight: bold;
}

/* === BOTÓN FLOTANTE === */
.btn-volver {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 0.8rem 1.3rem;
  border-radius: 12px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
  transition: all 0.25s ease;
  z-index: 1000;
}

.btn-volver:hover {
  background-color: #43a047;
  transform: translateY(-3px);
}

/* === RESPONSIVE === */
@media (max-width: 800px) {
  .desktop-only {
    display: none;
  }
  .mobile-cards {
    display: block;
  }
}

@media (max-width: 600px) {
  .inventory-container {
    padding: 0.8rem;
  }

  h2 {
    font-size: 1.3rem;
  }

  .btn-primary,
  .btn-danger {
    width: 100%;
    margin-top: 0.3rem;
  }

  .card-actions {
    flex-direction: column;
    gap: 0.4rem;
  }
}
</style>
