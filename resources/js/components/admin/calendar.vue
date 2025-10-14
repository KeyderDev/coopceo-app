<template>
  <div class="calendar-root">
    <header class="cal-header">
      <div class="nav">
        <button @click="prevMonth">‹</button>
        <div class="month-title">{{ monthYear }}</div>
        <button @click="nextMonth">›</button>
      </div>
      <div class="actions">
        <button class="today-btn" @click="goToday">Hoy</button>
      </div>
    </header>

    <div class="weekdays">
      <div v-for="d in weekDays" :key="d" class="weekday">{{ d }}</div>
    </div>

    <div class="days-grid">
      <div
        v-for="cell in monthGrid"
        :key="cell.key"
        :class="['day-cell', { 'other-month': cell.other, 'today': cell.isToday }]"
        @click="openNote(cell.date)"
      >
        <div class="day-number">{{ cell.day }}</div>

        <!-- 🔹 Mostrar mini-notas en la celda -->
        <div v-if="hasNotes(cell.date)" class="note-preview">
          <div v-for="(n, i) in getShortNotes(cell.date)" :key="i">{{ n }}</div>
        </div>

        <div class="dots">
          <span v-if="hasNotes(cell.date)" class="dot" v-for="n in notesCount(cell.date)" :key="n"></span>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="modal.show" class="modal-backdrop" @click.self="closeModal">
      <div class="modal-card">
        <h3>{{ modal.title }}</h3>
        <div class="modal-date">{{ formatFullDate(modal.date) }}</div>

        <!-- Lista de notas -->
<div class="note-list">
  <div v-for="(note, idx) in modal.list" :key="note.id" class="note-item">
    <span>{{ note.note }}</span>
    <button @click="deleteSingleNote(note.id)">❌</button>
  </div>
</div>


        <!-- Nueva nota -->
        <textarea v-model="modal.text" placeholder="Escribe una nueva nota..."></textarea>

        <div class="modal-actions">
          <div class="spacer"></div>
          <button class="btn" @click="saveModal">Agregar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const today = new Date()
const currentYear = ref(today.getFullYear())
const currentMonth = ref(today.getMonth())
const authToken = localStorage.getItem('auth_token')
const notes = ref({}) // cada fecha será un array de objetos {id, note}

const monthYear = computed(() => {
  const d = new Date(currentYear.value, currentMonth.value, 1)
  return d.toLocaleString(undefined, { month: 'long', year: 'numeric' })
})

const weekDays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']

const monthGrid = computed(() => {
  const year = currentYear.value
  const month = currentMonth.value
  const start = new Date(year, month, 1)
  let startWeekday = (start.getDay() + 6) % 7
  const lead = startWeekday
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const prevMonthEnd = new Date(year, month, 0).getDate()

  const grid = []
  for (let i = 0; i < 42; i++) {
    const dayIndex = i - lead + 1
    let cellDate
    let other = false
    if (dayIndex <= 0) {
      cellDate = new Date(year, month - 1, prevMonthEnd + dayIndex)
      other = true
    } else if (dayIndex > daysInMonth) {
      cellDate = new Date(year, month + 1, dayIndex - daysInMonth)
      other = true
    } else {
      cellDate = new Date(year, month, dayIndex)
    }

    const isToday = sameDay(cellDate, today)
    grid.push({ key: cellDate.toISOString(), date: cellDate, day: cellDate.getDate(), other, isToday })
  }
  return grid
})

function sameDay(a, b) {
  return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate()
}

function formatKey(date) {
  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  const d = String(date.getDate()).padStart(2, '0')
  return `${y}-${m}-${d}`
}

function hasNotes(date) {
  const key = formatKey(date)
  return Array.isArray(notes.value[key]) && notes.value[key].length > 0
}

function notesCount(date) {
  const key = formatKey(date)
  return notes.value[key] ? notes.value[key].length : 0
}

function getShortNotes(date) {
  const key = formatKey(date)
  if (!notes.value[key]) return []
  return notes.value[key].map(n => n.note.length > 35 ? n.note.substring(0, 35) + '…' : n.note)
}

