<template>
  <div class="pos-container">
    <div v-if="isLoading" class="loader-overlay">
      <div class="loader">
        <div></div>
        <div></div>
        <div></div>
      </div>
      <p>Cargando POS...</p>
    </div>

    <div v-else>
      <div class="client-section">
        <label>Seleccionar cliente:</label>
        <input type="text" v-model="busquedaCliente" placeholder="Buscar por nombre o #socio..." class="search-bar"
          @focus="mostrarClientes = true" @blur="ocultarListaClientes" />
        <div v-if="mostrarClientes && clientesFiltrados.length" class="clientes-list">
          <div class="cliente-item" v-for="cliente in clientesFiltrados" :key="cliente.id"
            @mousedown.prevent="seleccionarCliente(cliente)">
            {{ cliente.numero_socio ? '#' + cliente.numero_socio + ' - ' : '' }}
            {{ (cliente.nombre || cliente.name) }} {{ (cliente.apellido || cliente.lastname) }}
          </div>
        </div>
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

            <div class="cash-buttons2">
              <button class="athmovil-btn" @click="mostrarATH">
                <img :src="athLogo" alt="ATH" class="icon" width="90" height="90" />
              </button>

              <button style="background-color: #3A63E8;" class="more-options-btn">Pago Mixto</button>

              <button class="more-options-btn" @click="toggleMasOpciones">Mas Opciones</button>
              <button class="more-options-btn" @click="volverProductos">Atras</button>
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

          <input type="text" v-model="busqueda" placeholder="Buscar producto..." class="search-bar"
            v-if="!mostrarOpciones" />

          <div v-if="mostrarOpciones" class="productos-botones">
            <button class="boton-extra">Codigo Promocional</button>
            <button class="boton-extra" style="background-color: goldenrod;">Refund</button>
            <router-link to="/cuadre" custom v-slot="{ navigate }">
              <button class="boton-extra" @click="navigate">Cuadre</button>
            </router-link>
            <button class="boton-extra">ATH Movil Summary</button>
          </div>

          <div v-else>
            <div v-if="productos.length === 0">Cargando productos...</div>
            <div v-else>
              <div v-if="Object.keys(productosPorCategoria).length === 0">No se encontraron productos</div>
              <div v-for="(listaProductos, categoria) in productosPorCategoria" :key="categoria"
                class="categoria-bloque">
                <h4 class="categoria-titulo">{{ categoria }}</h4>
                <div class="productos-grid">
                  <button v-for="producto in listaProductos" :key="producto.id" class="producto-boton"
                    :class="obtenerClaseCategoria(producto)" @click="agregarProducto(producto)">
                    {{ producto.nombre }}
                  </button>
                </div>
              </div>
            </div>
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
    <!-- Modal imagen ATH -->
    <div v-if="mostrarImagenATH" class="ath-overlay" @click="cerrarATH">
      <img :src="getImageUrl('athceo.png')" alt="Logo" />
    </div>

  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      mostrarImagenATH: false,
      isLoading: true,
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
      busquedaCliente: "",
      mostrarClientes: false,
      clienteSeleccionado: null,
      user: {},
      athLogo: "/images/athpng.png",
      mostrarOpciones: false,
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
    clientesFiltrados() {
      if (!this.busquedaCliente) return this.clientes;
      return this.clientes.filter(cliente => {
        const nombreCompleto = `${cliente.nombre || cliente.name} ${cliente.apellido || cliente.lastname}`.toLowerCase();
        const numeroSocio = cliente.numero_socio ? cliente.numero_socio.toString() : "";
        const busqueda = this.busquedaCliente.toLowerCase();
        return nombreCompleto.includes(busqueda) || numeroSocio.includes(busqueda);
      });
    },
    productosFiltrados() {
      if (!this.busqueda) return this.productos;
      return this.productos.filter(p => (p.nombre || "").toLowerCase().includes(this.busqueda.toLowerCase()));
    },
    productosPorCategoria() {
      const grupos = {};
      const ordenados = [...this.productosFiltrados].sort((a, b) => {
        const ca = (a.categoria || "Sin categoría").toLowerCase();
        const cb = (b.categoria || "Sin categoría").toLowerCase();
        if (ca < cb) return -1;
        if (ca > cb) return 1;
        return (a.nombre || "").localeCompare(b.nombre || "");
      });
      ordenados.forEach(producto => {
        const categoria = producto.categoria && producto.categoria !== "" ? producto.categoria : "Sin categoría";
        if (!grupos[categoria]) grupos[categoria] = [];
        grupos[categoria].push(producto);
      });
      return grupos;
    },
  },
  async created() {
    const MIN_LOADING_TIME = 1500;
    const startTime = Date.now();
    try {
      const token = localStorage.getItem("auth_token");
      await this.loadCurrentUser(token);
      await this.obtenerClientes();
      await this.obtenerProductos();
    } catch (err) {
      console.error("Error al cargar:", err);
    } finally {
      const elapsed = Date.now() - startTime;
      const remaining = MIN_LOADING_TIME - elapsed;
      if (remaining > 0) {
        setTimeout(() => { this.isLoading = false; }, remaining);
      } else {
        this.isLoading = false;
      }
    }
  },
  methods: {
    async loadCurrentUser(token) {
      const res = await axios.get("/api/user", { headers: { Authorization: `Bearer ${token}` } });
      this.user = res.data;
    },
    volverProductos() {
      this.mostrarOpciones = false;
    },
    mostrarATH() {
      this.mostrarImagenATH = true;
    },
    cerrarATH() {
      this.mostrarImagenATH = false;
    },
    async obtenerClientes() {
      const token = localStorage.getItem("auth_token");
      const res = await axios.get("/api/users", { headers: { Authorization: `Bearer ${token}` } });
      this.clientes = res.data;
    },
    getImageUrl(name) {
      return `${window.location.origin}/images/${name}`;
    },
    async obtenerProductos() {
      const res = await axios.get("/api/products");
      this.productos = res.data;
    },
    seleccionarCliente(cliente) {
      this.clienteId = cliente.id;
      this.clienteSeleccionado = cliente;
      this.busquedaCliente = `${cliente.numero_socio ? '#' + cliente.numero_socio + ' - ' : ''}${cliente.nombre || cliente.name} ${cliente.apellido || cliente.lastname}`;
      this.mostrarClientes = false;
    },
    ocultarListaClientes() { setTimeout(() => (this.mostrarClientes = false), 200); },
    agregarProducto(producto) {
      const index = this.orden.findIndex(p => p.id === producto.id);
      if (index !== -1) this.orden[index].cantidad += 1;
      else this.orden.push({ ...producto, cantidad: 1 });
    },
    obtenerClaseCategoria(producto) {
      const cat = producto.categoria?.toLowerCase() || "";
      if (cat.includes("bebidas")) return producto.nombre.toLowerCase().includes("agua") ? "boton-agua" : "boton-bebida";
      else if (cat.includes("refrigerio")) return "boton-refrigerio";
      else if (cat.includes("dulce")) return "boton-dulce";
      else if (cat.includes("chocolate")) return "boton-chocolates";
      else if (cat.includes("comida")) return "boton-comida";
      else return "boton-otros";
    },
    intentarEliminarProducto(index) {
      if (this.metodoPago === "efectivo" && this.cashRecibido > 0) {
        this.productoAEliminar = index;
        this.mostrarModal = true;
      } else this.eliminarProducto(index);
    },
    eliminarProducto(index) { this.orden.splice(index, 1); },
    confirmarVoid() {
      if (this.codigoSuperAdmin === "0000superadmin") {
        this.eliminarProducto(this.productoAEliminar);
        this.mostrarModal = false;
        this.codigoSuperAdmin = "";
      } else alert("Código incorrecto");
    },
    cancelarVoid() {
      this.mostrarModal = false;
      this.codigoSuperAdmin = "";
    },
    seleccionarCash(monto) { this.cashRecibido += monto; },
    seleccionarExacto() { this.cashRecibido = this.total; },
    toggleManual() { this.mostrarManual = !this.mostrarManual; if (!this.mostrarManual) this.cashRecibido = 0; },
    async terminarOrden() {
      if (this.orden.length === 0) return;
      this.loading = true;

      const payload = {
        cliente_id: this.clienteId,
        cajero_id: this.user.id,
        total: this.total,
        metodo_pago: this.metodoPago,
        productos: this.orden.map(i => ({
          product_id: i.id,
          quantity: i.cantidad  // <-- importante, antes no estaba
        })),
      };

      try {
        const token = localStorage.getItem("auth_token");
        const res = await axios.post("/api/sales", payload, {
          headers: { Authorization: `Bearer ${token}` }
        });

        alert(`Orden registrada con éxito! Dividendos actuales: ${res.data.dividendos_actuales}`);

        this.orden = [];
        this.clienteId = null;
        this.cashRecibido = 0;
        this.busquedaCliente = "";
        this.clienteSeleccionado = null;
        this.mostrarClientes = false;

      } catch (e) {
        alert("Error al procesar la orden");
      } finally {
        this.loading = false;
      }
    },

    toggleMasOpciones() { this.mostrarOpciones = !this.mostrarOpciones; },
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

.ath-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 3000;
  cursor: pointer;
}

