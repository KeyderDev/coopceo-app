<template>
  <div class="email-container">
    <h2 class="title">Enviar Email</h2>

    <input v-model="subject" placeholder="Asunto" class="input" />
    <textarea v-model="body" placeholder="Mensaje" class="textarea"></textarea>

    <h3 class="subtitle">Selecciona usuarios</h3>
    <div class="users-list">
      <label class="user-label select-all">
        <input
          type="checkbox"
          v-model="selectAll"
          @change="toggleSelectAll"
        />
        Seleccionar todos
      </label>
      <label v-for="user in users" :key="user.id" class="user-label">
        <input type="checkbox" :value="user.id" v-model="selectedUsers" />
        {{ user.email }}
      </label>
    </div>

    <button class="send-btn" @click="sendEmail">Enviar</button>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      users: [],
      selectedUsers: [],
      subject: '',
      body: '',
      selectAll: false,
    };
  },
  async created() {
    try {
      const token = localStorage.getItem('auth_token');
      const res = await axios.get('https://coopceo.ddns.net:8000/api/users', {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.users = res.data;
    } catch (error) {
      console.error(error);
      alert('No autorizado. Por favor, inicia sesión.');
    }
  },
  watch: {
    selectedUsers(val) {
      this.selectAll = val.length === this.users.length;
    },
  },
  methods: {
    toggleSelectAll() {
      if (this.selectAll) {
        this.selectedUsers = this.users.map(user => user.id);
      } else {
        this.selectedUsers = [];
      }
    },
    async sendEmail() {
      if (!this.subject || !this.body || this.selectedUsers.length === 0) {
        return alert('Completa todos los campos y selecciona al menos un usuario.');
      }

      try {
        const token = localStorage.getItem('auth_token');
        await axios.post(
          'https://coopceo.ddns.net:8000/api/send-email',
          {
            user_ids: this.selectedUsers,
            subject: this.subject,
            body: this.body,
          },
          { headers: { Authorization: `Bearer ${token}` } }
        );
        alert('Emails enviados correctamente');
        this.subject = '';
        this.body = '';
        this.selectedUsers = [];
        this.selectAll = false;
      } catch (error) {
        console.error(error);
        alert('Error al enviar los emails');
      }
    },
  },
};
</script>

<style scoped>
.email-container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 2.5rem;
  background: #043b65;
  border-radius: 12px;
  color: #fff;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
  font-family: 'Inter', sans-serif;
}

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
}

.input::placeholder,
.textarea::placeholder {
  color: #043b65;
  opacity: 0.8;
}

.textarea {
  min-height: 140px;
  resize: vertical;
}

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
}

.user-label:hover {
  background: rgba(151, 213, 105, 0.15);
}

.user-label input[type='checkbox'] {
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

@media (max-width: 600px) {
  .email-container {
    max-width: 95%;
    padding: 1.5rem;
    margin: 1rem auto;
  }

  .title {
    font-size: 1.6rem;
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

  .user-label {
    font-size: 0.9rem;
    padding: 0.3rem 0.5rem;
  }

  .send-btn {
    font-size: 1rem;
    padding: 0.8rem;
  }
}
</style>

