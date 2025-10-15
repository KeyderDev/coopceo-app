<template>
  <div class="pos-container">
    <div class="client-section">
      <label>Seleccionar cliente:</label>
      <select v-model="clienteId">
        <option value="" disabled>Seleccione un cliente</option>
        <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
          {{ cliente.numero_socio ? `#${cliente.numero_socio} - ` : '' }}
          {{ (cliente.nombre || cliente.name) }} {{ (cliente.apellido || cliente.lastname) }}
        </option>
      </select>

    </div>

    <div class="main-section">
      <div class="order-section">
        <h3>Orden</h3>

        <div class="order-items-container">
          <div v-if="orden.length === 0">No hay productos agregados</div>
          <div v-else>
            <div class="order-item" v-for="(item, index) in orden" :key="item.id">
              <span>{{ item.nombre }}</span>
              <div class="cantidad-subtotal">
                <input type="number" min="1" v-model.number="item.cantidad" />
                <span>Subtotal: ${{ (item.cantidad * item.precio).toFixed(2) }}</span>
              </div>
              <button @click="intentarEliminarProducto(index)">Eliminar</button>
            </div>
          </div>
        </div>

        <div class="order-footer">
          <div class="totales">
            <span>Subtotal: ${{ subtotal.toFixed(2) }}</span>
            <span>Total: ${{ total.toFixed(2) }}</span>
          </div>

          <div v-if="cashRecibido > 0" class="cash-info">
            <p>Cash: ${{ cashRecibido.toFixed(2) }}</p>
            <p>Cambio: ${{ cambio.toFixed(2) }}</p>
          </div>

          <div class="payment-section">
            <label>Método de pago:</label>
            <select v-model="metodoPago">
              <option value="efectivo">Efectivo</option>
              <option value="athmovil">ATH Movil</option>
            </select>
          </div>
          <div v-if="metodoPago === 'efectivo'" class="cash-buttons">
            <button class="exacto-btn" @click="seleccionarExacto">Exacto</button>
            <button v-for="monto in [1, 5, 10, 20]" :key="monto" class="efectivo-btn" @click="seleccionarCash(monto)">
              ${{ monto }}
            </button>
            <button class="manual-btn" @click="toggleManual">Manual</button>
          </div>

          <div v-if="metodoPago === 'efectivo'" class="cash-buttons2">
            <button class="athmovil-btn">
              <img :src="athLogo" alt="ATH" class="icon" width="90" height="90" />
            </button>
          </div>

          <div v-if="metodoPago === 'efectivo' && mostrarManual" class="cash-input">
            <label>Monto recibido:</label>
            <input type="number" min="0" step="0.01" v-model.number="cashRecibido" placeholder="Ej. 37.50" />
          </div>

          <button @click="terminarOrden" :disabled="orden.length === 0 || loading">
            {{ loading ? "Procesando..." : "Terminar Orden" }}
          </button>
        </div>
      </div>

      <div class="products-section">
        <h3>Productos</h3>

        <input type="text" v-model="busqueda" placeholder="Buscar producto..." class="search-bar" />

        <div v-if="productos.length === 0">Cargando productos...</div>
        <div class="product-card" v-for="producto in productosFiltrados" :key="producto.id">
          <span>{{ producto.nombre }}</span>
          <span>${{ Number(producto.precio).toFixed(2) }}</span>
          <button @click="agregarProducto(producto)">Agregar</button>
        </div>
      </div>
    </div>

    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal">
        <h3>Ingrese código superadmin para void</h3>
        <input type="password" v-model="codigoSuperAdmin" placeholder="Código..." />
        <div class="modal-buttons">
          <button @click="confirmarVoid">Confirmar</button>
          <button @click="cancelarVoid">Cancelar</button>
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
      clientes: [],
      productos: [],
      clienteId: null,
      orden: [],
      metodoPago: "efectivo",
      loading: false,
      cashRecibido: 0,
      busqueda: "",
      mostrarManual: false,
      mostrarModal: false,
      productoAEliminar: null,
      codigoSuperAdmin: "",
      athLogo: '/images/athpng.png'

    };
  },
  computed: {
    subtotal() {
      return this.orden.reduce((acc, item) => acc + Number(item.precio) * item.cantidad, 0);
    },
    total() {
      return this.subtotal;
    },
    cambio() {
      return this.cashRecibido - this.total > 0 ? this.cashRecibido - this.total : 0;
    },
    productosFiltrados() {
      if (!this.busqueda) return this.productos;
      return this.productos.filter(p =>
        p.nombre.toLowerCase().includes(this.busqueda.toLowerCase())
      );
    }
  },
  created() {
    this.obtenerClientes();
    this.obtenerProductos();
  },
  methods: {
    async obtenerClientes() {
      try {
        const token = localStorage.getItem("auth_token");
        const res = await axios.get("/api/users", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.clientes = res.data;
      } catch (err) {
        console.error("Error al cargar clientes:", err);
      }
    },
    toggleManual() {
      this.mostrarManual = !this.mostrarManual;
      if (!this.mostrarManual) this.cashRecibido = 0;
    },
    async obtenerProductos() {
      try {
        const res = await axios.get("/api/products");
        this.productos = res.data;
      } catch (err) {
        console.error("Error al cargar productos:", err);
      }
    },
    agregarProducto(producto) {
      const index = this.orden.findIndex(p => p.id === producto.id);
      if (index !== -1) {
        this.orden[index].cantidad += 1;
      } else {
        this.orden.push({ ...producto, cantidad: 1 });
      }
    },
    intentarEliminarProducto(index) {
      if (this.metodoPago === 'efectivo' && this.cashRecibido > 0) {
        this.productoAEliminar = index;
        this.mostrarModal = true;
      } else {
        this.eliminarProducto(index);
      }
    },
    eliminarProducto(index) {
      this.orden.splice(index, 1);
    },
    confirmarVoid() {
      if (this.codigoSuperAdmin === "0000superadmin") {
        this.eliminarProducto(this.productoAEliminar);
        this.mostrarModal = false;
        this.codigoSuperAdmin = "";
        this.productoAEliminar = null;
      } else {
        alert("Código incorrecto. No se puede eliminar el producto.");
      }
    },
    cancelarVoid() {
      this.mostrarModal = false;
      this.codigoSuperAdmin = "";
      this.productoAEliminar = null;
    },
    seleccionarCash(monto) {
      this.metodoPago = "efectivo";
      this.cashRecibido += monto;
    },
    seleccionarExacto() {
      this.metodoPago = "efectivo";
      this.cashRecibido = this.total;
    },
    async terminarOrden() {
      if (this.orden.length === 0) return;
      this.loading = true;

      const payload = {
        cliente_id: this.clienteId,
        total: this.total,
        metodo_pago: this.metodoPago,
        productos: this.orden.map(item => ({ product_id: item.id }))
      };

      try {
        const token = localStorage.getItem("auth_token");
        const res = await axios.post("/api/sales", payload, {
          headers: { Authorization: `Bearer ${token}` },
        });

        alert(`Orden registrada con éxito! Dividendos actuales: ${res.data.dividendos_actuales}`);
        this.orden = [];
        this.clienteId = null;
        this.metodoPago = "efectivo";
        this.cashRecibido = 0;
      } catch (error) {
        if (error.response) {
          if (error.response.status === 422 && error.response.data.errors) {
            alert("Error de validación: " + JSON.stringify(error.response.data.errors));
          } else {
            alert("Error al procesar la orden: " + (error.response.data.message || "desconocido"));
          }
        } else {
          alert("Error al procesar la orden");
        }
      } finally {
        this.loading = false;
      }
    }
  },
};
</script>

