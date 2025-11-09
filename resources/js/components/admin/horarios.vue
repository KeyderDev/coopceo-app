<template>
  <div class="schedule-wrapper">
    <div v-if="!authToken" class="alert">
      ⚠ No se encontró token de autenticación. Guarda el auth_token en localStorage.
    </div>

    <!-- 🧾 Formulario -->
    <section class="form-section">
      <h2><i class="fa-solid fa-clock"></i> Asignar horario</h2>

      <div class="form-grid">
        <!-- 🔍 Buscar usuario -->
        <div class="form-item user-search-wrapper">
          <label>Usuario</label>
          <input
            type="text"
            v-model="userSearch"
            placeholder="Escribe un nombre..."
            class="search-user-input"
            @focus="showUserList = true"
            @input="onUserSearchInput"
            @blur="hideUserList"
          />

          <ul v-if="showUserList && filteredUsers.length" class="user-results">
            <li
              v-for="user in filteredUsers"
              :key="user.id"
              class="user-result-item"
              @mousedown.prevent="selectUser(user)"
            >
              {{ user.nombre }}
            </li>
          </ul>

          <small v-if="selectedUserName" class="selected-user-label">
            Seleccionado: <strong>{{ selectedUserName }}</strong>
          </small>
        </div>

        <!-- Día -->
        <div class="form-item">
          <label>Día</label>
          <select v-model="day" class="day-select">
            <option v-for="d in days" :key="d">{{ d }}</option>
          </select>
        </div>

        <!-- Hora inicio -->
        <div class="form-item">
          <label>Hora inicio</label>
          <input type="time" v-model="start_time" />
        </div>

        <!-- Hora fin -->
        <div class="form-item">
          <label>Hora fin</label>
          <input type="time" v-model="end_time" />
        </div>
      </div>

      <button class="btn add" @click="addSchedule">
        <i class="fa-solid fa-plus"></i> Agregar horario
      </button>
    </section>

    <!-- 📊 Tabla -->
    <section class="table-section">
      <h2><i class="fa-solid fa-table"></i> Resumen del horario</h2>

      <div class="table-container" v-if="scheduleTable.length">
        <table>
          <thead>
            <tr>
              <th>Usuario</th>
              <th>LUNES</th>
              <th>MARTES</th>
              <th>MIÉRCOLES</th>
              <th>JUEVES</th>
              <th>VIERNES</th>
              <th>SÁBADO</th>
              <th>DOMINGO</th>
              <th>Total horas</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in scheduleTable" :key="u.user">
              <td>{{ u.user }}</td>
              <td>{{ u.LUNES }}</td>
              <td>{{ u.MARTES }}</td>
              <td>{{ u.MIERCOLES }}</td>
              <td>{{ u.JUEVES }}</td>
              <td>{{ u.VIERNES }}</td>
              <td>{{ u.SABADO }}</td>
              <td>{{ u.DOMINGO }}</td>
              <td>{{ u.totalHours }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-else class="no-schedules">No hay horarios registrados.</p>
    </section>

    <button class="btn send" @click="sendEmail">
      <i class="fa-solid fa-paper-plane"></i> Enviar horarios por correo
    </button>

    <button class="btn-volver" @click="volverMenu">
      <i class="fa-solid fa-house"></i> Menú Principal
    </button>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      users: [],
      currentUser: "",
      day: "LUNES",
      start_time: "",
      end_time: "",
      schedules: [],
      days: [
        "LUNES",
        "MARTES",
        "MIERCOLES",
        "JUEVES",
        "VIERNES",
        "SABADO",
        "DOMINGO",
      ],
      authToken: localStorage.getItem("auth_token") || "",
      userSearch: "",
      showUserList: false,
      selectedUserName: "",
    };
  },
  computed: {
    filteredUsers() {
      if (!this.userSearch.trim()) return this.users;
      return this.users.filter((u) =>
        (u.nombre || "").toLowerCase().includes(this.userSearch.toLowerCase())
      );
    },
    scheduleTable() {
      const table = {};
      this.schedules.forEach((s) => {
        const user = this.users.find((u) => u.id === s.user_id);
        if (!user) return;
        if (!table[s.user_id]) {
          table[s.user_id] = { user: user.nombre };
          this.days.forEach((d) => (table[s.user_id][d] = ""));
          table[s.user_id].totalHours = 0;
        }
        table[s.user_id][s.day] = `${s.start_time} - ${s.end_time}`;
        const [sh, sm] = s.start_time.split(":").map(Number);
        const [eh, em] = s.end_time.split(":").map(Number);
        table[s.user_id].totalHours += eh + em / 60 - (sh + sm / 60);
      });
      return Object.values(table).map((u) => {
        u.totalHours = u.totalHours.toFixed(2);
        return u;
      });
    },
  },
  async created() {
    if (!this.authToken) return;
    try {
      const res = await axios.get("/api/users", {
        headers: { Authorization: `Bearer ${this.authToken}` },
      });
      this.users = res.data;
    } catch (err) {
      console.error(err);
      alert("No se pudieron cargar los usuarios.");
    }
  },
  methods: {
    volverMenu() {
      this.$router.push("/");
    },
    onUserSearchInput() {
      this.showUserList = true;
      this.currentUser = "";
      this.selectedUserName = "";
    },
    hideUserList() {
      setTimeout(() => {
        this.showUserList = false;
      }, 150);
    },
    selectUser(user) {
      this.currentUser = user.id;
      this.userSearch = user.nombre;
      this.selectedUserName = user.nombre;
      this.showUserList = false;
    },
    addSchedule() {
      if (!this.currentUser || !this.start_time || !this.end_time) return;
      const exists = this.schedules.find(
        (s) => s.user_id === this.currentUser && s.day === this.day
      );
      if (exists) {
        exists.start_time = this.start_time;
        exists.end_time = this.end_time;
      } else {
        this.schedules.push({
          user_id: this.currentUser,
          day: this.day,
          start_time: this.start_time,
          end_time: this.end_time,
        });
      }
      this.day = "LUNES";
      this.start_time = "";
      this.end_time = "";
    },
    async sendEmail() {
      if (!this.schedules.length) return;
      const grouped = this.schedules.reduce((acc, s) => {
        const user = this.users.find((u) => u.id === s.user_id);
        if (!user) return acc;
        if (!acc[s.user_id]) acc[s.user_id] = { user, schedules: [] };
        acc[s.user_id].schedules.push({ ...s, name: user.nombre });
        return acc;
      }, {});
      try {
        for (const key in grouped) {
          const { schedules } = grouped[key];
          await axios.post(
            "/api/schedules/send-email",
            { schedules },
            { headers: { Authorization: `Bearer ${this.authToken}` } }
          );
        }
        alert("Horarios enviados correctamente.");
      } catch (err) {
        console.error(err);
      }
    },
  },
};
</script>

