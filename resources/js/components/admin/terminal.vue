<template>
    <div class="pos-container">
        <div class="client-section">
            <label>Seleccionar cliente:</label>
            <select v-model="clienteId">
                <option value="" disabled>Seleccione un cliente</option>
                <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
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
                            <button @click="eliminarProducto(index)">Eliminar</button>
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

                    <div class="cash-buttons">
                        <button v-for="monto in [1, 5, 10, 20]" :key="monto" @click="seleccionarCash(monto)">
                            ${{ monto }}
                        </button>
                    </div>

                    <button @click="terminarOrden" :disabled="orden.length === 0 || !clienteId || loading">
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
            busqueda: "" 
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
        async obtenerProductos() {
            try {
                const res = await axios.get("/api/products");
                this.productos = res.data;
            } catch (err) {
                console.error("Error al cargar productos:", err);
            }
        },
        agregarProducto(producto) {
            const index = this.orden.findIndex((p) => p.id === producto.id);
            if (index !== -1) {
                this.orden[index].cantidad += 1;
            } else {
                this.orden.push({ ...producto, cantidad: 1 });
            }
        },
        eliminarProducto(index) {
            this.orden.splice(index, 1);
        },
        seleccionarCash(monto) {
            this.metodoPago = "efectivo";
            this.cashRecibido += monto;
        },
        async terminarOrden() {
            if (!this.clienteId || this.orden.length === 0) return;
            this.loading = true;

            const payload = {
                cliente_id: this.clienteId,
                cajero_id: 1,
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
    /* quitamos min-height: 100vh */
}

.cash-buttons {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.cash-buttons button {
    flex: 1;
    background-color: #4caf50;
    color: #fff;
    border: none;
    padding: 0.8rem;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
}

.cash-buttons button:hover {
    background-color: #43a047;
}

.cash-info {
    margin-top: 0.5rem;
    font-weight: bold;
    color: #2e7d32;
}

.client-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.client-section select {
    padding: 0.4rem 0.6rem;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.main-section {
    display: flex;
    gap: 1rem;
    align-items: flex-start; /* para que no estire */
}

.order-section {
    flex: 1;
    min-width: 300px;
    max-height: 400px; /* altura limitada */
    background-color: #fff; /* mantenemos el fondo */
    border-radius: 10px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    overflow: hidden; /* evita que el contenido sobresalga */
}

.order-items-container {
    flex: 1;
    overflow-y: auto; /* scroll solo aquí */
    margin-bottom: 0.5rem;
}


.order-footer {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.products-section {
    flex: 2;
    min-width: 300px;
    max-height: 600px;
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
}

.product-card button {
    background-color: #4caf50;
    color: #fff;
    border: none;
    padding: 0.3rem 0.6rem;
    border-radius: 6px;
}

.order-item button {
    background-color: #e53935;
    color: #fff;
    border: none;
    padding: 0.3rem 0.6rem;
    border-radius: 6px;
}

.cantidad-subtotal {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cantidad-subtotal input {
    width: 60px;
    padding: 0.2rem;
}

.totales {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
}

.payment-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
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
}
</style>
