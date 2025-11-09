<template>
  <div class="page-wrapper">
    <section class="compra-section">
      <!-- PANEL IZQUIERDO -->
      <div class="form-panel">
        <h2><i class="fa-solid fa-receipt"></i> Registrar nueva compra</h2>

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
const volverMenu = () => router.push("/");

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
/* 🌌 Fondo general */
.page-wrapper {
  min-height: 100vh;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  color: #fff;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 2rem;
  font-family: "Inter", sans-serif;
  box-sizing: border-box;
}

/* === Estructura === */
.compra-section {
  display: flex;
  flex-wrap: wrap;
  gap: 1.8rem;
  width: 100%;
  margin: 0 auto;
}

/* === Panel formulario === */
.form-panel {
  flex: 2 1 600px;
  background: rgba(25, 27, 31, 0.85);
  border: 1px solid rgba(157, 216, 106, 0.15);
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
  animation: fadeIn 0.6s ease-in-out;
}

.form-panel h2 {
  margin-bottom: 1.5rem;
  color: #9dd86a;
  font-size: 1.6rem;
  text-align: center;
}

/* === Inputs y selects === */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 1rem;
}

label {
  font-weight: 600;
  margin-bottom: 0.4rem;
  color: #9dd86a;
}

input,
select {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(157, 216, 106, 0.2);
  border-radius: 8px;
  padding: 0.8rem;
  color: #fff;
  font-size: 1rem;
  transition: 0.2s;
}

input:focus,
select:focus {
  border-color: #9dd86a;
  box-shadow: 0 0 6px rgba(157, 216, 106, 0.5);
  outline: none;
}

/* === Botón guardar === */
.btn-container {
  margin-top: 1.5rem;
  display: flex;
  justify-content: center;
}

.btn-guardar {
  width: 100%;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 1rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-guardar:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-2px);
}

/* === Tabla === */
.tabla-wrapper {
  margin-top: 1.5rem;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 10px;
}

th,
td {
  padding: 0.8rem;
  text-align: left;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

th {
  background: rgba(157, 216, 106, 0.1);
  color: #9dd86a;
  text-transform: uppercase;
  font-size: 0.9rem;
}

/* === Vista previa === */
.visual-panel {
  flex: 1 1 320px;
  background: rgba(25, 27, 31, 0.85);
  border-radius: 20px;
  border: 1px solid rgba(157, 216, 106, 0.15);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
  padding: 1.5rem;
  align-self: flex-start;
}

.preview-card {
  background: rgba(255, 255, 255, 0.05);
  padding: 1rem;
  border-radius: 12px;
}

.preview-card h4 {
  color: #9dd86a;
  margin-bottom: 1rem;
}

/* === Botón volver === */
.btn-volver {
  margin-top: 2rem;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  border: none;
  padding: 1rem 1.4rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: 0.3s;
  align-self: center;
}

.btn-volver:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-2px);
}

/* === Responsive === */
@media (max-width: 768px) {
  .page-wrapper {
    padding: 1rem;
  }

  .compra-section {
    flex-direction: column;
  }

  .form-panel,
  .visual-panel {
    width: 100%;
    padding: 1.2rem;
  }

  table {
    font-size: 0.9rem;
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
