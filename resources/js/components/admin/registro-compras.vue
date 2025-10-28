<template>
  <div class="registro-compra-container">
    <h2 class="title">Registrar compra</h2>

    <form @submit.prevent="guardarCompra" class="form">
      <div class="form-group">
        <label>Descripción</label>
        <input
          type="text"
          v-model="form.descripcion"
          placeholder="Ej: Compra de bebidas"
        />
      </div>

      <div class="form-group">
        <label>Total (USD)</label>
        <input
          type="number"
          v-model="form.total"
          min="0"
          step="0.01"
          placeholder="Ej: 120.50"
          required
        />
      </div>

      <div class="form-group">
        <label>Fecha de compra</label>
        <input type="date" v-model="form.fecha_compra" />
      </div>

      <div class="form-group">
        <label>Proveedor</label>
        <input
          type="text"
          v-model="form.proveedor"
          placeholder="Ej: Supermercado La Unión"
        />
      </div>

      <div class="form-group">
        <label>Método de pago</label>
        <select v-model="form.metodo_pago">
          <option disabled value="">Seleccionar...</option>
          <option>Efectivo</option>
          <option>Tarjeta</option>
          <option>Transferencia</option>
        </select>
      </div>

      <button type="submit" class="btn-guardar" :disabled="isLoading">
        <span v-if="isLoading">Guardando...</span>
        <span v-else>Guardar compra</span>
      </button>

      <p v-if="mensaje" class="mensaje">{{ mensaje }}</p>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        descripcion: "",
        total: "",
        fecha_compra: new Date().toISOString().slice(0, 10),
        proveedor: "",
        metodo_pago: "",
      },
      isLoading: false,
      mensaje: "",
    };
  },
  methods: {
    async guardarCompra() {
      this.isLoading = true;
      this.mensaje = "";

      try {
        const token = localStorage.getItem("auth_token");
        const response = await fetch(`${import.meta.env.VITE_APP_URL}/api/compras`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify(this.form),
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.message || "Error al guardar");

        this.mensaje = "✅ Compra registrada correctamente.";
        this.form = {
          descripcion: "",
          total: "",
          fecha_compra: new Date().toISOString().slice(0, 10),
          proveedor: "",
          metodo_pago: "",
        };
      } catch (error) {
        console.error(error);
        this.mensaje = "❌ " + error.message;
      } finally {
        this.isLoading = false;
      }
    },
  },
};
</script>

<style scoped>
:root {
  --verde: #97d569;
  --azul: #033961;
  --azul-oscuro: #022a50;
  --texto-claro: #ffffff;
}

/* 🌟 Contenedor principal */
.registro-compra-container {
  background-color: var(--azul);
  padding: 2rem;
  border-radius: 1.2rem;
  max-width: 480px;
  margin: 2rem auto;
  color: var(--texto-claro);
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.35);
  transition: all 0.3s ease;
}

/* 🟢 Título */
.title {
  font-size: 1.7rem;
  margin-bottom: 1.5rem;
  text-align: center;
  font-weight: 700;
  color: var(--verde);
}

/* 📋 Formulario */
.form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

label {
  margin-bottom: 0.4rem;
  color: var(--verde);
  font-size: 0.95rem;
}

/* ✏️ Inputs */
input,
select {
  background-color: var(--azul-oscuro);
  border: 1px solid var(--verde);
  border-radius: 0.6rem;
  padding: 0.7rem 0.8rem;
  color: var(--texto-claro);
  font-size: 1rem;
  transition: all 0.3s ease;
}

input::placeholder,
select::placeholder {
  color: #b5cdd8;
}

input:focus,
select:focus {
  outline: none;
  border-color: var(--verde);
  box-shadow: 0 0 8px var(--verde);
}

/* 💾 Botón */
.btn-guardar {
  background-color: var(--verde);
  color: var(--azul);
  border: none;
  padding: 0.9rem;
  border-radius: 0.7rem;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-guardar:hover {
  background-color: #a8e178;
  transform: scale(1.03);
}

.btn-guardar:disabled {
  background-color: #739e51;
  opacity: 0.8;
  cursor: not-allowed;
}

/* 📩 Mensaje */
.mensaje {
  text-align: center;
  margin-top: 1.2rem;
  font-size: 0.95rem;
  color: var(--verde);
}

/* 📱 Responsive */
@media (max-width: 600px) {
  .registro-compra-container {
    margin: 1rem;
    padding: 1.5rem;
    border-radius: 1rem;
  }

  .title {
    font-size: 1.5rem;
  }

  input,
  select {
    font-size: 0.95rem;
    padding: 0.6rem 0.7rem;
  }

  .btn-guardar {
    padding: 0.8rem;
    font-size: 0.95rem;
  }
}

@media (max-width: 400px) {
  .registro-compra-container {
    padding: 1rem;
  }

  .title {
    font-size: 1.3rem;
  }

  input,
  select {
    font-size: 0.9rem;
  }

  .btn-guardar {
    padding: 0.7rem;
    font-size: 0.9rem;
  }
}
</style>
