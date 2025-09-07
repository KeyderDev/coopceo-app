<template>
    <div class="logs-container">
        <h2>Logs de usuarios</h2>
        <div class="logs-table-wrapper">
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Evento</th>
                        <th>IP</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs" :key="log.id">
                        <td>
                            {{ log.user ? log.user.nombre + ' ' + log.user.apellido : 'Desconocido' }}
                        </td>
                        <td>
                            <span class="event-badge">{{ log.event }}</span>
                        </td>
                        <td>{{ log.ip_address }}</td>
                        <td>{{ new Date(log.created_at).toLocaleString() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Logs",
    data() {
        return {
            logs: []
        };
    },
    async created() {
        const token = localStorage.getItem("auth_token");
        const res = await axios.get("/api/logs", {
            headers: { Authorization: `Bearer ${token}` }
        });
        this.logs = res.data;
    }
};
</script>

<style scoped>
.logs-container {
    padding: 20px;
    background: #044271; /* Azul oscuro de fondo */
    color: #e0e0e0;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    font-family: "Segoe UI", sans-serif;
}

.logs-container h2 {
    margin-bottom: 15px;
    font-size: 22px;
    font-weight: 600;
    color: #97d569; /* Verde para el título */
}

/* Contenedor con scroll */
.logs-table-wrapper {
    max-height: 600px; /* Ajusta la altura a lo que necesites */
    overflow-y: auto;
    border-radius: 8px;
    border: 1px solid #06609d;
}

/* Tabla */
.logs-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px; /* Para que no se vea comprimida */
}

.logs-table thead {
    background: #97d569; /* Verde */
    color: #044271;      /* Azul oscuro para contraste */
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 1;
}

.logs-table th, 
.logs-table td {
    padding: 12px 15px;
    text-align: left;
}

.logs-table tbody tr {
    background: #03365a; /* Azul más oscuro para las filas */
    transition: background 0.3s;
}

.logs-table tbody tr:nth-child(even) {
    background: #022a47;
}

.logs-table tbody tr:hover {
    background: #055a91;
}

.logs-table td {
    border-bottom: 1px solid #06609d;
}

/* Badge para el evento */
.event-badge {
    background: #97d569;
    color: #044271;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
}
</style>
