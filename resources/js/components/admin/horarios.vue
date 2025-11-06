<template>
  <div class="schedule-wrapper">
    <div v-if="!authToken" class="alert">
      ⚠ No se encontró token de autenticación. Guarda el auth_token en localStorage.
    </div>

    <!-- 🧾 Formulario -->
    <section class="form-section">
      <h2>Asignar horario</h2>

      <div class="form-grid">
        <!-- 🔍 Buscar / seleccionar usuario -->
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

          <!-- Lista de coincidencias -->
          <ul
            v-if="showUserList && filteredUsers.length"
            class="user-results"
          >
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

        <div class="form-item">
          <label>Día</label>
          <select v-model="day">
            <option v-for="d in days" :key="d">{{ d }}</option>
          </select>
        </div>

        <div class="form-item">
          <label>Hora inicio</label>
          <input type="time" v-model="start_time" />
        </div>

        <div class="form-item">
          <label>Hora fin</label>
          <input type="time" v-model="end_time" />
        </div>
      </div>

      <button class="btn add" @click="addSchedule">Agregar horario</button>
    </section>

    <!-- 📊 Tabla -->
    <section class="table-section">
      <h2>Resumen del horario</h2>

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
    </section>

    <button class="btn send" @click="sendEmail">Enviar horarios por correo</button>

    <button class="btn-volver" @click="volverMenu">
      <i class="fa-solid fa-house"></i> Menú Principal
    </button>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      users: [],
      currentUser: '',
      day: 'LUNES',
      start_time: '',
      end_time: '',
      schedules: [],
      days: ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'],
      authToken: localStorage.getItem('auth_token') || '',
      userSearch: '',
      showUserList: false,
      selectedUserName: ''
    }
  },
  computed: {
    filteredUsers() {
      if (!this.userSearch.trim()) return this.users
      return this.users.filter(u =>
        (u.nombre || '')
          .toLowerCase()
          .includes(this.userSearch.toLowerCase())
      )
    },
    scheduleTable() {
      const table = {}
      this.schedules.forEach(s => {
        const user = this.users.find(u => u.id === s.user_id)
        if (!user) return
        if (!table[s.user_id]) {
          table[s.user_id] = { user: user.nombre }
          this.days.forEach(d => (table[s.user_id][d] = ''))
          table[s.user_id].totalHours = 0
        }
        table[s.user_id][s.day] = `${s.start_time} - ${s.end_time}`
        const [sh, sm] = s.start_time.split(':').map(Number)
        const [eh, em] = s.end_time.split(':').map(Number)
        table[s.user_id].totalHours += (eh + em / 60) - (sh + sm / 60)
      })
      return Object.values(table).map(u => {
        u.totalHours = u.totalHours.toFixed(2)
        return u
      })
    }
  },
  async created() {
    if (!this.authToken) return
    try {
      const res = await axios.get('/api/users', {
        headers: { Authorization: `Bearer ${this.authToken}` }
      })
      this.users = res.data
    } catch (err) {
      console.error(err)
      alert('No se pudieron cargar los usuarios.')
    }
  },
  methods: {
    volverMenu() {
      this.$router.push('/')
    },
    onUserSearchInput() {
      this.showUserList = true
      this.currentUser = '' // evitar que se quede un ID viejo
      this.selectedUserName = ''
    },
    hideUserList() {
      // pequeño delay para permitir el click en la opción
      setTimeout(() => {
        this.showUserList = false
      }, 150)
    },
    selectUser(user) {
      this.currentUser = user.id
      this.userSearch = user.nombre
      this.selectedUserName = user.nombre
      this.showUserList = false
    },
    addSchedule() {
      if (!this.currentUser || !this.start_time || !this.end_time) return
      const exists = this.schedules.find(
        s => s.user_id === this.currentUser && s.day === this.day
      )
      if (exists) {
        exists.start_time = this.start_time
        exists.end_time = this.end_time
      } else {
        this.schedules.push({
          user_id: this.currentUser,
          day: this.day,
          start_time: this.start_time,
          end_time: this.end_time
        })
      }
      this.day = 'LUNES'
      this.start_time = ''
      this.end_time = ''
    },
    async sendEmail() {
      if (!this.schedules.length) return
      const grouped = this.schedules.reduce((acc, s) => {
        const user = this.users.find(u => u.id === s.user_id)
        if (!user) return acc
        if (!acc[s.user_id]) acc[s.user_id] = { user, schedules: [] }
        acc[s.user_id].schedules.push({ ...s, name: user.nombre })
        return acc
      }, {})
      try {
        for (const key in grouped) {
          const { schedules } = grouped[key]
          await axios.post(
            '/api/schedules/send-email',
            { schedules },
            {
              headers: { Authorization: `Bearer ${this.authToken}` }
            }
          )
        }
        alert('Horarios enviados correctamente.')
      } catch (err) {
        console.error(err)
      }
    }
  }
}
</script>

