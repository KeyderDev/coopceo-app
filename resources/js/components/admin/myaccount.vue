<template>
  <div class="user-admin-wrapper">
    <h1 class="title">Mi Perfil Administrativo</h1>

    <div v-if="loading" class="loading">Cargando información...</div>

    <div v-else class="content">
      <div class="left">
        <div class="card user-card">
          <div class="avatar">
            <img v-if="user?.profile_picture" :src="imageUrl(user.profile_picture)" class="avatar-img" />

            <div v-else class="avatar-placeholder">
              {{ user?.nombre?.charAt(0) }}
            </div>
          </div>

          <h2 class="user-name">{{ user.nombre }} {{ user.apellido }}</h2>
          <p class="email">{{ user.email }}</p>

          <div class="info-list">
            <div class="field"><span>ID</span><strong>{{ user.id }}</strong></div>
            <div class="field"><span>Número de socio</span><strong>{{ user.numero_socio ?? 'N/A' }}</strong></div>
            <div class="field"><span>Teléfono</span><strong>{{ user.telefono ?? 'N/A' }}</strong></div>
            <div class="field"><span>Rol</span><strong>{{ user.admin ? 'Administrador' : 'Socio' }}</strong></div>
            <div class="field"><span>Creado</span><strong>{{ safeFormat(user.created_at) }}</strong></div>
            <div class="field"><span>Último inicio</span><strong>{{ safeFormat(user.last_login_at) }}</strong></div>
          </div>
        </div>
      </div>

      <div class="right">
        <div class="card stats-card">
          <h3 class="stats-title">Actividad del Usuario</h3>

          <div class="stats-row">
            <div class="stat">
              <div class="stat-value">{{ stats.total_sales }}</div>
              <div class="stat-label">Ventas</div>
            </div>

            <div class="stat">
              <div class="stat-value">$ {{ stats.total_money }}</div>
              <div class="stat-label">Generado</div>
            </div>

            <div class="stat">
              <div class="stat-value">{{ stats.total_products }}</div>
              <div class="stat-label">Productos</div>
            </div>
          </div>

          <div class="last-sale" v-if="stats.last_sale">
            <h4 class="last-title">Última Venta</h4>
            <div class="last-sale-info">
              <div class="info-row"><span>ID:</span><strong>{{ stats.last_sale.id }}</strong></div>
              <div class="info-row"><span>Total:</span><strong>$ {{ stats.last_sale.total }}</strong></div>
              <div class="info-row"><span>Método de Pago:</span><strong>{{ stats.last_sale.metodo_pago }}</strong></div>
              <div class="info-row"><span>Fecha:</span><strong>{{ safeFormat(stats.last_sale.created_at) }}</strong>
              </div>
              <div class="info-row"><span>Hora:</span><strong>{{ time(stats.last_sale.created_at) }}</strong></div>
            </div>
          </div>
        </div>

        <div class="card chart-card">
          <h3 class="stats-title">Categorías más vendidas</h3>
          <apexchart type="donut" height="300" :options="chartCategories.options" :series="chartCategories.series">
          </apexchart>
        </div>

        <div class="card chart-card">
          <h3 class="stats-title">Métodos de Pago</h3>
          <apexchart type="donut" height="300" :options="chartPayments.options" :series="chartPayments.series">
          </apexchart>
        </div>

      </div>
    </div>
  </div>
</template>



<script>
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'

export default {
  name: 'UserAdminPanel',
  components: { apexchart: VueApexCharts },
  data() {
    return {
      loading: true,
      user: {},
      stats: {
        total_sales: 0,
        total_money: 0,
        total_products: 0,
        last_sale: null
      },
      chartCategories: {
        series: [],
        options: {
          labels: [],
          colors: ['#d2691e', '#ed7d31', '#ffc000', '#70ad47', '#4472c4'],
          legend: {
            labels: { colors: '#e9f5ee' }
          },
          stroke: {
            show: true,
            width: 2,
            colors: ['#ffffff']
          },
          chart: {
            background: 'transparent'
          },
          dataLabels: {
            enabled: true,
            style: {
              colors: ['#fff']
            }
          }
        }
      },
      chartPayments: {
        series: [],
        options: {
          labels: [],
          colors: ['#ed7d31', '#70ad47', '#ffc000'],
          legend: {
            labels: { colors: '#e9f5ee' }
          },
          stroke: {
            show: true,
            width: 2,
            colors: ['#ffffff']
          },
          chart: {
            background: 'transparent'
          },
          dataLabels: {
            enabled: true,
            style: {
              colors: ['#fff']
            }
          }
        }
      }

    }
  },
  async created() {
    const token = localStorage.getItem('auth_token')
    const coop = localStorage.getItem('coop_code')

    const userRes = await axios.get('/api/user', {
      headers: { Authorization: `Bearer ${token}`, 'X-Coop-Code': coop }
    })
    this.user = userRes.data

    const salesRes = await axios.get('/api/sales', {
      headers: { Authorization: `Bearer ${token}`, 'X-Coop-Code': coop }
    })

    const sales = salesRes.data
    const mySales = sales.filter(s => s.cajero_id === this.user.id)

    const totalSales = mySales.length
    const totalMoney = mySales.reduce((acc, s) => acc + Number(s.total || 0), 0)
    const totalProducts = mySales.reduce((acc, s) => {
      return acc + s.products.reduce((pacc, p) => pacc + Number(p.pivot.quantity || 0), 0)
    }, 0)
    const lastSale = totalSales > 0 ? mySales[mySales.length - 1] : null

    this.stats = {
      total_sales: totalSales,
      total_money: totalMoney.toFixed(2),
      total_products: totalProducts,
      last_sale: lastSale
    }

    const categoryTotals = {}
    mySales.forEach(s => {
      s.products.forEach(p => {
        if (!categoryTotals[p.categoria]) categoryTotals[p.categoria] = 0
        categoryTotals[p.categoria] += Number(p.pivot.quantity)
      })
    })

    this.chartCategories.series = Object.values(categoryTotals)
    this.chartCategories.options.labels = Object.keys(categoryTotals)

    const paymentTotals = {}
    mySales.forEach(s => {
      if (!paymentTotals[s.metodo_pago]) paymentTotals[s.metodo_pago] = 0
      paymentTotals[s.metodo_pago]++
    })

    this.chartPayments.series = Object.values(paymentTotals)
    this.chartPayments.options.labels = Object.keys(paymentTotals)

    this.loading = false
  },
  methods: {
    safeFormat(date) {
      if (!date) return 'Sin registro'
      return new Date(date).toLocaleDateString()
    },
    time(date) {
      if (!date) return 'N/A'
      const d = new Date(date)
      return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    },
    imageUrl(path) {
      return `${import.meta.env.VITE_APP_URL}/storage/${path}`
    }

  }
}
</script>



