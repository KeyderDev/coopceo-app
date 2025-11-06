<template>
  <div class="page-wrapper">
    <section class="compra-section">
      <!-- PANEL IZQUIERDO -->
      <div class="form-panel">
        <h2>Registrar nueva compra</h2>

        <form @submit.prevent="guardarCompra" class="compra-form">
          <div class="form-grid">
            <div class="form-group">
              <label>Descripción</label>
              <input type="text" v-model="form.descripcion" placeholder="Ej: Compra de bebidas" required />
            </div>

            <div class="form-group">
              <label>Proveedor</label>
              <input type="text" v-model="form.proveedor" placeholder="Ej: PepsiCo PR" required />
            </div>

            <div class="form-group">
              <label>Método de pago</label>
              <select v-model="form.metodo_pago" required>
                <option disabled value="">Selecciona un método</option>
                <option value="efectivo">Efectivo</option>
                <option value="athmovil">ATH Móvil</option>
                <option value="transferencia">Transferencia bancaria</option>
                <option value="cheque">Cheque</option>
              </select>
            </div>

            <div class="form-group">
              <label>Total (USD)</label>
              <input type="number" v-model="form.total" step="0.01" placeholder="Ej: 230.50" required />
            </div>

            <div class="form-group">
              <label>Fecha de compra</label>
              <input type="date" v-model="form.fecha_compra" required />
            </div>
          </div>

          <div class="btn-container">
            <button type="submit" class="btn-guardar" :disabled="isLoading">
              <span v-if="!isLoading"><i class="fa-solid fa-circle-check"></i> Guardar compra</span>
              <span v-else class="loader"></span>
            </button>
          </div>

          <p v-if="mensaje" class="mensaje" :class="{ error: mensaje.includes('❌') }">{{ mensaje }}</p>
        </form>

        <!-- TABLA DE COMPRAS -->
        <div class="tabla-compras" v-if="compras.length">
          <h3>🧾 Compras registradas</h3>
          <div class="tabla-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Descripción</th>
                  <th>Proveedor</th>
                  <th>Método</th>
                  <th>Total</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="compra in compras" :key="compra.id">
                  <td>{{ compra.descripcion || "—" }}</td>
                  <td>{{ compra.proveedor || "—" }}</td>
                  <td>{{ compra.metodo_pago || "—" }}</td>
                  <td>${{ parseFloat(compra.total).toFixed(2) }}</td>
                  <td>{{ formatFecha(compra.fecha_compra) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <p v-else class="sin-registros">No hay compras registradas aún.</p>
      </div>

      <!-- PANEL DERECHO -->
      <div class="visual-panel">
        <div class="preview-card">
          <h4>Vista previa</h4>
          <p><strong>Descripción:</strong> {{ form.descripcion || "—" }}</p>
          <p><strong>Proveedor:</strong> {{ form.proveedor || "—" }}</p>
          <p><strong>Método:</strong> {{ form.metodo_pago || "—" }}</p>
          <p><strong>Total:</strong> {{ form.total ? `$${form.total}` : "—" }}</p>
          <p><strong>Fecha:</strong> {{ form.fecha_compra || "—" }}</p>
        </div>
      </div>
    </section>

    <button class="btn-volver" @click="volverMenu">
      <i class="fa-solid fa-house"></i> Menú Principal
    </button>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";

const isLoading = ref(false);
const mensaje = ref("");
const compras = ref([]);

const form = reactive({
  descripcion: "",
  proveedor: "",
  metodo_pago: "",
  total: "",
  fecha_compra: "",
});

onMounted(() => {
  form.fecha_compra = new Date().toISOString().slice(0, 10);
  cargarCompras();
});

const router = useRouter();

const volverMenu = () => {
  router.push("/");
};

async function cargarCompras() {
  try {
    const token = localStorage.getItem("auth_token");
    const response = await fetch(`${import.meta.env.VITE_APP_URL}/api/compras`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (!response.ok) throw new Error("Error al obtener compras");
    compras.value = await response.json();
  } catch (error) {
    console.error(error);
  }
}
function formatFecha(fecha) {
  if (!fecha) return "—";
  return new Date(fecha).toISOString().slice(0, 10);
}

async function guardarCompra() {
  isLoading.value = true;
  mensaje.value = "";

  try {
    const token = localStorage.getItem("auth_token");
    const response = await fetch(`${import.meta.env.VITE_APP_URL}/api/compras`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(form),
    });

    const data = await response.json();
    if (!response.ok) throw new Error(data.message || "Error al guardar");

    mensaje.value = "✅ Compra registrada correctamente.";
    form.descripcion = "";
    form.proveedor = "";
    form.metodo_pago = "";
    form.total = "";
    form.fecha_compra = new Date().toISOString().slice(0, 10);

    await cargarCompras();
  } catch (error) {
    console.error(error);
    mensaje.value = "❌ " + error.message;
  } finally {
    isLoading.value = false;
  }
}
</script>

<style scoped>
.page-wrapper {
  min-height: 100vh;
  background: #f1f1f1;
  color: #333;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 1rem;
  font-family: "Inter", sans-serif;
  width: 100%;
  box-sizing: border-box;
}

.compra-section {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  width: 100%;
  margin: 0 auto;
}

/* PANEL FORMULARIO */
.form-panel {
  flex: 2 1 600px;
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #4caf50;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
}

.form-panel h2 {
  margin-bottom: 1.5rem;
  color: #388e3c;
  font-size: 1.6rem;
  text-align: center;
}

/* FORMULARIO */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: 600;
  margin-bottom: 0.4rem;
}

input,
select {
  background: #fafafa;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 0.8rem;
  font-size: 1rem;
  transition: 0.2s;
}

input:focus,
select:focus {
  border-color: #4caf50;
  box-shadow: 0 0 6px #4caf5055;
  outline: none;
}

/* BOTÓN */
.btn-container {
  margin-top: 1.5rem;
  display: flex;
  justify-content: center;
}

.btn-guardar {
  width: 100%;
  background: #4caf50;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 1rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-guardar:hover {
  background: #43a047;
}

/* TABLA */
.tabla-wrapper {
  margin-top: 1.5rem;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: #f8f8f8;
  border-radius: 8px;
}

th,
td {
  padding: 0.8rem;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background: #4caf50;
  color: white;
  font-weight: 600;
}

tr:hover {
  background: #e8f5e9;
}

/* PANEL VISTA PREVIA */
.visual-panel {
  flex: 1 1 320px;
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #4caf50;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
  align-self: flex-start;
}

.preview-card {
  background: #f7f7f7;
  padding: 1rem;
  border-radius: 10px;
}

.preview-card h4 {
  color: #388e3c;
  margin-bottom: 0.8rem;
}

/* BOTÓN VOLVER */
.btn-volver {
  margin-top: 2rem;
  width: 100%;
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 10px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: 0.25s;
}

.btn-volver:hover {
  background-color: #43a047;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .page-wrapper {
    padding: 0.5rem;
  }

  .compra-section {
    flex-direction: column;
  }

  .form-panel,
  .visual-panel {
    width: 100%;
    padding: 1.2rem;
  }

  h2 {
    font-size: 1.4rem;
  }

  table {
    font-size: 0.9rem;
  }

  th,
  td {
    padding: 0.6rem;
  }

  .btn-volver {
    margin-bottom: 2rem;
  }
}
</style>
