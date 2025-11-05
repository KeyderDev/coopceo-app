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
            <router-link to ="/horarios" class="menu-item">
                <i class="fa-solid fa-clock"></i>
                <p>Schedules</p>
            </router-link>


            <!-- <div class="menu-item">
                <i class="fas fa-clipboard-list"></i>
                <p>Inventory</p>
            </div>

            <div class="menu-item">
                <i class="fa-solid fa-user"></i>
                <p>Users</p>
            </div>

            <router-link to="/ganancias" class="menu-item">
                <i class="fas fa-chart-line"></i>
                <p>Sales</p>
            </router-link>

            <router-link to="/terminal" class="menu-item">
                <i class="fa-solid fa-cash-register"></i>
                <p>Terminal</p>
            </router-link>

            <div class="menu-item">
                <i class="fas fa-file-alt"></i>
                <p>Reports</p>
            </div> -->
        </div>
    </div>
    <button class="btn-volver" @click="volverMenu">
        <i class="fa-solid fa-house"></i> Menú Principal
    </button>
</template>

<script>
export default {
    name: "MenuGrid",
    methods: {
        volverMenu() {
            this.$router.push("/");
        }
    }
};
</script>

<style scoped>
.menu-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #e0e0e0;
    min-height: 100vh;
    width: 100%;
    padding: 1.5rem;
    box-sizing: border-box;
}

/* === GRID FIJA 3x2 === */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(3, 200px);
    grid-template-rows: repeat(2, 200px);
    gap: 25px;
    justify-content: center;
    align-content: center;
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
}

/* === ITEMS === */
.menu-item {
    background-color: #4caf50;
    border-radius: 12px;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-decoration: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
    transition: all 0.25s ease;
    cursor: pointer;
}

.menu-item:hover {
    background-color: #45a049;
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
}

.menu-item i {
    font-size: 34px;
    margin-bottom: 10px;
}

.menu-item p {
    font-weight: bold;
    font-size: 1rem;
    text-align: center;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .menu-grid {
        grid-template-columns: repeat(2, 150px);
        grid-template-rows: auto;
        gap: 20px;
    }

    .menu-item {
        height: 130px;
    }

    .menu-item i {
        font-size: 28px;
    }

    .menu-item p {
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .menu-grid {
        grid-template-columns: repeat(2, 120px);
        gap: 12px;
    }

    .menu-item {
        height: 110px;
    }

    .menu-item i {
        font-size: 24px;
    }

    .menu-item p {
        font-size: 0.8rem;
    }
}
</style>
