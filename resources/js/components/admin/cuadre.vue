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

    <h3>Rolled Currency</h3>
    <!-- Rollos: $10 (quarters), $5 (dimes), $2 (nickels), $0.50 (pennies) -->
    <div class="field" v-for="roll in rolledCurrency" :key="roll.value">
      <label>{{ roll.label }}:</label>
      <input type="number" v-model.number="roll.quantity" min="0" @focus="clearZero($event, roll)" />
    </div>

    <div class="totals">
      <p>Total ventas en efectivo: <span>{{ totalSalesCash.toFixed(2) }}</span></p>
      <p>Total contado (billetes + monedas + rollos): <span>{{ totalCounted.toFixed(2) }}</span></p>
      <p>Diferencia: <span :class="{ negative: difference < 0 }">{{ difference.toFixed(2) }}</span></p>
    </div>

    <button class="btn-guardar" @click="saveReconciliation">Guardar cuadre</button>
  </div>
      <button class="btn-volver" @click="volverMenu">
      <i class="fa-solid fa-house"></i> Menú Principal
    </button>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      petty: 50,
      bills: [
        { label: '$20', value: 20, quantity: 0 },
        { label: '$10', value: 10, quantity: 0 },
        { label: '$5', value: 5, quantity: 0 },
        { label: '$1', value: 1, quantity: 0 },
      ],
      coins: [
        { label: '$0.10', value: 0.10, quantity: 0 }, // dimes
        { label: '$0.05', value: 0.05, quantity: 0 }, // nickels
        { label: '$0.01', value: 0.01, quantity: 0 }, // pennies
        { label: '$0.25', value: 0.25, quantity: 0 }, // quarters
      ],
      rolledCurrency: [
        { label: 'Rollos de $10', value: 10.00, coinValue: 0.25, coinsPerRoll: 40, quantity: 0, key: 'roll_10' },
        { label: 'Rollos de $5', value: 5.00, coinValue: 0.10, coinsPerRoll: 50, quantity: 0, key: 'roll_5' },
        { label: 'Rollos de $2', value: 2.00, coinValue: 0.05, coinsPerRoll: 40, quantity: 0, key: 'roll_2' },
        { label: 'Rollos de $0.50', value: 0.50, coinValue: 0.01, coinsPerRoll: 50, quantity: 0, key: 'roll_0_50' },
      ],
      totalSalesCash: 0,
    };
  },
  computed: {
    totalBills() {
      return this.bills.reduce((sum, b) => sum + ((b.quantity || 0) * b.value), 0);
    },

    rolledCoinsAddition() {
      const additions = {};
      this.rolledCurrency.forEach(roll => {
        const qty = parseInt(roll.quantity || 0, 10);
        if (!qty) return;
        const coinVal = roll.coinValue;
        const extraCoins = qty * roll.coinsPerRoll;
        additions[coinVal] = (additions[coinVal] || 0) + extraCoins;
      });
      return additions;
    },

    totalCoins() {
      return this.coins.reduce((sum, c) => {
        const manualQty = parseInt(c.quantity || 0, 10);
        const extraCoins = this.rolledCoinsAddition[c.value] || 0;
        const totalQtyForCoin = manualQty + extraCoins;
        return sum + (totalQtyForCoin * c.value);
      }, 0);
    },

    totalCounted() {
      const total = this.totalBills + this.totalCoins;
      return parseFloat(total.toFixed(2));
    },

    difference() {
      const expectedCash = parseFloat((this.petty || 0) + (this.totalSalesCash || 0));
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
    volverMenu() {
      this.$router.push("/");
    },
    async saveReconciliation() {
      const rolledAdd = this.rolledCoinsAddition;

      const payload = {
        petty: this.petty,
        bill_20: this.bills[0].quantity,
        bill_10: this.bills[1].quantity,
        bill_5: this.bills[2].quantity,
        bill_1: this.bills[3].quantity,
        coin_10: (this.coins.find(c => c.value === 0.10).quantity || 0) + (rolledAdd[0.10] || 0),
        coin_5: (this.coins.find(c => c.value === 0.05).quantity || 0) + (rolledAdd[0.05] || 0),
        coin_1: (this.coins.find(c => c.value === 0.01).quantity || 0) + (rolledAdd[0.01] || 0),
        coin_25: (this.coins.find(c => c.value === 0.25).quantity || 0) + (rolledAdd[0.25] || 0),
        roll_10: this.rolledCurrency.find(r => r.key === 'roll_10').quantity,
        roll_5: this.rolledCurrency.find(r => r.key === 'roll_5').quantity,
        roll_2: this.rolledCurrency.find(r => r.key === 'roll_2').quantity,
        roll_0_50: this.rolledCurrency.find(r => r.key === 'roll_0_50').quantity,
        total_counted: this.totalCounted,
        total_sales_cash: this.totalSalesCash,
        difference: this.difference
      };

      try {
        await axios.post('/api/cash-reconciliations', payload);
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

.btn-volver {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 0.7rem 1.2rem;
  border-radius: 10px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
  transition: all 0.25s ease;
  z-index: 1000;
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

.btn-guardar {
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
