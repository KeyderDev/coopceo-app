<template>
  <div class="login-page">
    <div class="login-card">
      <h1 class="login-title">Bienvenido</h1>
      <p class="login-subtitle">Ingresa con tu usuario y contraseña</p>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="email">Correo Electronico</label>
          <input
            id="email"
            v-model="email"
            type="text"
            placeholder="Email"
            required
          />
        </div>

        <div class="form-group">
          <label for="password">Contraseña</label>
          <input
            id="password"
            v-model="password"
            type="password"
            placeholder="Tu contraseña"
            required
          />
        </div>

        <button type="submit" class="login-button" :disabled="loading">
          {{ loading ? 'Ingresando...' : 'Entrar' }}
        </button>

        <p v-if="error" class="error-message">{{ error }}</p>
      </form>

      <p class="signup-text">
        ¿No tienes cuenta? <router-link to="/register">Regístrate</router-link>
      </p>
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
        const response = await axios.post("/api/login", {
          email: this.email,
          password: this.password,
        });

        const token = response.data.access_token;
        const user = response.data.user;

        localStorage.setItem("auth_token", token);
        localStorage.setItem("user", JSON.stringify(user));

        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

        this.$router.push({ name: "dashboard" });
      } catch (err) {
        if (err.response && err.response.data) {
          this.error =
            err.response.data.message ||
            (err.response.data.errors?.username
              ? err.response.data.errors.username[0]
              : "Credenciales inválidas");
        } else {
          this.error = "Error de conexión. Intenta nuevamente";
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
  background: linear-gradient(135deg, #044271, #022d50);
  font-family: 'Inter', sans-serif;
}

.login-card {
  background-color: #ffffff;
  padding: 2.5rem 2rem;
  border-radius: 16px;
  width: 380px;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 18px 36px rgba(0, 0, 0, 0.4);
}

.logo-container {
  margin-bottom: 1rem;
}

.login-title {
  color: #044271;
  font-size: 2rem;
  margin-bottom: 0.25rem;
  font-weight: 700;
}

.login-subtitle {
  color: #555;
  font-size: 0.95rem;
  margin-bottom: 1.5rem;
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
  display: block;
  margin-bottom: 0.4rem;
  font-weight: 600;
  color: #044271;
}

input {
  width: 100%;
  padding: 0.65rem 0.9rem;
  border: 2px solid #ccd6f1;
  border-radius: 10px;
  outline: none;
  transition: border-color 0.3s, box-shadow 0.3s;
  font-size: 0.95rem;
}

input:focus {
  border-color: #99d36c;
  box-shadow: 0 0 8px rgba(153, 211, 108, 0.5);
}

.login-button {
  background-color: #99d36c;
  color: #ffffff;
  font-weight: 700;
  padding: 0.75rem;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s, transform 0.2s;
}

.login-button:hover {
  background-color: #7cbf5a;
  transform: translateY(-2px);
}

.signup-text {
  margin-top: 1.5rem;
  font-size: 0.9rem;
  color: #444;
}

.signup-text a {
  color: #99d36c;
  font-weight: 600;
  text-decoration: none;
}

.signup-text a:hover {
  text-decoration: underline;
}
</style>
