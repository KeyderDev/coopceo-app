<template>
  <div class="download-panel">
    <h2>Panel de Descargas</h2>

    <div class="downloads-list">
      <h3>Informes disponibles</h3>

      <div
        class="download-item"
        v-for="file in informes"
        :key="file.id"
      >
        <div class="file-info">
          <span class="file-icon">📄</span>
          <div class="file-text">
            <p class="filename" :title="file.name">{{ file.name }}</p>
            <p class="file-type">{{ file.type }} • {{ file.frequency }}</p>
          </div>
        </div>

        <!-- Selección de mes para el informe real -->
        <div v-if="file.real && file.selectMonth" class="month-select">
          <select v-model="selectedMonth">
            <option disabled value="">Selecciona un mes</option>
            <option
              v-for="m in months"
              :key="m.value"
              :value="m.value"
            >
              {{ m.label }}
            </option>
          </select>
          <button @click="descargarPDFReal" :disabled="!selectedMonth">
            Descargar
          </button>
        </div>

        <!-- Botón principal -->
        <button
          v-else
          @click="file.real ? activarSeleccionMes(file) : descargarFicticio(file)"
        >
          Descargar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { reactive, ref } from "vue";

// Lista de informes
const informes = reactive([
  { id: 1, name: "Informe Operacional", type: "PDF", frequency: "Mensual", real: true, selectMonth: false },
  { id: 2, name: "Reporte de Inventario", type: "Excel", frequency: "Semanal", real: false },
  { id: 3, name: "Lista de Clientes", type: "CSV", frequency: "Diario", real: false },
  { id: 4, name: "Resumen Semanal", type: "Word", frequency: "Semanal", real: false },
]);

const selectedMonth = ref("");

const months = [];
const currentYear = new Date().getFullYear();
for (let y = currentYear; y >= currentYear - 5; y--) {
  for (let m = 1; m <= 12; m++) {
    const monthValue = `${y}-${m.toString().padStart(2, "0")}`;
    const monthLabel = new Date(y, m - 1).toLocaleString("es-ES", { month: "long", year: "numeric" });
    months.push({ value: monthValue, label: monthLabel });
  }
}

const activarSeleccionMes = (file) => {
  file.selectMonth = true;
};

const descargarPDFReal = async () => {
  if (!selectedMonth.value) return;

  try {
    const response = await axios.get(
      `${import.meta.env.VITE_APP_URL}/api/sales/resumen-mensual/${selectedMonth.value}`,
      { responseType: "blob" }
    );

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", `resumen_mensual_${selectedMonth.value}.pdf`);
    document.body.appendChild(link);
    link.click();

    selectedMonth.value = "";
    informes.find(f => f.real).selectMonth = false;
  } catch (error) {
    console.error("Error al descargar PDF:", error);
    alert("Error al descargar el informe.");
  }
};

const descargarFicticio = (file) => {
  alert(`Simulación: descargando ${file.name}`);
};
</script>

<style scoped>
.download-panel {
  max-width: 700px;
  margin: 3rem auto;
  padding: 2rem;
  background: #03355c;
  border-radius: 16px;
  color: #fff;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}

.download-panel h2 {
  color: #97d569;
  text-align: center;
  font-size: 2rem;
  margin-bottom: 1.5rem;
}

.downloads-list h3 {
  color: #97d569;
  margin-bottom: 1rem;
  font-size: 1.2rem;
}

.download-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #1e1e1e;
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 10px;
  transition: background 0.3s;
  gap: 10px;
}

.download-item:hover {
  background: #2c2c2c;
}

.file-info {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.file-text {
  min-width: 0;
}

.filename {
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.file-type {
  font-size: 0.85rem;
  color: #ccc;
  white-space: nowrap;
}

button {
  background-color: #97d569;
  color: #03355c;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
  flex-shrink: 0;
}

button:hover:not(:disabled) {
  background-color: #7ebd50;
  transform: translateY(-2px);
}

button:disabled {
  background-color: #555;
  cursor: not-allowed;
}

.month-select {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.month-select select {
  padding: 0.4rem 0.6rem;
  border-radius: 6px;
  border: 1px solid #97d569;
  background: #1e1e1e;
  color: #fff;
  font-size: 0.95rem;
}

/* Responsivo */
@media (max-width: 600px) {
  .download-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  .month-select {
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
  }
  .month-select select,
  .month-select button {
    width: 100%;
  }
}
</style>
