<template>
  <div class="pdf-download">
    <h2>Descargar Resumen Mensual</h2>
    <input type="month" v-model="mesSeleccionado" />
    <button @click="descargarPDF" :disabled="!mesSeleccionado">
      Descargar PDF
    </button>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";


const mesSeleccionado = ref("");

const descargarPDF = async () => {
  if (!mesSeleccionado.value) return;

  try {
    const response = await axios.get(
      `${import.meta.env.VITE_APP_URL}/api/sales/resumen-mensual/${mesSeleccionado.value}`,
      { responseType: "blob" }
    );

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", `resumen_mensual_${mesSeleccionado.value}.pdf`);
    document.body.appendChild(link);
    link.click();
  } catch (error) {
    console.error("Error al descargar PDF:", error);
  }
};
</script>

<style scoped>
.pdf-download {
  background: #1e1e1e;
  color: #fff;
  padding: 1.5rem;
  border-radius: 12px;
  text-align: center;
  max-width: 400px;
  margin: 2rem auto;
}
input {
  background: #2c2c2c;
  border: none;
  color: #fff;
  padding: 0.5rem;
  margin-right: 10px;
  border-radius: 6px;
}
button {
  background-color: #007acc;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
button:disabled {
  background-color: #555;
  cursor: not-allowed;
}
</style>