<style scoped>
.schedule-wrapper {
  width: 100%;
  min-height: 100vh;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  color: #fff;
  box-sizing: border-box;
  padding: 2rem 1.5rem 6rem;
  font-family: "Inter", sans-serif;
}

/* 🧾 Formulario */
.form-section {
  background: rgba(25, 27, 31, 0.9);
  border: 1px solid rgba(157, 216, 106, 0.15);
  border-radius: 20px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}

.form-section h2 {
  color: #9dd86a;
  border-bottom: 1px solid rgba(157, 216, 106, 0.3);
  padding-bottom: 0.6rem;
  margin-bottom: 1.5rem;
  text-align: center;
}

/* Inputs */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

label {
  font-weight: 600;
  margin-bottom: 0.4rem;
  color: #9dd86a;
}

input,
select {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(157, 216, 106, 0.3);
  border-radius: 8px;
  padding: 0.8rem;
  color: #e8ffe1;
  width: 100%;
  transition: all 0.3s;
}

select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-color: rgba(255, 255, 255, 0.08);
  color: #e8ffe1;
}

option {
  background: #1a1f23;
  color: #e8ffe1;
}

select:hover {
  background: rgba(157, 216, 106, 0.15);
}

input:focus,
select:focus {
  border-color: #9dd86a;
  box-shadow: 0 0 6px rgba(157, 216, 106, 0.5);
  outline: none;
}

/* Lista desplegable */
.user-search-wrapper {
  position: relative;
}

.user-results {
  position: absolute;
  top: 105%;
  left: 0;
  right: 0;
  background: rgba(25, 27, 31, 0.95);
  border: 1px solid rgba(157, 216, 106, 0.2);
  border-radius: 10px;
  max-height: 200px;
  overflow-y: auto;
  list-style: none;
  padding: 4px 0;
  z-index: 10;
}

.user-result-item {
  padding: 10px 12px;
  cursor: pointer;
}

.user-result-item:hover {
  background: rgba(157, 216, 106, 0.15);
}

/* === Botones === */
.btn {
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn.add,
.btn.send {
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  width: 100%;
  margin-top: 1.5rem;
}

.btn.add:hover,
.btn.send:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-2px);
}

/* === Tabla === */
.table-section {
  background: rgba(25, 27, 31, 0.9);
  border-radius: 20px;
  border: 1px solid rgba(157, 216, 106, 0.15);
  padding: 2rem;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
  overflow-x: auto;
}

.table-section h2 {
  color: #9dd86a;
  margin-bottom: 1rem;
  text-align: center;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 0.8rem;
  border: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
}

th {
  background: rgba(157, 216, 106, 0.15);
  color: #9dd86a;
}

.no-schedules {
  text-align: center;
  color: #ccc;
  font-style: italic;
}

/* === Botón volver === */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 25px;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  border: none;
  padding: 0.9rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
  transition: all 0.3s ease;
  z-index: 1000;
}

.btn-volver:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-3px);
}

/* ⚠ Alerta */
.alert {
  background: rgba(255, 255, 255, 0.1);
  color: #ff8a8a;
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 1rem;
  border-radius: 10px;
  text-align: center;
  margin-bottom: 1.5rem;
}

/* 📱 Responsive */
@media (max-width: 768px) {
  .schedule-wrapper {
    padding: 1rem;
  }

  table {
    font-size: 0.85rem;
  }

  .btn-volver {
    position: static;
    width: 100%;
    margin-top: 2rem;
  }
}
</style>
