<template>
    <div class="products-container">
        <h2>Productos</h2>

        <!-- Formulario para crear producto -->
        <form @submit.prevent="crearProducto" class="product-form">
            <div>
                <label>Nombre:</label>
                <input type="text" v-model="nuevoProducto.nombre" required />
            </div>

            <div>
                <label>Precio:</label>
                <input type="number" v-model.number="nuevoProducto.precio" step="0.01" required />
            </div>

            <div>
                <label>Categoría:</label>
                <input type="text" v-model="nuevoProducto.categoria" required />
            </div>

            <button type="submit">Agregar Producto</button>
        </form>

        <!-- Lista de productos -->
        <h3>Productos existentes</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="producto in productos" :key="producto.id">
                    <td>{{ producto.nombre }}</td>
                    <td>{{ Number(producto.precio).toFixed(2) }}</td>
                    <td>{{ producto.categoria }}</td>
                    <td>
                        <button class="delete-btn" @click="eliminarProducto(producto.id)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            productos: [],
            nuevoProducto: {
                nombre: "",
                precio: 0,
                categoria: ""
            }
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
                this.nuevoProducto = { nombre: "", precio: 0, categoria: "" };
            } catch (error) {
                console.error("Error al crear producto:", error);
            }
        },
        async eliminarProducto(id) {
            if (!confirm("¿Seguro que deseas eliminar este producto?")) return;

            try {
                await axios.delete(`/api/products/${id}`);
                // Quitar el producto de la lista local
                this.productos = this.productos.filter(p => p.id !== id);
            } catch (error) {
                console.error("Error al eliminar producto:", error);
            }
        }
    }
};
</script>

<style scoped>
.products-container {
    max-width: 750px;
    margin: 2rem auto;
    padding: 2rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    color: #033861;
}

h2, h3 {
    color: #033861;
    margin-bottom: 1rem;
    font-weight: 600;
}

.product-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.product-form div {
    display: flex;
    flex-direction: column;
}

.product-form label {
    margin-bottom: 0.4rem;
    font-weight: 600;
    color: #033861;
}

.product-form input {
    padding: 0.6rem;
    border: 1px solid #ccc;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.product-form input:focus {
    border-color: #97d569;
    box-shadow: 0 0 6px rgba(151, 213, 105, 0.4);
    outline: none;
}

button {
    padding: 0.65rem 1.3rem;
    background-color: #97d569;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    align-self: flex-start;
}

button:hover {
    background-color: #033861;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 56, 97, 0.3);
}

.table-wrapper {
    overflow-x: auto;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px; 
}

table th {
    background-color: #033861;
    color: #97d569;
    padding: 0.8rem;
    font-weight: 600;
    text-align: left;
}

table td {
    padding: 0.7rem;
    border-bottom: 1px solid #e6e6e6;
}

table tr:last-child td {
    border-bottom: none;
}

table tr:hover {
    background-color: #f0f9f5;
    transition: background-color 0.3s;
}

.delete-btn {
    padding: 0.45rem 0.9rem;
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

/* --- MEDIA QUERIES PARA MÓVILES --- */
@media (max-width: 600px) {
    .products-container {
        padding: 1rem;
        margin: 1rem;
    }

    .product-form input {
        font-size: 0.9rem;
        padding: 0.5rem;
    }

    button {
        width: 100%;
        text-align: center;
    }
}
</style>

