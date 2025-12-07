<template>
  <div class="db-wrapper">
    <h1 class="title">Database Information</h1>

    <div v-if="loading" class="loading">Loading database info...</div>

    <div v-else>
      <div class="db-card main-card">
        <p><strong>Database:</strong> {{ info.database }}</p>
        <p><strong>Total Size:</strong> {{ info.total_mb }} MB</p>
        <p><strong>Tables:</strong> {{ info.count }}</p>

        <button class="backup-btn" @click="openSuperadmin">
          📦 Create Backup
        </button>
      </div>

      <div class="db-card extra-card">
        <p><strong>Data Size:</strong> {{ info.total_data_mb }} MB</p>
        <p><strong>Index Size:</strong> {{ info.total_index_mb }} MB</p>
        <p><strong>Largest Table:</strong> {{ largestTable?.name }} ({{ largestTable?.size_mb }} MB)</p>
        <p><strong>Most Rows:</strong> {{ mostRows?.name }} ({{ mostRows?.table_rows }} rows)</p>
      </div>

      <h2 class="subtitle">Tables</h2>

      <table class="db-table">
        <thead>
          <tr>
            <th>Table</th>
            <th>Rows</th>
            <th>Engine</th>
            <th>Data (MB)</th>
            <th>Index (MB)</th>
            <th>Total (MB)</th>
            <th>Updated</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in info.tables" :key="t.name">
            <td>{{ t.name }}</td>
            <td>{{ t.table_rows }}</td>
            <td>{{ t.engine }}</td>
            <td>{{ t.data_mb }}</td>
            <td>{{ t.index_mb }}</td>
            <td>{{ t.size_mb }}</td>
            <td>{{ t.update_time || 'N/A' }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <SuperadminModal
      :visible="showSuperadmin"
      title="Código Superadmin requerido"
      @confirm="confirmBackup"
      @cancel="showSuperadmin = false"
    />
  </div>
</template>

<script>
import axios from "axios"
import SuperadminModal from "../global/superadmin.vue"

export default {
  name: "DatabasePage",
  components: { SuperadminModal },

  data() {
    return {
      loading: true,
      info: {},
      showSuperadmin: false
    }
  },

  computed: {
    largestTable() {
      if (!this.info.tables) return null
      return [...this.info.tables].sort((a, b) => b.size_mb - a.size_mb)[0]
    },
    mostRows() {
      if (!this.info.tables) return null
      return [...this.info.tables].sort((a, b) => b.table_rows - a.table_rows)[0]
    }
  },

  async created() {
    try {
      const res = await axios.get("/api/database/info", {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          "X-Coop-Code": localStorage.getItem("coop_code")
        }
      })
      this.info = res.data
    } catch (e) {}
    this.loading = false
  },

  methods: {
    openSuperadmin() {
      this.showSuperadmin = true
    },

    async confirmBackup() {
      this.showSuperadmin = false
      try {
        const response = await axios.get("/api/database/backup", {
          responseType: "blob",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            "X-Coop-Code": localStorage.getItem("coop_code")
          }
        })

        const url = window.URL.createObjectURL(new Blob([response.data]))
        const a = document.createElement("a")
        a.href = url
        a.download = "database_backup.sql"
        a.click()
        window.URL.revokeObjectURL(url)
      } catch (e) {}
    }
  }
}
</script>


<style scoped>
.db-wrapper {
  padding: 18px;
  color: white;
  background: #0f2027;
  min-height: 100vh;
}

.title {
  font-size: 26px;
  font-weight: 700;
  color: #97d569;
  margin-bottom: 20px;
}

.subtitle {
  margin-top: 30px;
  font-weight: 600;
  color: #97d569;
  font-size: 20px;
}

.loading {
  font-size: 17px;
  color: #97d569;
}

.db-card {
  background: #1e1e1e;
  padding: 16px 18px;
  border-radius: 16px;
  border-left: 4px solid #97d569;
  box-shadow: 0 0 12px rgba(0,0,0,0.45);
  margin-bottom: 22px;
  font-size: 14px;
}

.extra-card p {
  margin: 4px 0;
}

.backup-btn {
  background: #97d569;
  color: #0f2027;
  border: none;
  padding: 12px 18px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 10px;
  width: 100%;
}

.backup-btn:hover {
  background: #8ac15d;
}

.db-table {
  width: 100%;
  border-collapse: collapse;
  background: #1e1e1e;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 0 10px rgba(0,0,0,0.35);
  font-size: 14px;
}

.db-table th {
  background: #03355c;
  padding: 10px;
  text-align: left;
  color: white;
  font-weight: 600;
  white-space: nowrap;
}

.db-table td {
  padding: 10px;
  border-bottom: 1px solid #2c2c2c;
  color: #dddddd;
}

.db-table tbody tr:nth-child(odd) {
  background: #252525;
}

.db-table tbody tr:nth-child(even) {
  background: #1e1e1e;
}

.db-table tbody tr:hover {
  background: #2f2f2f;
}

/* ===== MOBILE OPTIMIZATION ===== */

@media (max-width: 768px) {
  .title {
    font-size: 24px;
    text-align: center;
  }

  .subtitle {
    text-align: center;
    font-size: 18px;
  }

  .db-card {
    font-size: 14px;
  }

  .db-table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }

  .db-table th,
  .db-table td {
    padding: 9px 12px;
    font-size: 13px;
  }
}

@media (max-width: 480px) {
  .db-wrapper {
    padding: 14px;
  }

  .title {
    font-size: 22px;
  }

  .db-card {
    padding: 14px;
    font-size: 13px;
  }

  .backup-btn {
    padding: 10px 14px;
    font-size: 14px;
  }

  .db-table th,
  .db-table td {
    padding: 8px 10px;
    font-size: 12px;
  }
}

</style>