<style scoped>
.user-admin-wrapper {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.title {
  font-size: 32px;
  font-weight: 700;
  color: #97d569;
  text-align: center;
}

.content {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 35px;
  width: 100%;
}

.left {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.right {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 25px;
}

.stats-card {
  grid-column: span 2;
}

.card {
  background: rgba(15, 32, 39, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(8px);
  border-radius: 20px;
  padding: 26px;
  color: #e9f5ee;
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.28);
}

.avatar {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  overflow: hidden;
  background: #97d569;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto 15px auto;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  font-size: 48px;
  font-weight: bold;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #0f2027;
}


.user-name {
  font-size: 24px;
  text-align: center;
}

.email {
  opacity: 0.75;
  text-align: center;
}

.info-list .field {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  font-size: 15px;
}

/* ===== ESTADÍSTICAS ===== */
.stats-title {
  font-size: 20px;
  margin-bottom: 16px;
  color: #97d569;
  font-weight: 600;
}

.stats-row {
  display: flex;
  gap: 18px;
  margin-bottom: 18px;
}

.stat {
  flex: 1;
  background: #12222d;
  padding: 20px;
  border-radius: 16px;
  text-align: center;
}

.stat-value {
  font-size: 30px;
  font-weight: 700;
  color: #97d569;
}

/* ===== ÚLTIMA VENTA ===== */
.last-sale {
  margin-top: 20px;
  background: #162a36;
  padding: 18px;
  border-radius: 14px;
}

.last-sale-info {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 6px 0;
  border-bottom: 1px dashed rgba(255, 255, 255, 0.12);
}

/* ===========================
   🔥🔥🔥 RESPONSIVE 🔥🔥🔥
   =========================== */

/* Tablets */
@media (max-width: 980px) {
  .content {
    grid-template-columns: 1fr;
    gap: 25px;
  }

  .right {
    grid-template-columns: 1fr 1fr;
  }

  .stats-card {
    grid-column: span 2;
  }

  .avatar {
    width: 90px;
    height: 90px;
    font-size: 40px;
  }
}

/* Teléfonos grandes */
@media (max-width: 640px) {
  .right {
    grid-template-columns: 1fr;
    gap: 18px;
  }

  .stats-card {
    grid-column: span 1;
  }

  .stats-row {
    flex-direction: column;
    gap: 12px;
  }

  .stat {
    padding: 16px;
  }

  .info-list .field {
    font-size: 14px;
  }

  .chart-card apexchart {
    width: 100%;
    transform: scale(0.95);
  }
}

/* Teléfonos pequeños (iPhone SE / 360px) */
@media (max-width: 480px) {
  .user-admin-wrapper {
    padding: 15px;
  }

  .title {
    font-size: 26px;
  }

  .avatar {
    width: 75px;
    height: 75px;
    font-size: 34px;
  }

  .user-name {
    font-size: 20px;
  }

  .email {
    font-size: 14px;
  }

  .stat-value {
    font-size: 24px;
  }

  .chart-card {
    padding: 15px;
  }

  apexchart {
    transform: scale(0.8);
  }

  .info-row {
    font-size: 14px;
  }
}

/* Teléfonos muy pequeños (≤ 360px) */
@media (max-width: 360px) {
  .avatar {
    width: 65px;
    height: 65px;
    font-size: 30px;
  }

  .card {
    padding: 18px;
  }

  apexchart {
    transform: scale(0.75);
  }
}
</style>
