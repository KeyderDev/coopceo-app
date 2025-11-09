<template>
  <div class="menu-wrapper">
    <div class="menu-grid">
      <router-link to="/registro-compras" class="menu-item">
        <i class="fa-solid fa-cart-shopping"></i>
        <p>Purchases</p>
      </router-link>
      <router-link to="/pos-transactions" class="menu-item">
        <i class="fa-solid fa-scroll"></i>
        <p>Transactions</p>
      </router-link>
      <router-link to="/cuadres" class="menu-item">
        <i class="fas fa-boxes"></i>
        <p>Reconciliations</p>
      </router-link>
      <router-link to="/horarios" class="menu-item">
        <i class="fa-solid fa-clock"></i>
        <p>Schedules</p>
      </router-link>
    </div>

  </div>
</template>

<script>
export default {
  name: "MenuGrid",
  methods: {
    volverMenu() {
      this.$router.push("/");
    },
  },
};
</script>

<style scoped>
/* 🌌 Fondo general */
.menu-wrapper {
  min-height: 100vh;
  width: 100%;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem 1.5rem 6rem;
  box-sizing: border-box;
  color: #fff;
  font-family: "Inter", sans-serif;
}

/* === GRID FIJA === */
.menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.8rem;
  justify-content: center;
  align-content: center;
  width: 100%;
  max-width: 900px;
}

/* === ITEMS === */
.menu-item {
  background: rgba(255, 255, 255, 0.05);
  border-left: 5px solid #9dd86a;
  border-radius: 16px;
  text-decoration: none;
  color: #fff;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 2rem 1rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  transition: all 0.25s ease;
  backdrop-filter: blur(10px);
  cursor: pointer;
}

.menu-item:hover {
  background: rgba(157, 216, 106, 0.1);
  transform: translateY(-5px);
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35);
}

.menu-item i {
  font-size: 2.4rem;
  margin-bottom: 10px;
  color: #9dd86a;
  transition: transform 0.3s ease;
}

.menu-item:hover i {
  transform: scale(1.1);
}

.menu-item p {
  font-weight: 600;
  font-size: 1rem;
  text-align: center;
  letter-spacing: 0.5px;
  margin: 0;
}

/* === BOTÓN VOLVER === */
.btn-volver {
  position: fixed;
  bottom: 25px;
  right: 25px;
  background: linear-gradient(135deg, #9dd86a, #7ac75d);
  color: #1a1a1a;
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
  transform: translateY(-3px);
  filter: brightness(1.1);
}

/* 📱 RESPONSIVE */
@media (max-width: 768px) {
  .menu-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.2rem;
  }

  .menu-item {
    padding: 1.5rem 0.8rem;
  }

  .menu-item i {
    font-size: 1.8rem;
  }

  .menu-item p {
    font-size: 0.9rem;
  }

  .btn-volver {
    right: 50%;
    transform: translateX(50%);
    padding: 0.8rem 1.4rem;
  }
}

@media (max-width: 480px) {
  .menu-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }

  .menu-item {
    padding: 1.2rem 0.6rem;
  }

  .menu-item i {
    font-size: 1.6rem;
  }

  .menu-item p {
    font-size: 0.8rem;
  }

  .btn-volver {
    bottom: 15px;
    right: 50%;
    transform: translateX(50%);
    padding: 0.7rem 1.2rem;
    font-size: 0.9rem;
  }
}
</style>
