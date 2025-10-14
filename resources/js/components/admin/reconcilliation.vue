<template>
  <div class="cash-reconciliation">
    <div v-if="showPasswordModal" class="modal-overlay">
      <div class="modal">
        <h3>🔒 Acceso restringido</h3>
        <p>Por favor, ingresa la contraseña para continuar:</p>
        <input
          type="password"
          v-model="enteredPassword"
          placeholder="Contraseña"
          @keyup.enter="checkPassword"
        />
        <div class="modal-buttons">
          <button @click="checkPassword">Entrar</button>
        </div>
        <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
      </div>
    </div>

    <div v-if="!showPasswordModal">
      <h2>Cash Reconciliation</h2>

      <div class="field">
        <label>Petty:</label>
        <input type="number" v-model.number="petty" />
      </div>

      <h3>Billetes</h3>
      <div class="field" v-for="bill in bills" :key="bill.value">
        <label>{{ bill.label }}:</label>
        <input
          type="number"
          v-model.number="bill.quantity"
          min="0"
          @focus="clearZero($event, bill)"
        />
      </div>

      <h3>Monedas</h3>
      <div class="field" v-for="coin in coins" :key="coin.value">
        <label>{{ coin.label }}:</label>
        <input
          type="number"
          v-model.number="coin.quantity"
          min="0"
          @focus="clearZero($event, coin)"
        />
      </div>

      <div class="totals">
        <p>Total ventas en efectivo: <span>{{ totalSalesCash.toFixed(2) }}</span></p>
        <p>
          Diferencia:
          <span :class="{ negative: difference < 0 }">
            {{ difference.toFixed(2) }}
          </span>
        </p>
      </div>

      <button @click="saveReconciliation">Guardar cuadre</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      petty: 50,
      bills: [
        { label: "$20", value: 20, quantity: 0 },
        { label: "$10", value: 10, quantity: 0 },
        { label: "$5", value: 5, quantity: 0 },
        { label: "$1", value: 1, quantity: 0 },
      ],
      coins: [
        { label: "$0.10", value: 0.1, quantity: 0 },
        { label: "$0.05", value: 0.05, quantity: 0 },
        { label: "$0.01", value: 0.01, quantity: 0 },
        { label: "$0.25", value: 0.25, quantity: 0 },
      ],
      totalSalesCash: 0,
      showPasswordModal: true,
      enteredPassword: "",
      correctPassword: "0000superadmin", 
      errorMessage: "",
    };
  },
  computed: {
    totalCounted() {
      const totalBills = this.bills.reduce(
        (sum, b) => sum + (b.quantity || 0) * b.value,
        0
      );
      const totalCoins = this.coins.reduce(
        (sum, c) => sum + (c.quantity || 0) * c.value,
        0
      );
      return parseFloat((totalBills + totalCoins).toFixed(2));
    },
    difference() {
      const expectedCash = this.petty + this.totalSalesCash;
      return parseFloat((this.totalCounted - expectedCash).toFixed(2));
    },
  },
  async created() {
    const today = new Date();
    const offset = today.getTimezoneOffset();
    today.setMinutes(today.getMinutes() - offset);
    const localDate = today.toISOString().split("T")[0];

    try {
      const response = await axios.get(
        `/api/sales-reconcilliation?date=${localDate}&payment=efectivo`
      );
      this.totalSalesCash = parseFloat(response.data.total_cash);
    } catch (error) {
      console.error("Error al obtener total de ventas en efectivo:", error);
      this.totalSalesCash = 0;
    }
  },
  methods: {
    clearZero(event, item) {
      if (item.quantity === 0) {
        item.quantity = null;
        event.target.value = "";
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
        difference: this.difference,
      };

      try {
        await axios.post("/api/cash-reconciliations", data);
        alert("Cuadre guardado correctamente!");
      } catch (error) {
        console.error("Error al guardar cuadre:", error);
        alert("Ocurrió un error al guardar el cuadre.");
      }
    },
    checkPassword() {
      if (this.enteredPassword === this.correctPassword) {
        this.showPasswordModal = false;
        this.errorMessage = "";
      } else {
        this.errorMessage = "Contraseña incorrecta. Intenta de nuevo.";
      }
    },
  },
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
  font-family: "Arial", sans-serif;
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

/* === MODAL === */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal {
  background: #043861;
  border-radius: 12px;
  padding: 30px;
  width: 300px;
  text-align: center;
  color: #fff;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
}

.modal input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: none;
  margin-top: 10px;
  text-align: center;
}

.modal-buttons {
  margin-top: 15px;
}

.modal button {
  background: #97d569;
  color: #043861;
  border: none;
  padding: 8px 20px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
}

.modal button:hover {
  background: #81c25d;
}

.error {
  margin-top: 10px;
  color: #ff5c5c;
  font-weight: bold;
}
</style>