async function fetchNotes() {
  try {
    const res = await axios.get(`https://coopceo.ddns.net:8000/api/calendar-notes`, {
      headers: { Authorization: `Bearer ${authToken}` }
    })
    notes.value = {}
    res.data.forEach(n => {
      if (!notes.value[n.date]) notes.value[n.date] = []
      // Guardamos el objeto completo
      notes.value[n.date].push({ id: n.id, note: n.note })
    })
  } catch (error) {
    console.error('Error al cargar notas:', error)
  }
}

const modal = ref({ show: false, date: null, text: '', list: [], title: '' })

function openNote(date) {
  modal.value.show = true
  modal.value.date = date
  modal.value.title = 'Notas'
  const key = formatKey(date)
  modal.value.list = notes.value[key] ? [...notes.value[key]] : []
  modal.value.text = ''
}

function closeModal() { modal.value.show = false }

// saveModal
async function saveModal() {
  try {
    const key = formatKey(modal.value.date)
    if (!notes.value[key]) notes.value[key] = []

    if (modal.value.text.trim() !== '') {
      const res = await axios.post(`https://coopceo.ddns.net:8000/api/calendar-notes`, {
        date: key,
        note: modal.value.text.trim(),
      }, { headers: { Authorization: `Bearer ${authToken}` } })

      // Guardamos el objeto completo
      const newNote = { id: res.data.id, note: res.data.note }
      notes.value[key].push(newNote)
      modal.value.list.push(newNote)
      modal.value.text = ''
    }
  } catch (error) {
    console.error('Error al guardar nota:', error)
  }
}


// deleteSingleNote
async function deleteSingleNote(noteId) {
  try {
    const key = formatKey(modal.value.date)

    await axios.delete(`https://coopceo.ddns.net:8000/api/calendar-notes/${noteId}`, {
      headers: { Authorization: `Bearer ${authToken}` }
    })

    // Eliminamos del array local
    notes.value[key] = notes.value[key].filter(n => n.id !== noteId)
    modal.value.list = modal.value.list.filter(n => n.id !== noteId)
  } catch (error) {
    console.error('Error al eliminar nota:', error)
  }
}


function prevMonth() {
  if (currentMonth.value === 0) {
    currentMonth.value = 11
    currentYear.value -= 1
  } else {
    currentMonth.value -= 1
  }
}

function nextMonth() {
  if (currentMonth.value === 11) {
    currentMonth.value = 0
    currentYear.value += 1
  } else {
    currentMonth.value += 1
  }
}

function goToday() {
  currentYear.value = today.getFullYear()
  currentMonth.value = today.getMonth()
}

function formatFullDate(date) {
  return date.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
}

onMounted(() => {
  fetchNotes()
})
</script>


<style scoped>
:root {
  --primary: #97d569;
  --secondary: #03355c;
  --light-bg: #f9fafb;
  --white: #ffffff;
  --shadow: rgba(3, 53, 92, 0.15);
}

