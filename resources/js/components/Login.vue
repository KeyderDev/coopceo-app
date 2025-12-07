<template>
  <div class="login-page">
    <div class="login-card">
      <div class="logo-container">
        <i class="fa-solid fa-user-shield logo-icon"></i>
      </div>
      <h1 class="login-title">Bienvenido</h1>
      <p class="login-subtitle">Inicia sesión para continuar</p>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="email">Correo Electrónico</label>
          <input id="email" v-model="email" type="text" placeholder="ejemplo@correo.com" autocomplete="off" required />
        </div>

        <div class="form-group">
          <label for="password">Contraseña</label>
          <input id="password" v-model="password" type="password" placeholder="Tu contraseña" required />
        </div>

        <button type="submit" class="login-button" :disabled="loading">
          <span v-if="!loading">Entrar</span>
          <span v-else class="spinner"></span>
        </button>

        <p v-if="error" class="error-message">{{ error }}</p>
      </form>

      <div class="footer-links">
        <router-link to="/forgot-password" class="link">¿Olvidaste tu contraseña?</router-link>
        <router-link to="/register" class="link">Crear cuenta</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Login",
  data() {
    return {
      email: "",
      password: "",
      loading: false,
      error: null,
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post(`${import.meta.env.VITE_APP_URL}/api/login`, {
          email: this.email,
          password: this.password,
        });

        const token = response.data.access_token;
        const user = response.data.user;
        const coop = response.data.coop_codigo;
        const isAdmin = Number(response.data.admin ?? user.admin ?? 0);

        localStorage.setItem("auth_token", token);
        localStorage.setItem("user", JSON.stringify(user));
        localStorage.setItem("coop_codigo", coop);
        localStorage.setItem("is_admin", isAdmin);

        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        axios.defaults.headers.common["X-Coop-Code"] = coop;

        if (isAdmin === 1) {
          this.$router.push("/");
        } else {
          this.$router.push('/')
        }
      } catch (err) {
        if (err.response) {
          this.error = err.response.data.message || "Credenciales inválidas.";
        } else if (err.request) {
          this.error = "No se pudo conectar con el servidor.";
        } else {
          this.error = "Ocurrió un error inesperado.";
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.login-page {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  font-family: "Inter", sans-serif;
  padding: 1rem;
}

.login-card {
  background: rgba(25, 27, 31, 0.95);
  padding: 2.5rem 2rem;
  border-radius: 20px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
  text-align: center;
  animation: fadeIn 0.6s ease-in-out;
}

.logo-container {
  margin-bottom: 1rem;
}

.logo-icon {
  font-size: 3rem;
  color: #9dd86a;
  background: rgba(157, 216, 106, 0.1);
  padding: 15px;
  border-radius: 50%;
  animation: pulse 2s infinite ease-in-out;
}

.login-title {
  color: #ffffff;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.login-subtitle {
  color: #ccc;
  font-size: 0.95rem;
  margin-bottom: 2rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.form-group {
  text-align: left;
}

label {
  color: #aaa;
  font-weight: 600;
  font-size: 0.9rem;
  margin-bottom: 0.4rem;
  display: block;
}

input {
  width: 100%;
  padding: 0.7rem 0.9rem;
  border-radius: 10px;
  background-color: #2c2f36;
  border: 2px solid transparent;
  color: #fff;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

input:focus {
  border-color: #9dd86a;
  box-shadow: 0 0 8px rgba(157, 216, 106, 0.5);
  outline: none;
}

.login-button {
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  font-weight: 700;
  padding: 0.9rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(157, 216, 106, 0.4);
}

.login-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spinner {
  border: 3px solid #2c2f36;
  border-top: 3px solid #9dd86a;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  animation: spin 0.8s linear infinite;
  display: inline-block;
  vertical-align: middle;
}

.error-message {
  color: #ff6666;
  font-size: 0.9rem;
  margin-top: 1rem;
}

.footer-links {
  margin-top: 1.5rem;
  display: flex;
  justify-content: space-between;
}

.link {
  color: #9dd86a;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.link:hover {
  color: #b9f089;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {

  0%,
  100% {
    transform: scale(1);
    opacity: 1;
  }

  50% {
    transform: scale(1.1);
    opacity: 0.7;
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 480px) {
  .login-card {
    padding: 2rem 1.5rem;
    border-radius: 14px;
  }

  .login-title {
    font-size: 1.7rem;
  }
}
</style>
