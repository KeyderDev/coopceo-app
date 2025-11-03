<template>
  <div class="container">
    <h1>Crear Horario</h1>

    <div v-if="!authToken" class="alert">
      ⚠ No se encontró token de autenticación. Guarda el auth_token en localStorage.
    </div>

    <section class="section">
      <h2>Asignar horario</h2>

      <div class="form-row">
        <label>Usuario:</label>
        <select v-model="currentUser">
          <option disabled value="">--Selecciona un usuario--</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.nombre }}
          </option>
        </select>
      </div>

      <div class="form-row">
        <label>Día:</label>
        <select v-model="day">
          <option v-for="d in days" :key="d">{{ d }}</option>
        </select>
      </div>

      <div class="form-row">
        <label>Hora inicio:</label>
        <input type="time" v-model="start_time">
      </div>

      <div class="form-row">
        <label>Hora fin:</label>
        <input type="time" v-model="end_time">
      </div>

      <button class="btn" @click="addSchedule">Agregar horario</button>
    </section>

    <section class="section">
      <h2>Resumen del horario</h2>
      <table v-if="scheduleTable.length">
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
    </section>

    <button class="btn btn-send" @click="sendEmail">Enviar horarios por correo</button>
  </div>
</template>

<script>
import axios from 'axios';

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
      authToken: localStorage.getItem('auth_token') || ''
    }
  },
  computed: {
    scheduleTable() {
      const table = {};
      this.schedules.forEach(s => {
        const user = this.users.find(u => u.id === s.user_id);
        if (!user) return;
        if (!table[s.user_id]) {
          table[s.user_id] = { user: user.nombre };
          this.days.forEach(d => table[s.user_id][d] = '');
          table[s.user_id].totalHours = 0;
        }
        table[s.user_id][s.day] = `${s.start_time} - ${s.end_time}`;
        const [startH, startM] = s.start_time.split(':').map(Number);
        const [endH, endM] = s.end_time.split(':').map(Number);
        const hours = (endH + endM / 60) - (startH + startM / 60);
        table[s.user_id].totalHours += hours;
      });
      return Object.values(table).map(u => {
        u.totalHours = u.totalHours.toFixed(2);
        return u;
      });
    }
  },
  async created() {
    if (!this.authToken) return;
    try {
      const res = await axios.get('/api/users', {
        headers: { Authorization: `Bearer ${this.authToken}` }
      });
      this.users = res.data;
    } catch (err) {
      console.error('Error al obtener usuarios:', err.response ? err.response.data : err);
      alert('No se pudieron cargar los usuarios.');
    }
  },
  methods: {
    addSchedule() {
      if (!this.currentUser || !this.start_time || !this.end_time) return;
      const exists = this.schedules.find(s => s.user_id === this.currentUser && s.day === this.day);
      if (exists) {
        exists.start_time = this.start_time;
        exists.end_time = this.end_time;
      } else {
        this.schedules.push({
          user_id: this.currentUser,
          day: this.day,
          start_time: this.start_time,
          end_time: this.end_time
        });
      }
      this.day = 'LUNES';
      this.start_time = '';
      this.end_time = '';
    },
    async sendEmail() {
      if (!this.schedules.length) return;
      const schedulesByUser = this.schedules.reduce((acc, s) => {
        const user = this.users.find(u => u.id === s.user_id);
        if (!user) return acc;
        if (!acc[s.user_id]) acc[s.user_id] = { user, schedules: [] };
        acc[s.user_id].schedules.push({ ...s, name: user.nombre });
        return acc;
      }, {});
      try {
        for (const key in schedulesByUser) {
          const { schedules } = schedulesByUser[key];
          await axios.post('/api/schedules/send-email', { schedules }, {
            headers: { Authorization: `Bearer ${this.authToken}` }
          });
        }
        alert('Horarios enviados por correo a todos los usuarios con horarios.');
      } catch (err) {
        console.error('Error al enviar horarios:', err.response ? err.response.data : err);
      }
    }
  }
}
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 40px auto;
  padding: 25px 30px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f7f7f7;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}

h1 {
  text-align: center;
  color: #03355c;
  font-size: 2.2rem;
  margin-bottom: 25px;
}

h2 {
  color: #03355c;
  font-size: 1.5rem;
  margin-bottom: 15px;
  border-bottom: 2px solid #97d569;
  padding-bottom: 5px;
}

.section {
  margin-top: 25px;
  padding: 20px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.form-row {
  display: flex;
  align-items: center;
  margin: 12px 0;
  gap: 15px;
}

.form-row label {
  width: 140px;
  font-weight: bold;
  color: #03355c;
}

.form-row input,
.form-row select {
  flex: 1;
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 1rem;
}

.btn {
  padding: 10px 20px;
  margin-top: 15px;
  background-color: #97d569;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}

.btn:hover {
  background-color: #7ebd50;
}

.btn-send {
  display: block;
  width: 100%;
  margin-top: 25px;
  background-color: #03355c;
}

.btn-send:hover {
  background-color: #021f3d;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 18px;
  font-size: 0.95rem;
}

table th,
table td {
  border: 1px solid #ccc;
  padding: 10px;
  text-align: center;
  transition: background 0.3s;
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

.alert {
  color: #b00020;
  font-weight: bold;
  margin: 12px 0;
  text-align: center;
}

/* Responsivo */
@media (max-width: 768px) {
  .form-row {
    flex-direction: column;
    align-items: flex-start;
  }
  .form-row label {
    width: 100%;
    margin-bottom: 5px;
  }
}
</style>
