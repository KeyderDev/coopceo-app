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
  background: #e0e0e0;
  color: #333;
  display: flex;
  flex-direction: column;
  padding: 2rem 3rem;
  font-family: "Inter", sans-serif;
}

/* === GRID PRINCIPAL === */
.compra-section {
  display: grid;
  grid-template-columns: 60% 40%;
  gap: 2.5rem;
  width: 100%;
}

/* === PANEL FORMULARIO === */
.form-panel {
  background: white;
  border-radius: 16px;
  padding: 2.5rem 2rem 3rem;
  border: 2px solid #4caf50;
  box-shadow: 0 8px 25px rgba(76, 175, 80, 0.15);
}

.form-panel h2 {
  margin-bottom: 2rem;
  color: #4caf50;
  font-size: 1.7rem;
}

/* === FORMULARIO === */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem 2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

input,
select {
  background: #f8f8f8;
  color: #333;
  border: 1px solid #ccc;
  border-radius: 10px;
  padding: 0.9rem 1rem;
  font-size: 1rem;
  transition: all 0.25s ease;
}

input:focus,
select:focus {
  outline: none;
  border-color: #4caf50;
  box-shadow: 0 0 10px #4caf5055;
}

/* === BOTONES === */
.btn-container {
  display: flex;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

.btn-guardar {
  background: #4caf50;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 1rem 1.8rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: 0.25s;
}

.btn-guardar:hover {
  background: #43a047;
}

/* === TABLA === */
.tabla-wrapper {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: #f8f8f8;
  border-radius: 10px;
}

th,
td {
  padding: 1rem;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

th {
  background: #4caf50;
  color: #fff;
  font-weight: 600;
}

tr:hover {
  background: #eafbea;
}

/* === PANEL DERECHO === */
.visual-panel {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  border: 2px solid #4caf50;
  box-shadow: 0 8px 25px rgba(76, 175, 80, 0.15);
}

.preview-card {
  background: #f4f4f4;
  border-radius: 12px;
  padding: 1.2rem;
  border: 1px solid #ccc;
}

.preview-card h4 {
  color: #4caf50;
  margin-bottom: 0.8rem;
}

/* === BOTÓN VOLVER === */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 30px;
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 0.9rem 1.4rem;
  border-radius: 12px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: 0.25s;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.btn-volver:hover {
  background-color: #43a047;
}

/* === RESPONSIVE === */
@media (max-width: 1000px) {
  .compra-section {
    grid-template-columns: 1fr;
  }

  .visual-panel {
    margin-top: 2rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .page-wrapper {
    padding: 1.2rem;
  }

  .form-panel,
  .visual-panel {
    padding: 1.5rem;
  }

  h2 {
    font-size: 1.4rem;
  }

  .btn-guardar {
    width: 100%;
  }

  th,
  td {
    padding: 0.7rem;
    font-size: 0.9rem;
  }

  .btn-volver {
    position: static;
    margin-top: 1.5rem;
    width: 100%;
    text-align: center;
  }
}
</style>
