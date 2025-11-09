<template>
  <div class="download-panel">
    <h2>Panel de Descargas</h2>

    <div class="downloads-list">
      <h3>Informes disponibles</h3>

      <div class="download-item" v-for="file in informes" :key="file.id">
        <div class="file-info">
          <span class="file-icon">📄</span>
          <div class="file-text">
            <p class="filename" :title="file.name">{{ file.name }}</p>
            <p class="file-type">{{ file.type }} • {{ file.frequency }}</p>
          </div>
        </div>

        <div v-if="file.real && file.selectMonth" class="month-select">
          <select v-model="selectedMonth">
            <option disabled value="">Selecciona un mes</option>
            <option v-for="m in months" :key="m.value" :value="m.value">
              {{ m.label }}
            </option>
          </select>
          <button @click="descargarPDFReal" :disabled="!selectedMonth">
            Descargar
          </button>
        </div>

        <button v-else @click="file.real ? activarSeleccionMes(file) : descargarFicticio(file)">
          Descargar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

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

const volverMenu = () => {
  router.push("/"); 
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
  max-width: 1000px;
  width: 90%;
  margin: 3rem auto;
  padding: 2rem;
  background: rgba(30, 30, 30, 0.9);
  border-radius: 16px;
  color: #f5f7fa;
  font-family: "Inter", sans-serif;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(10px);
}

/* 🧭 Título principal */
.download-panel h2 {
  color: #9dd86a;
  text-align: center;
  font-size: 2rem;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid rgba(157, 216, 106, 0.3);
  padding-bottom: 0.4rem;
  text-shadow: 0 0 6px rgba(157, 216, 106, 0.3);
}

/* 📂 Subtítulo */
.downloads-list h3 {
  color: #9dd86a;
  margin-bottom: 1rem;
  font-size: 1.3rem;
  border-left: 4px solid #9dd86a;
  padding-left: 0.6rem;
}

/* 📄 Cada item */
.download-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba(25, 27, 31, 0.85);
  padding: 1rem 1.2rem;
  border-radius: 12px;
  margin-bottom: 0.8rem;
  border: 1px solid rgba(255, 255, 255, 0.05);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
  transition: all 0.3s ease;
  gap: 20px;
}

.download-item:hover {
  background: rgba(35, 37, 41, 0.9);
  border-color: rgba(157, 216, 106, 0.25);
  transform: translateY(-2px);
}

/* 📘 Info del archivo */
.file-info {
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: 0;
  flex: 2;
}

.file-icon {
  font-size: 1.5rem;
}

.file-text {
  min-width: 0;
}

.filename {
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #f5f7fa;
}

.file-type {
  font-size: 0.85rem;
  color: #b3b3b3;
  white-space: nowrap;
}

/* 🟩 Botones */
button {
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  font-weight: 600;
  padding: 0.6rem 1.3rem;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

button:hover:not(:disabled) {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-3px);
}

button:disabled {
  background: rgba(100, 100, 100, 0.5);
  cursor: not-allowed;
}

/* 🗓️ Selección de mes */
.month-select {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.month-select select {
  padding: 0.5rem 0.8rem;
  border-radius: 8px;
  border: 1px solid rgba(157, 216, 106, 0.3);
  background: rgba(14, 17, 23, 0.9);
  color: #f5f7fa;
  font-size: 0.95rem;
  transition: all 0.25s ease;
}

.month-select select:focus {
  outline: none;
  border-color: #9dd86a;
  box-shadow: 0 0 8px rgba(157, 216, 106, 0.3);
}

/* 🏠 Botón Volver */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 25px;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: white;
  border: none;
  padding: 0.8rem 1.3rem;
  border-radius: 10px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
  transition: all 0.25s ease;
  z-index: 1000;
}

.btn-volver:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-3px);
}

/* 🌗 Scrollbar personalizada */
.download-panel::-webkit-scrollbar {
  width: 10px;
}
.download-panel::-webkit-scrollbar-thumb {
  background: rgba(157, 216, 106, 0.3);
  border-radius: 10px;
}
.download-panel::-webkit-scrollbar-thumb:hover {
  background: rgba(157, 216, 106, 0.6);
}
.download-panel::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

/* 📱 Responsive */
@media (max-width: 1000px) {
  .download-panel {
    width: 95%;
  }
}

@media (max-width: 600px) {
  .download-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
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
