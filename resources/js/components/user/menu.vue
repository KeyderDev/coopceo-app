<template>
  <div class="productos-wrapper">
    <h2 class="titulo">
      <i class="fa-solid fa-boxes-stacked"></i> Inventario
    </h2>

    <div class="filtros">
      <input 
        type="text" 
        v-model="busqueda"
        placeholder="Buscar producto..."
        class="input-busqueda"
      />

      <select v-model="categoriaSeleccionada" class="select-categoria">
        <option value="">Todas las categorías</option>
        <option v-for="cat in categorias" :key="cat" :value="cat">
          {{ cat }}
        </option>
      </select>

      <button 
        class="filtro-btn" 
        :class="{ activo: filtroStock === 'stock' }"
        @click="filtroStock = filtroStock === 'stock' ? '' : 'stock'"
      >
        Solo Stock
      </button>

      <button 
        class="filtro-btn" 
        :class="{ activo: filtroStock === 'no-stock' }"
        @click="filtroStock = filtroStock === 'no-stock' ? '' : 'no-stock'"
      >
        Fuera de Stock
      </button>

      <select v-model="orden" class="select-categoria">
        <option value="">Ordenar…</option>
        <option value="az">A → Z</option>
        <option value="za">Z → A</option>
        <option value="precioAsc">Precio ↑</option>
        <option value="precioDesc">Precio ↓</option>
      </select>

      <button class="refresh-btn" @click="cargarProductos">
        <i class="fa-solid fa-rotate"></i>
      </button>
    </div>

    <p v-if="loading" class="loading">Cargando productos...</p>

    <div v-else class="productos-grid">
      <div
        v-for="producto in productosProcesados"
        :key="producto.id"
        class="producto-card"
      >
        <div class="producto-header">
          <h3>{{ producto.nombre }}</h3>

          <span
            class="status"
            :class="producto.stock > 0 ? 'en-stock' : 'sin-stock'"
          >
            <i :class="producto.stock > 0 ? 'fa-solid fa-check' : 'fa-solid fa-xmark'"></i>
            {{ producto.stock > 0 ? "Stock" : "Fuera de Stock" }}
          </span>
        </div>

        <p class="categoria">Categoría: {{ producto.categoria }}</p>
        <p class="precio">${{ producto.precio.toFixed(2) }}</p>
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
      loading: true,
      busqueda: "",
      categoriaSeleccionada: "",
      filtroStock: "",
      orden: "",
    };
  },

  computed: {
    categorias() {
      return [...new Set(this.productos.map((p) => p.categoria))];
    },

    productosProcesados() {
      let lista = [...this.productos];

      lista = lista.filter((p) => {
        const text = this.busqueda.toLowerCase();
        return (
          p.nombre.toLowerCase().includes(text) ||
          p.categoria.toLowerCase().includes(text)
        );
      });

      if (this.categoriaSeleccionada) {
        lista = lista.filter((p) => p.categoria === this.categoriaSeleccionada);
      }

      if (this.filtroStock === "stock") {
        lista = lista.filter((p) => p.stock > 0);
      } 
      else if (this.filtroStock === "no-stock") {
        lista = lista.filter((p) => p.stock === 0);
      }

      switch (this.orden) {
        case "az":
          lista.sort((a, b) => a.nombre.localeCompare(b.nombre));
          break;
        case "za":
          lista.sort((a, b) => b.nombre.localeCompare(a.nombre));
          break;
        case "precioAsc":
          lista.sort((a, b) => a.precio - b.precio);
          break;
        case "precioDesc":
          lista.sort((a, b) => b.precio - a.precio);
          break;
      }

      return lista;
    },
  },

  async created() {
    await this.cargarProductos();
  },

  methods: {
    async cargarProductos() {
      try {
        this.loading = true;
        const token = localStorage.getItem("auth_token");

        const res = await axios.get(`${import.meta.env.VITE_APP_URL}/api/products`, {
          headers: { Authorization: `Bearer ${token}` },
        });

        let productosAPI = Array.isArray(res.data)
          ? res.data
          : Array.isArray(res.data.data)
          ? res.data.data
          : [];

        this.productos = productosAPI.map((p) => ({
          id: p.id,
          nombre: p.nombre,
          categoria: p.categoria,
          precio: Number(p.precio),
          stock: Number(p.stock),
        }));

      } catch (error) {
        console.error("Error cargando productos:", error);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.productos-wrapper {
  width: 100%;
  padding: 1rem;
  background: #0e1117;
  color: #fff;
}

.titulo {
  color: #97d569;
  text-align: center;
  font-size: 1.6rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.filtros {
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
  margin-bottom: 1rem;
}

.input-busqueda,
.select-categoria,
.filtro-btn {
  background: #162029;
  border: 1px solid rgba(255,255,255,0.08);
  color: #fff;
  padding: 12px;
  border-radius: 10px;
  font-size: 1rem;
  width: 100%;
  box-sizing: border-box;
}

.input-busqueda:focus,
.select-categoria:focus {
  outline: none;
  border-color: #97d569;
}

.filtro-btn {
  cursor: pointer;
  text-align: center;
  font-weight: 500;
  transition: 0.2s;
}

.filtro-btn.activo {
  background: #97d569;
  color: #0e1117;
  font-weight: bold;
}

.refresh-btn {
  background: #97d569;
  color: #0e1117;
  border: none;
  padding: 12px;
  border-radius: 10px;
  font-size: 1.2rem;
  cursor: pointer;
  width: 100%;
}

.refresh-btn:hover {
  background: #b4f67f;
}

.loading {
  text-align: center;
  margin-top: 1.5rem;
  color: #97d569;
  font-size: 1.2rem;
}

.productos-grid {
  margin-top: 1rem;
  display: grid;
  gap: 1rem;
  grid-template-columns: 1fr;
}

@media (min-width: 700px) {
  .filtros {
    flex-direction: row;
    flex-wrap: wrap;
  }

  .input-busqueda {
    flex: 1;
  }

  .select-categoria {
    width: 180px;
  }

  .filtro-btn {
    width: 150px;
  }

  .refresh-btn {
    width: auto;
  }

  .productos-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1100px) {
  .productos-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1400px) {
  .productos-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.producto-card {
  background: #162029;
  padding: 1rem;
  border-radius: 14px;
  border: 1px solid rgba(255,255,255,0.06);
  transition: 0.3s;
  box-shadow: 0 4px 14px rgba(0,0,0,0.28);
}

.producto-card:hover {
  transform: translateY(-5px);
  border-color: #97d569;
}

.producto-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.producto-header h3 {
  color: #97d569;
  font-size: 1.15rem;
  margin: 0;
}

.status {
  padding: 6px 10px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.85rem;
}

.en-stock {
  background: rgba(151,213,105,0.2);
  color: #97d569;
}

.sin-stock {
  background: rgba(255,65,65,0.2);
  color: #ff6b6b;
}

.categoria {
  color: #b9c4ce;
  margin-top: 0.4rem;
}

.precio {
  color: #97d569;
  margin-top: 8px;
  font-weight: 600;
  font-size: 1.05rem;
}
</style>