.ath-image {
  max-width: 90%;
  max-height: 90%;
  object-fit: contain;
  border-radius: 20px;
  box-shadow: 0 0 25px rgba(255, 255, 255, 0.3);
  animation: zoomIn 0.4s ease-out;
}

@keyframes zoomIn {
  from {
    transform: scale(0.7);
    opacity: 0;
  }

  to {
    transform: scale(1);
    opacity: 1;
  }
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

.cash-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.cash-buttons button {
  flex: 1;
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
  margin-top: 0.5rem;
}

.athmovil-btn {
  background-color: #000;
  color: #fff;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 0.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  width: auto;
}

.producto-boton {
  background-color: #4caf50;
  color: #fff;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 0.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  width: auto;
}

.boton-extra {
  background-color: #4caf50;
  color: #fff;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 0.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;

  width: 130px;
  /* 👈 ancho fijo */
  height: 50px;
  /* 👈 alto fijo */
}

.reconciliation-btn {
  background-color: #2399EB;
  color: #fff;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 0.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  width: auto;
}

.reconciliation-btn:hover {
  background-color: #1a7acb;
}

.more-options-btn {
  background-color: #033963;
  color: #fff;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 0.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  width: auto;
}

.more-options-btn:hover {
  background-color: #022f4a;
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
  flex-direction: column;
  align-items: flex-start;
  gap: 0.5rem;
  margin-bottom: 1rem;
  position: relative;
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

/* --- AGRUPACIÓN POR CATEGORÍA --- */
.categoria-bloque {
  margin-bottom: 1rem;
  background: #f9f9f9;
  border-radius: 10px;
  padding: 0.8rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

.categoria-titulo {
  margin-bottom: 0.6rem;
  font-weight: bold;
  text-transform: capitalize;
  color: #333;
  border-bottom: 2px solid #ddd;
  padding-bottom: 0.3rem;
}

.productos-botones {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
}

.productos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
  gap: 0.5rem;
}

/* --- Colores por categoría --- */
.boton-bebida {
  background-color: #e53935;
  color: #fff;
}

.boton-agua {
  background-color: #2196f3;
  color: #fff;
}

.boton-refrigerio {
  background-color: #2196f3;
  color: #fff;
}

.boton-dulce {
  background-color: #e53935;
  color: #fff;
}

.boton-comida {
  background-color: #e53935;
  color: #fff;
}

.boton-otros {
  background-color: #757575;
  color: #fff;
}

.boton-chocolates {
  background-color: #8d6e63;
  color: #fff;
}


.producto-boton {
  border: none;
  padding: 0.6rem;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.2s;
}

.producto-boton:hover {
  filter: brightness(0.9);
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

.loader-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(30, 30, 30, 0.95);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  color: #fff;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.loader {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.loader div {
  width: 15px;
  height: 15px;
  background: #4caf50;
  border-radius: 50%;
  animation: bounce 0.6s infinite alternate;
}

.loader div:nth-child(2) {
  animation-delay: 0.2s;
}

.loader div:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes bounce {
  to {
    transform: translateY(-15px);
    opacity: 0.7;
  }
}

.loader-overlay p {
  font-size: 1.2rem;
  font-weight: bold;
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

.clientes-list {
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid #ccc;
  border-radius: 6px;
  background-color: #fff;
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  z-index: 10;
}

.cliente-item {
  padding: 0.5rem;
  cursor: pointer;
}

.cliente-item:hover {
  background-color: #f0f0f0;
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

@media (max-width: 768px) {
  .main-section {
    flex-direction: column;
  }

  .order-section,
  .products-section {
    min-width: 100%;
    max-height: none;
  }

  .products-section {
    max-height: 50vh;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }

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

  .producto-boton {
    flex: 1 1 calc(50% - 0.6rem);
    padding: 0.6rem 1rem;
  }
}
</style>
