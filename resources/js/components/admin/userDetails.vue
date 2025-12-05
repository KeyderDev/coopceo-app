<template>
  <div class="user-detail">
    <div class="header">
      <h2><i class="fa-solid fa-user-gear"></i> Administrar Socio</h2>
      <button @click="$router.push('/users')" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i> Volver
      </button>
    </div>

    <div v-if="user" class="user-card">
      <div class="user-info">
        <div class="info-field">
          <span class="label">Número de socio:</span>
          <span>{{ user.numero_socio }}</span>
        </div>
        <div class="info-field">
          <span class="label">Nombre:</span>
          <span>{{ user.nombre }} {{ user.apellido }}</span>
        </div>
        <div class="info-field">
          <span class="label">Email:</span>
          <span>{{ user.email }}</span>
        </div>
        <div class="info-field">
          <span class="label">Teléfono:</span>
          <span>{{ user.telefono || '—' }}</span>
        </div>
        <div class="info-field">
          <span class="label">Balance:</span>
          <span class="balance">${{ user.dividendos ?? 0 }}</span>
        </div>
        <div class="info-field">
          <span class="label">Administrador:</span>
          <span :class="['admin-status', user.admin ? 'on' : 'off']">
            {{ user.admin ? 'Sí' : 'No' }}
          </span>
        </div>
      </div>

      <div class="actions">
        <button @click="editMode = !editMode" :class="['edit-btn', editMode ? 'cancelar' : '']">
          <i class="fa-solid" :class="editMode ? 'fa-xmark' : 'fa-pen-to-square'"></i>
          {{ editMode ? "Cancelar edición" : "Editar información" }}
        </button>
      </div>

      <transition name="fade">
        <div v-if="editMode" class="edit-section">
          <h3><i class="fa-solid fa-pen"></i> Editar información</h3>

          <div class="form-grid">
            <input v-model="user.numero_socio" placeholder="Número de socio" />
            <input v-model="user.nombre" placeholder="Nombre" />
            <input v-model="user.apellido" placeholder="Apellido" />
            <input v-model="user.telefono" placeholder="Teléfono" />
            <input v-model="user.email" placeholder="Email" />

            <div class="admin-toggle">
              <label>Administrador:</label>
              <label class="switch">
                <input type="checkbox" v-model="user.admin" true-value="1" false-value="0" />
                <span class="slider"></span>
              </label>
            </div>
          </div>

          <button @click="guardarCambios" class="save-btn">
            <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
          </button>
        </div>
      </transition>
    </div>

    <div v-if="filteredTransactions.length" class="transactions-card">
      <h3><i class="fa-solid fa-receipt"></i> Últimas transacciones</h3>
      <div class="transactions-list">
        <div v-for="tx in filteredTransactions" :key="tx.id" class="transaction-item">
          <div class="tx-left">
            <p class="tx-id">Venta #{{ tx.id }}</p>
            <p class="tx-date">{{ formatDate(tx.created_at) }}</p>
            <p class="tx-cajero">Cajero: {{ getCajeroNombre(tx.cajero_id) }}</p>
          </div>
          <div class="tx-right">
            <p class="tx-total">${{ tx.total }}</p>
            <p class="tx-method" :class="tx.metodo_pago">{{ formatMethod(tx.metodo_pago) }}</p>
          </div>
        </div>
      </div>
    </div>

    <p v-else-if="user" class="no-transactions">Este socio aún no tiene transacciones registradas.</p>
  </div>
</template>

<script>
/** @type {import('vue').ComponentOptions} */
import axios from "axios";
export default {
  data() {
    return {
      user: null,
      transactions: [],
      usersMap: {},
      editMode: false
    };
  },
  computed: {
    filteredTransactions() {
      if (!this.user) return [];
      return this.transactions
        .filter(tx => tx.user_id === this.user.id || tx.cliente_id === this.user.id)
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 10);
    }
  },
  async created() {
    const id = this.$route.params.id;
    const token = localStorage.getItem("auth_token");

    const [userRes, salesRes, usersRes] = await Promise.all([
      axios.get(`${import.meta.env.VITE_APP_URL}/api/users/${id}`, {
        headers: { Authorization: `Bearer ${token}` }
      }),
      axios.get(`${import.meta.env.VITE_APP_URL}/api/sales`, {
        headers: { Authorization: `Bearer ${token}` }
      }),
      axios.get(`${import.meta.env.VITE_APP_URL}/api/users`, {
        headers: { Authorization: `Bearer ${token}` }
      })
    ]);

    this.user = userRes.data;
    this.transactions = salesRes.data;
    this.usersMap = Object.fromEntries(usersRes.data.map(u => [u.id, u]));
  },
  methods: {
    getCajeroNombre(id) {
      const u = this.usersMap[id];
      return u ? u.nombre : "Desconocido";
    },
    async guardarCambios() {
      const token = localStorage.getItem("auth_token");
      const payload = { ...this.user, admin: Number(this.user.admin) };
      await axios.put(
        `${import.meta.env.VITE_APP_URL}/api/users/${this.user.id}`,
        payload,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      alert("Cambios guardados correctamente");
      this.editMode = false;
    },
    formatDate(date) {
      return new Date(date).toLocaleString("es-PR", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      });
    },
    formatMethod(method) {
      return method === "athmovil" ? "ATH Móvil" : "Efectivo";
    }
  }
};
</script>