<style scoped>
.pos-container {
  padding: 1rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f5f5f5;
  width: 100%;
  box-sizing: border-box;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background-color: #fff;
  padding: 1.5rem;
  border-radius: 10px;
  width: 300px;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.modal input {
  padding: 0.5rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 1rem;
}

.modal-buttons {
  display: flex;
  justify-content: space-between;
  gap: 0.5rem;
}

.modal-buttons button {
  flex: 1;
  padding: 0.5rem;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}

.modal-buttons button:first-child {
  background-color: #4caf50;
  color: #fff;
}

.modal-buttons button:last-child {
  background-color: #e53935;
  color: #fff;
}

/* --- Botones de efectivo --- */
.cash-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.cash-buttons button {
  flex: 1;
  /* background-color: #4caf50; */
  color: #fff;
  border: none;
  padding: 0.8rem;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
}


.cash-buttons2 {
  display: flex;
  gap: 0.5rem;
  /* border: 1px solid black; */
  margin-top: 0.5rem;
}

.athmovil-btn {
  background-color: #000;
  color: #fff;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 0.2rem;
  /* mínimo padding */
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  width: auto;
}

.efectivo-btn {
  background-color: #4caf50;
}

.exacto-btn {
  background-color: #4caf50;
}

.manual-btn {
  background-color: #4caf50;
}

.cash-buttons button:hover {
  background-color: #43a047;
}

.cash-info {
  margin-top: 0.5rem;
  font-weight: bold;
  color: #2e7d32;
}

/* --- Sección de cliente --- */
.client-section {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.client-section select {
  flex: 1;
  min-width: 180px;
  padding: 0.4rem 0.6rem;
  border-radius: 6px;
  border: 1px solid #ccc;
}

/* --- Layout principal --- */
.main-section {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  flex-wrap: wrap;
}

/* --- Orden --- */
.order-section {
  flex: 1;
  min-width: 280px;
  max-height: 500px;
  background-color: #fff;
  border-radius: 10px;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
  overflow: visible;
}

.order-items-container {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 0.5rem;
  min-height: 50px;
}

.order-footer {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.totales {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
  flex-wrap: wrap;
}

/* --- Productos --- */
.products-section {
  flex: 2;
  min-width: 280px;
  max-height: 800px;
  background-color: #fff;
  border-radius: 10px;
  padding: 1rem;
  overflow-y: auto;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}

.product-card,
.order-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
  padding: 0.5rem;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  background-color: #fafafa;
  flex-wrap: wrap;
}

.product-card button {
  background-color: #4caf50;
  color: #fff;
  border: none;
  padding: 0.3rem 0.6rem;
  border-radius: 6px;
  cursor: pointer;
}

.cash-input {
  margin-top: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.cash-input input {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
  width: 100%;
  box-sizing: border-box;
}

.order-item button {
  background-color: #e53935;
  color: #fff;
  border: none;
  padding: 0.3rem 0.6rem;
  border-radius: 6px;
  cursor: pointer;
}

/* --- Cantidad/Subtotal --- */
.cantidad-subtotal {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.cantidad-subtotal input {
  width: 70px;
  padding: 0.2rem;
  box-sizing: border-box;
}

/* --- Métodos de pago --- */
.payment-section {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.order-footer>button {
  width: 100%;
  padding: 0.6rem;
  font-weight: bold;
  border-radius: 8px;
  border: none;
  background-color: #4caf50;
  color: #fff;
  cursor: pointer;
}

.order-footer>button:disabled {
  background-color: #a5d6a7;
  cursor: not-allowed;
}

.search-bar {
  width: 100%;
  padding: 0.6rem;
  margin-bottom: 1rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}

/* --- Responsive --- */
/* --- Responsive --- */
/* --- Responsive --- */
@media (max-width: 768px) {
  .main-section {
    flex-direction: column;
  }

  .order-section,
  .products-section {
    min-width: 100%;
    max-height: none;
  }

  /* ✅ Scroll SOLO en productos */
  .products-section {
    max-height: 50vh;
    /* ocupa 60% de la pantalla */
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }

  /* Scrollbar elegante */
  .products-section::-webkit-scrollbar {
    width: 6px;
  }

  .products-section::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
  }

  .products-section::-webkit-scrollbar-track {
    background-color: transparent;
  }

  .order-item,
  .product-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .cantidad-subtotal {
    width: 100%;
    justify-content: space-between;
  }

  .cantidad-subtotal input {
    width: 100%;
    max-width: 100px;
  }

  .cash-buttons button {
    flex: 1 1 45%;
    font-size: 0.9rem;
    padding: 0.6rem;
  }

  .totales {
    flex-direction: column;
    gap: 0.3rem;
  }

  .payment-section {
    flex-direction: column;
    align-items: flex-start;
  }

  .client-section {
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
  }

  .client-section select {
    width: 100%;
  }

  .order-footer>button {
    font-size: 1rem;
    padding: 0.8rem;
  }
}
</style>