/* CONTENEDOR PRINCIPAL */
.calendar-root {
  width: 100%;
  min-height: 100vh;
  background: linear-gradient(180deg, var(--secondary) 0%, #022849 100%);
  color: var(--secondary);
  padding: 2rem 1rem 4rem 1rem;
  font-family: "Inter", system-ui, sans-serif;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* HEADER */
.cal-header {
  width: 100%;
  max-width: 1200px;
  background: var(--white);
  border-radius: 14px;
  box-shadow: 0 8px 20px var(--shadow);
  padding: 1rem 1.2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.nav {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.nav button {
  background: var(--primary);
  color: #03355c;
  border: none;
  border-radius: 10px;
  padding: 0.5rem 0.75rem;
  cursor: pointer;
  font-weight: 700;
  font-size: 1.1rem;
  transition: background 0.25s ease, transform 0.1s ease;
}
.nav button:hover {
  background: #8acb5e;
  transform: scale(1.05);
}

.month-title {
  text-transform: capitalize;
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--secondary);
  letter-spacing: 0.5px;
}

.today-btn {
  background: transparent;
  border: 2px solid var(--secondary);
  color: var(--secondary);
  font-weight: 600;
  padding: 0.45rem 0.8rem;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.25s ease;
}
.today-btn:hover {
  background: var(--secondary);
  color: white;
}

/* DÍAS Y SEMANAS */
.weekdays {
  width: 100%;
  max-width: 1200px;
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  text-align: center;
  font-weight: 700;
  color: #97d569;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 0.6rem 0;
  margin-bottom: 0.5rem;
  backdrop-filter: blur(6px);
}

.days-grid {
  width: 100%;
  max-width: 1200px;
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 10px;
}

/* CELDAS DEL CALENDARIO */
.day-cell {
  min-height: 100px;
  background: var(--white);
  border-radius: 12px;
  box-shadow: 0 3px 8px var(--shadow);
  padding: 0.7rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: all 0.25s ease;
  cursor: pointer;
  position: relative;
}
.day-cell:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 16px var(--shadow);
}
.day-cell.other-month {
  opacity: 0.4;
}
.day-cell.today {
  outline: 3px solid var(--primary);
  outline-offset: 2px;
}

.day-number {
  font-weight: 700;
  color: var(--secondary);
  font-size: 1.1rem;
}

.dots {
  display: flex;
  gap: 5px;
  justify-content: flex-start;
  margin-top: auto;
}

.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--primary);
  box-shadow: 0 0 6px var(--primary);
}

/* MODAL */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(3, 53, 92, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(5px);
  z-index: 50;
}

.modal-card {
  width: 100%;
  max-width: 520px;
  background: var(--white);
  border-radius: 14px;
  box-shadow: 0 10px 40px var(--shadow);
  padding: 1.5rem;
  animation: fadeIn 0.25s ease;
}

.note-preview {
  margin-top: 0.3rem;
  font-size: 0.75rem;
  color: #555;
  background: rgba(151, 213, 105, 0.1);
  border-left: 3px solid var(--primary);
  padding: 2px 5px;
  border-radius: 5px;
  max-height: 45px;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.1;
}

.modal-card h3 {
  color: var(--secondary);
  margin: 0 0 0.4rem 0;
  font-size: 1.2rem;
}

.modal-date {
  color: gray;
  font-size: 0.9rem;
  margin-bottom: 1rem;
}


.note-list {
  max-height: 150px;
  overflow-y: auto;
  margin-bottom: 0.5rem;
}
.note-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba(151, 213, 105, 0.1);
  border-left: 3px solid var(--primary);
  padding: 3px 5px;
  border-radius: 5px;
  margin-bottom: 3px;
}
.note-item button {
  background: transparent;
  border: none;
  cursor: pointer;
  color: red;
  font-weight: bold;
}

.modal-card textarea {
  width: 100%;
  min-height: 150px;
  padding: 0.6rem;
  border-radius: 10px;
  border: 1px solid #dce3ea;
  font-size: 1rem;
  resize: vertical;
  color: #333;
}

.modal-actions {
  display: flex;
  align-items: center;
  margin-top: 1rem;
  gap: 0.6rem;
}

.btn {
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 10px;
  padding: 0.5rem 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s ease;
}
.btn:hover {
  background: #8acb5e;
}

.btn.secondary {
  background: transparent;
  border: 2px solid var(--secondary);
  color: var(--secondary);
}
.btn.secondary:hover {
  background: var(--secondary);
  color: white;
}

.spacer {
  flex: 1;
}

/* ANIMACIÓN */
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

/* RESPONSIVE */
@media (max-width: 900px) {
  .days-grid {
    gap: 8px;
  }
  .day-cell {
    min-height: 90px;
  }
}

@media (max-width: 700px) {
  .calendar-root {
    padding: 1rem;
  }
  .days-grid {
    gap: 6px;
  }
  .day-cell {
    min-height: 80px;
    padding: 0.5rem;
  }
  .month-title {
    font-size: 1.1rem;
  }
}

@media (max-width: 500px) {
  .weekdays {
    font-size: 0.85rem;
  }
  .day-cell {
    min-height: 70px;
  }
  .modal-card {
    padding: 1rem;
  }
}
</style>