<style scoped>
.user-detail {
  min-height: 100vh;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  color: #fff;
  padding: 1.2rem;
  font-family: "Inter", sans-serif;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.header {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: 100%;
  max-width: 900px;
  margin-bottom: 1.5rem;
  gap: 0.8rem;
}

.header h2 {
  font-size: 1.6rem;
  color: #9dd86a;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.back-btn {
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 8px;
  padding: 0.6rem 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  transition: 0.25s;
  width: 100%;
  justify-content: center;
}

.back-btn:hover {
  background: rgba(255, 255, 255, 0.25);
}

.user-card,
.transactions-card {
  background: rgba(255, 255, 255, 0.07);
  border-radius: 14px;
  border-left: 5px solid #9dd86a;
  padding: 1.4rem;
  width: 100%;
  max-width: 900px;
  margin-bottom: 2rem;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
}

.info-field {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.7rem 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  flex-wrap: wrap;
  gap: 0.4rem;
}

.label {
  color: #9dd86a;
  font-weight: 600;
  font-size: 0.95rem;
}

.balance {
  font-weight: bold;
  font-size: 1.2rem;
}

.admin-status {
  font-weight: 600;
  padding: 0.3rem 0.7rem;
  border-radius: 8px;
}

.admin-status.on {
  background: rgba(157, 216, 106, 0.2);
  color: #9dd86a;
}

.admin-status.off {
  background: rgba(255, 255, 255, 0.15);
  color: #ccc;
}

.actions {
  margin-top: 1.5rem;
  display: flex;
  justify-content: flex-start;
  width: 100%;
}

.edit-btn {
  background: rgba(157, 216, 106, 0.12);
  border: 1px solid rgba(157, 216, 106, 0.4);
  color: #9dd86a;
  border-radius: 10px;
  padding: 0.9rem 1.4rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  transition: 0.35s;
  min-width: 180px;
}

.edit-btn:hover {
  background: rgba(157, 216, 106, 0.25);
  transform: translateY(-1px);
}

.edit-btn.cancelar {
  background: rgba(255, 80, 80, 0.12);
  border-color: rgba(255, 99, 99, 0.4);
  color: #ff8b8b;
}

.save-btn {
  margin-top: 1.5rem;
  width: 100%;
  background: linear-gradient(135deg, #9dd86a, #7ac75d);
  color: #0f2027;
  border: none;
  border-radius: 10px;
  padding: 1rem 1.5rem;
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: 0.3px;
  cursor: pointer;
  transition: 0.35s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  box-shadow: 0 5px 20px rgba(157, 216, 106, 0.25);
}

.edit-section {
  margin-top: 1.5rem;
  background: rgba(0, 0, 0, 0.35);
  padding: 1.2rem;
  border-radius: 10px;
}

.form-grid {
  display: flex;
  flex-direction: column;
  gap: 0.9rem;
}

.form-grid input {
  width: 100%;
  padding: 0.8rem;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  font-size: 1rem;
}

.admin-toggle {
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #9dd86a;
  font-weight: 600;
}

.switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 26px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #555;
  transition: 0.4s;
  border-radius: 26px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #9dd86a;
}

input:checked + .slider:before {
  transform: translateX(22px);
}

.transactions-card h3 {
  color: #9dd86a;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  font-size: 1.2rem;
}

.transaction-item {
  background: rgba(0, 0, 0, 0.25);
  border-radius: 10px;
  padding: 1rem;
  margin-bottom: 0.8rem;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.6rem;
}

.tx-id {
  font-weight: 600;
  color: #fff;
}

.tx-date {
  font-size: 0.9rem;
  color: #ccc;
}

.tx-cajero {
  font-size: 0.9rem;
  color: #9dd86a;
  font-weight: 600;
}

.tx-total {
  font-weight: bold;
  font-size: 1.1rem;
}

.tx-method.athmovil {
  color: #00bcd4;
}

.tx-method.efectivo {
  color: #4caf50;
}

@media (min-width: 768px) {
  .header {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }

  .back-btn {
    width: auto;
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
  }

  .user-card,
  .transactions-card {
    padding: 2rem;
  }
}
</style>
