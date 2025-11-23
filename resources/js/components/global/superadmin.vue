<template>
  <div v-if="visible" class="modal-overlay">
    <div class="modal">

      <h3>{{ title }}</h3>

      <input 
        type="password" 
        v-model="codigo" 
        placeholder="Código..." 
        class="superadmin-input"
      />

      <div class="numpad">
        <button 
          v-for="n in [1,2,3,4,5,6,7,8,9]" 
          :key="n"
          class="numpad-btn"
          @click="append(n)"
        >
          {{ n }}
        </button>

        <button class="numpad-btn empty"></button>

        <button class="numpad-btn" @click="append(0)">0</button>

        <button class="numpad-btn delete" @click="backspace">⌫</button>
      </div>

      <div class="modal-buttons">
        <button class="btn-confirm" @click="emitConfirm">Confirmar</button>
        <button class="btn-cancel" @click="emitCancel">Cancelar</button>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: "superadmin",
  props: {
    visible: Boolean,
    title: {
      type: String,
      default: "Ingrese código superadmin"
    }
  },
  data() {
    return {
      codigo: ""
    };
  },
  methods: {
    append(n) {
      this.codigo += n.toString();
    },
    backspace() {
      this.codigo = this.codigo.slice(0, -1);
    },
    emitConfirm() {
      this.$emit("confirm", this.codigo);
      this.codigo = "";
    },
    emitCancel() {
      this.$emit("cancel");
      this.codigo = "";
    }
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}
.modal h3 {
  text-align: center;
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #cbd5e1;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #2c3340;
  padding-bottom: 0.5rem;
}

.modal {
  background-color: #1a1f2b;
  padding: 1.5rem;
  border-radius: 10px;
  width: 320px;
  color: #fff;
  border: 1px solid #2c3340;
}

.superadmin-input {
  width: 100%;
  background: #1f2633;
  border: 1px solid #333b4d;
  border-radius: 6px;
  padding: 0.7rem;
  margin-bottom: 1rem;
  color: white;
  font-size: 1.2rem;
  text-align: center;
}

.numpad {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.6rem;
  margin-bottom: 1rem;
}

.numpad-btn {
  background-color: #2c3340;
  border: none;
  border-radius: 8px;
  padding: 1rem;
  font-size: 1.4rem;
  font-weight: bold;
  color: white;
  cursor: pointer;
  transition: 0.2s;
}

.numpad-btn:hover {
  background-color: #3b455a;
}

.numpad-btn.delete {
  background-color: #ef4444;
  color: white;
}

.numpad-btn.delete:hover {
  background-color: #c73333;
}

.empty {
  background: transparent !important;
  pointer-events: none;
}

.modal-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.btn-confirm {
  background-color: #10b981;
  color: white;
  padding: 0.6rem 1rem;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}

.btn-cancel {
  background-color: #ef4444;
  color: white;
  padding: 0.6rem 1rem;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}
</style>
