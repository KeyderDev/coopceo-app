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

            <div v-if="metodoPago === 'efectivo' && mostrarManual" class="cash-input">
              <label>Monto recibido:</label>
              <input type="number" min="0" step="0.01" v-model.number="cashRecibido" placeholder="Ej. 37.50" />
            </div>

            <div class="cash-buttons2">
              <button class="athmovil-btn" @click="mostrarATH">
                <img :src="athLogo" alt="ATH" class="icon" width="90" height="90" />
              </button>

              <button class="more-options-btn" @click="toggleMasOpciones">Mas Opciones</button>
              <button class="more-options-btn" @click="volverProductos">Atras</button>
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
            <button class="boton-extra" @click="volverMenu">Menu Principal</button>
            <button style="background-color: #3A63E8;" class="boton-extra">Pago Mixto</button>
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

      <div v-if="mostrarErrorModal" class="modal-overlay">
        <div class="modal error-modal">
          <h3>⚠ Orden no totalizada</h3>
          <p>Debes ingresar o seleccionar una cantidad de efectivo antes de finalizar la orden.</p>
          <button @click="cerrarErrorModal">Aceptar</button>
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

      <div v-if="mostrarImagenATH" class="ath-overlay" @click="cerrarATH">
        <img :src="getImageUrl('athceo.png')" alt="ATH Móvil" class="ath-image" />
      </div>
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
      mostrarErrorModal: false,
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
  watch: {
    mostrarErrorModal(nuevoValor) {
      if (nuevoValor) {
        const sonido = new Audio("/sounds/error.mp3");
        sonido.play().catch(() => console.warn("No se pudo reproducir el sonido"));
      }
    },
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

      const search = this.normalizarTexto(this.busquedaCliente);

      return this.clientes.filter((c) => {
        const nombre = `${c.nombre || c.name} ${c.apellido || c.lastname}`;
        const nombreNormalizado = this.normalizarTexto(nombre);
        const socio = c.numero_socio ? c.numero_socio.toString() : "";

        return (
          nombreNormalizado.includes(search) ||
          socio.includes(search)
        );
      });
    },
    productosPorCategoria() {
      const grupos = {};
      const ordenados = [...this.productos].sort((a, b) =>
        (a.categoria || "").localeCompare(b.categoria || "")
      );
      ordenados.forEach((p) => {
        const cat = p.categoria || "Sin categoría";
        if (!grupos[cat]) grupos[cat] = [];
        grupos[cat].push(p);
      });
      return grupos;
    },
  },
  async created() {
    const MIN_LOADING_TIME = 1200;
    const start = Date.now();
    try {
      const token = localStorage.getItem("auth_token");
      await this.loadCurrentUser(token);
      await this.obtenerClientes();
      await this.obtenerProductos();
    } finally {
      const elapsed = Date.now() - start;
      setTimeout(() => (this.isLoading = false), Math.max(0, MIN_LOADING_TIME - elapsed));
    }
  },
  methods: {
    volverMenu() { this.$router.push("/"); },
    async loadCurrentUser(token) {
      const res = await axios.get("/api/user", { headers: { Authorization: `Bearer ${token}` } });
      this.user = res.data;
    },
    normalizarTexto(texto) {
      return texto
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .trim();
    },
    volverProductos() { this.mostrarOpciones = false; },
    toggleMasOpciones() { this.mostrarOpciones = !this.mostrarOpciones; },
    mostrarATH() { this.mostrarImagenATH = true; },
    cerrarATH() { this.mostrarImagenATH = false; },
    getImageUrl(name) { return `${window.location.origin}/images/${name}`; },
    async obtenerClientes() {
      const token = localStorage.getItem("auth_token");
      const res = await axios.get("/api/users", { headers: { Authorization: `Bearer ${token}` } });
      this.clientes = res.data;
    },

    async obtenerProductos() {
      const token = localStorage.getItem("auth_token");

      const res = await axios.get("/api/products", {
        headers: { Authorization: `Bearer ${token}` }
      });

      this.productos = res.data;
    },

    seleccionarCliente(cliente) {
      this.clienteId = cliente.id;
      this.clienteSeleccionado = cliente;
      this.busquedaCliente = `${cliente.numero_socio ? "#" + cliente.numero_socio + " - " : ""}${cliente.nombre || cliente.name} ${cliente.apellido || cliente.lastname}`;
      this.mostrarClientes = false;
    },
    ocultarListaClientes() { setTimeout(() => (this.mostrarClientes = false), 200); },
    agregarProducto(producto) {
      const index = this.orden.findIndex((p) => p.id === producto.id);
      if (index !== -1) this.orden[index].cantidad += 1;
      else this.orden.push({ ...producto, cantidad: 1 });
    },
    obtenerClaseCategoria(producto) {
      const cat = producto.categoria?.toLowerCase() || "";
      if (cat.includes("bebidas")) return producto.nombre.toLowerCase().includes("agua") ? "boton-agua" : "boton-bebida";
      if (cat.includes("refrigerio")) return "boton-refrigerio";
      if (cat.includes("dulce")) return "boton-dulce";
      if (cat.includes("chocolate")) return "boton-chocolates";
      if (cat.includes("comida")) return "boton-comida";
      return "boton-otros";
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
    cancelarVoid() { this.mostrarModal = false; this.codigoSuperAdmin = ""; },
    seleccionarCash(monto) { this.cashRecibido += monto; },
    seleccionarExacto() { this.cashRecibido = this.total; },
    toggleManual() { this.mostrarManual = !this.mostrarManual; if (!this.mostrarManual) this.cashRecibido = 0; },
    cerrarErrorModal() { this.mostrarErrorModal = false; },

    async terminarOrden() {
      if (this.orden.length === 0) return;

      if (this.metodoPago === "efectivo" && this.cashRecibido <= 0) {
        this.mostrarErrorModal = true;
        return;
      }

      this.loading = true;
      const payload = {
        cliente_id: this.clienteId,
        cajero_id: this.user.id,
        total: this.total,
        metodo_pago: this.metodoPago,
        productos: this.orden.map((i) => ({
          product_id: i.id,
          quantity: i.cantidad,
        })),
      };

      try {
        const token = localStorage.getItem("auth_token");
        const res = await axios.post("/api/sales", payload, {
          headers: { Authorization: `Bearer ${token}` },
        });

        alert(`Orden registrada con éxito! Dividendos actuales: ${res.data.dividendos_actuales}`);
        this.orden = [];
        this.cashRecibido = 0;
        this.clienteId = null;
        this.busquedaCliente = "";
        this.metodoPago = "efectivo";
      } catch {
        alert("Error al procesar la orden");
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.pos-container {
  padding: 1rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #0e1117;
  width: 100%;
  box-sizing: border-box;
  color: #f5f7fa;
}

.loader-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(14, 17, 23, 0.98);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  color: #fff;
}

.loader {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.loader div {
  width: 15px;
  height: 15px;
  background: #10b981;
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

.main-section {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  flex-wrap: wrap;
}

.order-section,
.products-section {
  background-color: #1a1f2b;
  border-radius: 10px;
  padding: 1rem;
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.6);
  color: #f5f7fa;
}

.order-section {
  flex: 1;
  min-width: 280px;
  max-height: 80vh;
  overflow-y: auto;
  overscroll-behavior: contain;
}

.products-section {
  flex: 2;
  min-width: 280px;
  max-height: 800px;
  overflow-y: auto;
}

.order-section h3,
.products-section h3 {
  font-size: 1.3rem;
  margin-bottom: 0.8rem;
  color: #cbd5e1;
  border-bottom: 1px solid #2c3340;
  padding-bottom: 0.3rem;
}

.client-section {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.8rem;
  margin-bottom: 1.5rem;
  position: relative;
}

.client-section label {
  color: #aeb9d1;
  font-size: 1rem;
  font-weight: 500;
}

.clientes-list {
  max-height: 220px;
  overflow-y: auto;
  border: 1px solid #2c3340;
  border-radius: 8px;
  background-color: #1e2430;
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  z-index: 10;
  font-size: 0.95rem;
}

.cliente-item {
  padding: 0.7rem;
  cursor: pointer;
  color: #f5f7fa;
}

.cliente-item:hover {
  background-color: #293142;
}

.search-bar {
  background: #1a1f2b;
  color: #f5f7fa;
  border: 1px solid #2c3340;
  border-radius: 8px;
  padding: 0.9rem 1rem;
  font-size: 1rem;
  width: 100%;
  transition: all 0.25s ease;
}

.search-bar:focus {
  border-color: #3a63e8;
  outline: none;
  box-shadow: 0 0 8px rgba(58, 99, 232, 0.4);
}

.products-section .search-bar {
  margin-bottom: 1.2rem;
}

.categoria-bloque {
  background: #1e2430;
  border-radius: 10px;
  padding: 0.8rem;
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.3);
}

.categoria-titulo {
  color: #8fa3c8;
  border-bottom: 2px solid #2c3340;
  padding-bottom: 0.3rem;
  margin-bottom: 0.6rem;
  text-transform: capitalize;
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

.producto-boton {
  background-color: #3a63e8;
  color: #fff;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  padding: 0.6rem;
  cursor: pointer;
  transition: 0.2s;
}

.producto-boton:hover {
  filter: brightness(1.2);
}

.boton-bebida {
  background-color: #3a63e8;
}

.boton-agua {
  background-color: #0ea5e9;
}

.boton-refrigerio {
  background-color: #1e90ff;
}

.boton-dulce {
  background-color: #f59e0b;
}

.boton-comida {
  background-color: #ef4444;
}

.boton-otros {
  background-color: #4b5563;
}

.boton-chocolates {
  background-color: #7b4c2b;
}

.boton-extra {
  background-color: #10b981;
  color: #fff;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 0.8rem;
  transition: 0.25s;
}

.boton-extra:hover {
  filter: brightness(1.2);
}

.totales {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
  flex-wrap: wrap;
}

.totales span {
  color: #10b981;
}

.order-items-container {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 0.5rem;
  min-height: 100px;
  max-height: 30vh;
  padding-right: 6px;
  scroll-behavior: smooth;
}

.order-items-container::-webkit-scrollbar {
  width: 6px;
}

.order-items-container::-webkit-scrollbar-thumb {
  background-color: #2c3340;
  border-radius: 3px;
}

.order-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
  padding: 0.5rem;
  border-radius: 8px;
  border: 1px solid #2c3340;
  background-color: #1e2430;
  flex-wrap: wrap;
}

.order-item button {
  background-color: #ef4444;
  color: #fff;
  border: none;
  padding: 0.3rem 0.6rem;
  border-radius: 6px;
  cursor: pointer;
}

.cantidad-subtotal {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.cantidad-subtotal input {
  width: 70px;
  padding: 0.2rem;
  background: #1a1f2b;
  color: #f5f7fa;
  border: 1px solid #2c3340;
  border-radius: 6px;
}

.payment-section {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.payment-section label {
  color: #aeb9d1;
}

.payment-section select {
  background: #1a1f2b;
  color: #f5f7fa;
  border: 1px solid #2c3340;
  border-radius: 6px;
  padding: 0.4rem 0.6rem;
}

.cash-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.cash-buttons button,
.manual-btn,
.exacto-btn {
  background-color: #374151;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.25s;
  padding: 0.8rem;
}

.cash-info {
  margin-top: 0.5rem;
  font-weight: bold;
  color: #10b981;
}

.cash-input {
  margin-top: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.cash-input input {
  background: #1a1f2b;
  color: #f5f7fa;
  border: 1px solid #2c3340;
  border-radius: 6px;
  padding: 0.5rem;
  width: 100%;
}

.order-footer {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  margin-top: 0.8rem;
}

.order-footer>button {
  width: 100%;
  padding: 1rem;
  font-size: 1rem;
  font-weight: 700;
  border-radius: 8px;
  border: none;
  background-color: #3a63e8;
  color: #fff;
  cursor: pointer;
  transition: 0.25s;
  letter-spacing: 0.5px;
}

.cash-buttons2 {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.athmovil-btn {
  background-color: #000;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.2rem;
  cursor: pointer;
}

.more-options-btn {
  background-color: #033963;
  color: #fff;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 0.6rem 1rem;
  cursor: pointer;
}

.more-options-btn:hover {
  background-color: #022f4a;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background-color: #1a1f2b;
  color: #f5f7fa;
  border-radius: 10px;
  padding: 1.5rem;
  border: 1px solid #2c3340;
  width: 300px;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.error-modal {
  background-color: #2b1a1a;
  border: 2px solid #ef4444;
  color: #fca5a5;
  text-align: center;
}

.error-modal button {
  background-color: #ef4444;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  cursor: pointer;
  margin-top: 0.5rem;
}

.modal input {
  padding: 0.5rem;
  border-radius: 6px;
  border: 1px solid #2c3340;
  background: #1e2430;
  color: #f5f7fa;
}

.modal-buttons {
  display: flex;
  justify-content: space-between;
  gap: 0.5rem;
}

.modal-buttons button:first-child {
  background-color: #10b981;
  color: #fff;
}

.modal-buttons button:last-child {
  background-color: #ef4444;
  color: #fff;
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

@media (max-width: 768px) {

  .pos-container {
    padding: 0.5rem;
    width: 100vw;
    max-width: 100%;
    margin: 0;
  }

  .order-section,
  .products-section,
  .order-items-container {
    max-height: unset !important;
    overflow: visible !important;
    width: 100% !important;
    flex-basis: 100% !important;
  }

  .products-section,
  .order-section {
    width: 100% !important;
    min-width: 100% !important;
    max-width: 100% !important;
    border-radius: 12px;
    margin: 0 auto;
    box-sizing: border-box;
  }

  .main-section {
    flex-direction: column;
    width: 100%;
    align-items: stretch;
    gap: 0.8rem;
  }

  .productos-grid {
    grid-template-columns: repeat(auto-fill, minmax(95%, 1fr));
    gap: 0.7rem;
    width: 100%;
  }

  .search-bar {
    font-size: 1rem;
    padding: 0.9rem 1rem;
    width: 100%;
  }

  .cash-buttons button {
    flex: 1 1 48%;
    font-size: 1rem;
    padding: 0.8rem;
  }

  .totales {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.4rem;
  }

  .order-footer>button {
    width: 100%;
    padding: 1.2rem;
    font-size: 1.1rem;
    font-weight: 700;
  }

  .clientes-list {
    width: 100%;
    left: 0;
  }

  .categoria-bloque {
    padding: 1rem;
  }
}
</style>
