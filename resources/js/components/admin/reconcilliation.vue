<template>
  <div class="cash-reconciliation">
    <h2>Cash Reconciliation</h2>

    <div class="field">
      <label>Petty:</label>
      <input type="number" v-model.number="petty" />
    </div>

    <h3>Billetes</h3>
    <div class="field" v-for="bill in bills" :key="bill.value">
      <label>{{ bill.label }}:</label>
      <input type="number" v-model.number="bill.quantity" min="0" @focus="clearZero($event, bill)" />
    </div>

    <h3>Monedas</h3>
    <div class="field" v-for="coin in coins" :key="coin.value">
      <label>{{ coin.label }}:</label>
      <input type="number" v-model.number="coin.quantity" min="0" @focus="clearZero($event, coin)" />
    </div>

    <div class="totals">
      <!-- <p>Total contado: <span>{{ totalCounted.toFixed(2) }}</span></p> -->
      <p>Total ventas en efectivo: <span>{{ totalSalesCash.toFixed(2) }}</span></p>
      <p>Diferencia: <span :class="{ negative: difference < 0 }">{{ difference.toFixed(2) }}</span></p>
    </div>

    <button @click="saveReconciliation">Guardar cuadre</button>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      petty: 50, // dinero inicial en caja
      bills: [
        { label: '20', value: 20, quantity: 0 },
        { label: '10', value: 10, quantity: 0 },
        { label: '5', value: 5, quantity: 0 },
        { label: '1', value: 1, quantity: 0 },
      ],
      coins: [
        { label: '0.10', value: 0.10, quantity: 0 },
        { label: '0.05', value: 0.05, quantity: 0 },
        { label: '0.01', value: 0.01, quantity: 0 },
        { label: '0.25', value: 0.25, quantity: 0 },
      ],
      totalSalesCash: 0, // total de ventas en efectivo
    };
  },
  computed: {
    // Total de efectivo físico contado en caja
    totalCounted() {
      const totalBills = this.bills.reduce((sum, b) => sum + (b.quantity || 0) * b.value, 0);
      const totalCoins = this.coins.reduce((sum, c) => sum + (c.quantity || 0) * c.value, 0);
      return parseFloat((totalBills + totalCoins).toFixed(2));
    },
    // Diferencia real: efectivo contado - ventas en efectivo (petty ya se cuenta dentro del total físico)
    difference() {
      const expectedCash = this.petty + this.totalSalesCash;
      return parseFloat((this.totalCounted - expectedCash).toFixed(2));
    }


  },
  async created() {
    const today = new Date();
    const offset = today.getTimezoneOffset();
    today.setMinutes(today.getMinutes() - offset);
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const localDate = `${year}-${month}-${day}`;

    try {
      const response = await axios.get(`/api/sales-reconcilliation?date=${localDate}&payment=efectivo`);
      this.totalSalesCash = parseFloat(response.data.total_cash);
    } catch (error) {
      console.error('Error al obtener total de ventas en efectivo:', error);
      this.totalSalesCash = 0;
    }
  },
  methods: {
    clearZero(event, item) {
      if (item.quantity === 0) {
        item.quantity = null;
        event.target.value = '';
      }
    },
    async saveReconciliation() {
      const data = {
        petty: this.petty,
        bill_20: this.bills[0].quantity,
        bill_10: this.bills[1].quantity,
        bill_5: this.bills[2].quantity,
        bill_1: this.bills[3].quantity,
        coin_10: this.coins[0].quantity,
        coin_5: this.coins[1].quantity,
        coin_1: this.coins[2].quantity,
        coin_25: this.coins[3].quantity,
        total_counted: this.totalCounted,
        total_sales_cash: this.totalSalesCash,
        difference: this.difference
      };

      try {
        await axios.post('/api/cash-reconciliations', data);
        alert('Cuadre guardado correctamente!');
      } catch (error) {
        console.error('Error al guardar cuadre:', error);
        alert('Ocurrió un error al guardar el cuadre.');
      }
    }
  }
};
</script>




<style scoped>
.cash-reconciliation {
  max-width: 650px;
  width: 85%;
  margin: 30px auto;
  padding: 25px 30px;
  background-color: #043861;
  color: #ffffff;
  border-radius: 14px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  font-family: 'Arial', sans-serif;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #97d569;
  font-size: 24px;
}

h3 {
  margin-top: 20px;
  margin-bottom: 12px;
  color: #97d569;
  font-size: 18px;
  border-bottom: 1px solid #97d569;
  padding-bottom: 4px;
}

.field {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 10px 0;
}

.field label {
  font-weight: bold;
  font-size: 15px;
}

.field input {
  width: 100px;
  padding: 6px 10px;
  border: none;
  border-radius: 6px;
  text-align: right;
  font-size: 15px;
}

.totals {
  margin-top: 25px;
  padding: 15px;
  background-color: #97d569;
  color: #043861;
  border-radius: 10px;
}

.totals p {
  display: flex;
  justify-content: space-between;
  margin: 6px 0;
  font-weight: bold;
  font-size: 15px;
}

.totals .negative {
  color: #ff4d4d;
}

button {
  width: 100%;
  padding: 10px;
  margin-top: 25px;
  background-color: #97d569;
  color: #043861;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

button:hover {
  background-color: #81c25d;
}

@media (min-width: 700px) {
  .field-group {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px 25px;
  }
}
</style>