<style scoped>
.schedule-wrapper {
  width: 100%;
  min-height: 100vh;
  background-color: #e0e0e0;
  box-sizing: border-box;
}

h1 {
  text-align: center;
  color: #03355c;
  font-size: 2.3rem;
  margin-bottom: 35px;
}

h2 {
  color: #03355c;
  font-size: 1.6rem;
  margin-bottom: 20px;
  border-bottom: 2px solid #97d569;
  padding-bottom: 5px;
}

.form-section {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  margin-bottom: 40px;
}

.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 30px;
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 0.8rem 1.3rem;
  border-radius: 12px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
  transition: all 0.25s ease;
  z-index: 1000;
}

.btn-volver:hover {
  background-color: #43a047;
  transform: translateY(-3px);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.form-item {
  display: flex;
  flex-direction: column;
  position: relative;
}

.form-item label {
  font-weight: 600;
  color: #03355c;
  margin-bottom: 5px;
}

.form-item select,
.form-item input {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 1rem;
  background: #fafafa;
  transition: all 0.2s;
}

.form-item select:focus,
.form-item input:focus {
  border-color: #97d569;
  background: #fff;
}

/* 🔍 Input de búsqueda */
.search-user-input {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 1rem;
  background: #fafafa;
  transition: all 0.2s;
}

.search-user-input:focus {
  border-color: #97d569;
  background: #fff;
}

/* Lista de resultados */
.user-search-wrapper {
  max-width: 100%;
}

.user-results {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  margin-top: 4px;
  background: #ffffff;
  border-radius: 8px;
  border: 1px solid #ddd;
  max-height: 220px;
  overflow-y: auto;
  z-index: 50;
  list-style: none;
  padding: 4px 0;
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
}

.user-result-item {
  padding: 8px 12px;
  cursor: pointer;
  font-size: 0.95rem;
}

.user-result-item:hover {
  background-color: #e6f2d9;
}

.selected-user-label {
  margin-top: 6px;
  font-size: 0.85rem;
  color: #555;
}

.table-section {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
}

.table-container {
  width: 100%;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 18px;
  font-size: 0.95rem;
  text-align: center;
}

table th,
table td {
  border: 1px solid #ccc;
  padding: 12px;
  white-space: nowrap;
}

table th {
  background-color: #03355c;
  color: #fff;
}

table tbody tr:nth-child(even) {
  background-color: #f3f8f2;
}

table tbody tr:hover {
  background-color: #e6f2d9;
}

.btn {
  display: inline-block;
  padding: 12px 20px;
  margin-top: 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}

.btn.add {
  background-color: #97d569;
  color: #fff;
}

.btn.add:hover {
  background-color: #82be54;
}

.btn.send {
  display: block;
  width: 100%;
  background-color: #03355c;
  color: #fff;
}

.btn.send:hover {
  background-color: #021f3d;
}

.alert {
  text-align: center;
  color: #b00020;
  background: #ffeaea;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 25px;
  font-weight: 600;
}

/* Responsivo */
@media (max-width: 768px) {
  .schedule-wrapper {
    padding: 25px 15px;
  }

  table {
    font-size: 0.85rem;
  }

  .btn.send {
    font-size: 1rem;
  }
}
</style>
