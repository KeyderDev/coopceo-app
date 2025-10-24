<template>
  <div class="email-container">
    <!-- Modal de contraseña -->
    <div v-if="showPasswordModal" class="modal-overlay">
      <div class="modal">
        <h3>🔒 Acceso restringido</h3>
        <p>Por favor, ingresa la contraseña para continuar:</p>
        <input type="password" v-model="enteredPassword" placeholder="Contraseña" @keyup.enter="checkPassword" />
        <div class="modal-buttons">
          <button @click="checkPassword">Entrar</button>
        </div>
        <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
      </div>
    </div>

    <!-- Contenido principal -->
    <div v-if="!showPasswordModal">
      <h2 class="title">Enviar Email</h2>

      <input v-model="subject" placeholder="Asunto" class="input" />
      <textarea v-model="body" placeholder="Mensaje" class="textarea"></textarea>

      <h3 class="subtitle">Selecciona usuarios</h3>
      <div class="users-list">
        <label class="user-label select-all">
          <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
          Seleccionar todos
        </label>

        <label v-for="user in users" :key="user.id" class="user-label">
          <input type="checkbox" :value="user.id" v-model="selectedUsers" />
          {{ user.email }}
        </label>
      </div>

      <button class="send-btn" @click="sendEmail">Enviar</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      users: [],
      selectedUsers: [],
      subject: "",
      body: "",
      selectAll: false,
      showPasswordModal: true,
      enteredPassword: "",
      correctPassword: "0000superadmin",
      errorMessage: "",
    };
  },
  async created() {
    try {
      const token = localStorage.getItem("auth_token");
      const res = await axios.get(`${import.meta.env.VITE_APP_URL}/api/users`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.users = res.data;
    } catch (error) {
      console.error(error);
      alert("No autorizado. Por favor, inicia sesión.");
    }
  },
  watch: {
    selectedUsers(val) {
      this.selectAll = val.length === this.users.length;
    },
  },
  methods: {
    toggleSelectAll() {
      this.selectedUsers = this.selectAll
        ? this.users.map((user) => user.id)
        : [];
    },
    async sendEmail() {
      if (!this.subject || !this.body || this.selectedUsers.length === 0) {
        return alert(
          "Completa todos los campos y selecciona al menos un usuario."
        );
      }

      try {
        const token = localStorage.getItem("auth_token");
        await axios.post(
          `${import.meta.env.VITE_APP_URL}/api/send-email`,
          {
            user_ids: this.selectedUsers,
            subject: this.subject,
            body: this.body,
          },
          { headers: { Authorization: `Bearer ${token}` } }
        );
        alert("Emails enviados correctamente");
        this.subject = "";
        this.body = "";
        this.selectedUsers = [];
        this.selectAll = false;
      } catch (error) {
        console.error(error);
        alert("Error al enviar los emails");
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
/* ==== CONTENEDOR PRINCIPAL ==== */
.email-container {
  width: 100%;
  max-width: 800px;
  margin: 1.5rem auto;
  padding: 2rem;
  background: #043b65;
  border-radius: 12px;
  color: #fff;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
  font-family: "Inter", sans-serif;
  box-sizing: border-box;
}

/* ==== TITULOS ==== */
.title {
  margin-bottom: 1rem;
  color: #97d569;
  text-align: center;
  font-size: 2rem;
  font-weight: 600;
}

.subtitle {
  margin: 1.5rem 0 0.5rem;
  color: #97d569;
  font-size: 1.2rem;
}

/* ==== CAMPOS ==== */
.input,
.textarea {
  width: 100%;
  padding: 1rem;
  margin-bottom: 1rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  color: #043b65;
  background: #97d569;
  font-weight: 500;
  box-sizing: border-box;
}

.textarea {
  min-height: 140px;
  resize: vertical;
}

/* ==== LISTA DE USUARIOS ==== */
.users-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  max-height: 250px;
  overflow-y: auto;
  padding: 1rem;
  background: #062d55;
  border-radius: 10px;
}

.user-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.95rem;
  padding: 0.4rem 0.6rem;
  border-radius: 6px;
  transition: all 0.2s ease;
  word-break: break-all;
}

.user-label:hover {
  background: rgba(151, 213, 105, 0.15);
}

.user-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: #97d569;
}

.select-all {
  font-weight: 600;
  border-bottom: 1px solid #97d569;
  margin-bottom: 0.5rem;
  padding-bottom: 0.5rem;
}

/* ==== BOTON ==== */
.send-btn {
  margin-top: 1.5rem;
  width: 100%;
  padding: 0.9rem;
  background: #97d569;
  color: #043b65;
  border: none;
  border-radius: 8px;
  font-size: 1.2rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.send-btn:hover {
  background: #82c156;
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
}

/* ==== MODAL ==== */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
  padding: 1rem;
  overflow-y: auto;
}

.modal {
  background: #043861;
  border-radius: 12px;
  padding: 25px 20px;
  width: 90%;
  max-width: 350px;
  text-align: center;
  color: #fff;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
  box-sizing: border-box;
}

.modal input {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: none;
  margin-top: 10px;
  text-align: center;
  font-size: 1rem;
}

.modal-buttons {
  margin-top: 15px;
}

.modal button {
  background: #97d569;
  color: #043861;
  border: none;
  padding: 10px 25px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  width: 100%;
}

.modal button:hover {
  background: #81c25d;
}

.error {
  margin-top: 10px;
  color: #ff5c5c;
  font-weight: bold;
}

/* ==== RESPONSIVE ==== */
@media (max-width: 600px) {
  .email-container {
    width: 100%;
    padding: 1rem;
    margin: 1rem auto;
    border-radius: 10px;
    background: #043b65;
  }

  .title {
    font-size: 1.5rem;
  }

  .subtitle {
    font-size: 1rem;
  }

  .input,
  .textarea {
    font-size: 0.95rem;
    padding: 0.8rem;
  }

  .textarea {
    min-height: 100px;
  }

  .users-list {
    max-height: 180px;
    padding: 0.8rem;
  }

  .user-label {
    font-size: 0.9rem;
    padding: 0.3rem 0.4rem;
  }

  .send-btn {
    font-size: 1rem;
    padding: 0.8rem;
  }

  .modal {
    width: 95%;
    padding: 1.2rem;
  }

  .modal h3 {
    font-size: 1.2rem;
  }

  .modal p {
    font-size: 0.9rem;
  }

  .modal input {
    font-size: 0.9rem;
  }
}
</style>
