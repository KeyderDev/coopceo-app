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
  max-width: 700px;
  width: 90%;
  margin: 2rem auto;
  padding: 2rem;
  background: rgba(30, 30, 30, 0.9);
  color: #f5f7fa;
  border-radius: 16px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
  font-family: "Inter", sans-serif;
  backdrop-filter: blur(10px);
}

/* 🧾 Títulos */
h2 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #9dd86a;
  font-size: 1.8rem;
  border-bottom: 2px solid rgba(157, 216, 106, 0.3);
  padding-bottom: 0.4rem;
  text-shadow: 0 0 6px rgba(157, 216, 106, 0.3);
}

h3 {
  margin-top: 1.5rem;
  margin-bottom: 0.8rem;
  color: #9dd86a;
  font-size: 1.2rem;
  border-bottom: 1px solid rgba(157, 216, 106, 0.3);
  padding-bottom: 4px;
}

/* 🧮 Campos */
.field {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 0.7rem 0;
}

.field label {
  font-weight: 600;
  font-size: 1rem;
  color: #e0e0e0;
}

.field input {
  width: 110px;
  padding: 0.5rem 0.7rem;
  background: rgba(14, 17, 23, 0.95);
  color: #f5f7fa;
  border: 1px solid rgba(157, 216, 106, 0.3);
  border-radius: 8px;
  text-align: right;
  font-size: 0.95rem;
  transition: all 0.25s ease;
}

.field input:focus {
  outline: none;
  border-color: #9dd86a;
  box-shadow: 0 0 8px rgba(157, 216, 106, 0.4);
}

/* 💰 Totales */
.totals {
  margin-top: 2rem;
  padding: 1rem 1.2rem;
  background: rgba(25, 27, 31, 0.85);
  color: #f5f7fa;
  border-radius: 12px;
  border: 1px solid rgba(157, 216, 106, 0.2);
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
}

.totals p {
  display: flex;
  justify-content: space-between;
  margin: 0.6rem 0;
  font-weight: 600;
  font-size: 1rem;
}

.totals span {
  color: #b9f089;
}

.totals .negative {
  color: #ff4d4d;
}

/* 💾 Botón Guardar */
.btn-guardar {
  width: 100%;
  padding: 0.9rem;
  margin-top: 1.8rem;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
}

.btn-guardar:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-3px);
}

/* 🏠 Botón Volver */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 25px;
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  border: none;
  padding: 0.8rem 1.3rem;
  border-radius: 10px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
  transition: all 0.25s ease;
  z-index: 1000;
}

.btn-volver:hover {
  background: linear-gradient(135deg, #b9f089, #91d46d);
  transform: translateY(-3px);
}

/* 🌗 Scrollbar personalizada */
.cash-reconciliation::-webkit-scrollbar {
  width: 10px;
}
.cash-reconciliation::-webkit-scrollbar-thumb {
  background: rgba(157, 216, 106, 0.3);
  border-radius: 10px;
}
.cash-reconciliation::-webkit-scrollbar-thumb:hover {
  background: rgba(157, 216, 106, 0.6);
}
.cash-reconciliation::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

/* 📱 Responsive */
@media (max-width: 768px) {
  .cash-reconciliation {
    width: 95%;
    padding: 1.2rem;
  }

  h2 {
    font-size: 1.5rem;
  }

  .field input {
    width: 90px;
  }

  .btn-volver {
    bottom: 15px;
    right: 15px;
    font-size: 0.9rem;
    padding: 0.7rem 1rem;
  }
}
</style>

