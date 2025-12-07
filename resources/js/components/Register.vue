<template>
  <div class="register-page">
    <div class="register-card">
      <div class="logo-container">
        <i class="fa-solid fa-user-plus logo-icon"></i>
      </div>

      <h1 class="title">Regístrate</h1>
      <p class="subtitle">Crea tu cuenta para comenzar</p>

      <form @submit.prevent="handleRegister" class="register-form">

        <div class="form-row">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input id="nombre" v-model="nombre" type="text" placeholder="Tu nombre" required />
          </div>

          <div class="form-group">
            <label for="apellido">Apellidos</label>
            <input id="apellido" v-model="apellido" type="text" placeholder="Tus apellidos" required />
          </div>
        </div>

        <div class="form-group">
          <label for="telefono">Teléfono</label>
          <input id="telefono" v-model="telefono" type="text" placeholder="Tu número de teléfono" />
        </div>

        <div class="form-group">
          <label for="email">Correo Electrónico</label>
          <input id="email" v-model="email" type="email" placeholder="ejemplo@correo.com" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input id="password" v-model="password" type="password" placeholder="Tu contraseña" required />
          </div>

          <div class="form-group">
            <label for="password_confirmation">Confirmar Contraseña</label>
            <input id="password_confirmation" v-model="password_confirmation" type="password"
              placeholder="Repite tu contraseña" required />
          </div>
        </div>

        <div class="form-group">
          <label for="codigo_coop">Código de Cooperativa</label>
          <input id="codigo_coop" v-model="codigo_coop" type="text" placeholder="Ej: COO1" required />
        </div>

        <button type="submit" class="register-button" :disabled="loading">
          <span v-if="!loading">Registrarse</span>
          <span v-else class="spinner"></span>
        </button>

        <p v-if="error" class="error-message">{{ error }}</p>
      </form>

      <p class="signup-text">
        ¿Ya tienes cuenta?
        <router-link to="/login">Inicia Sesión</router-link>
      </p>
    </div>
  </div>
</template>


<script>
import axios from "axios";

export default {
  name: "Register",
  data() {
    return {
      nombre: "",
      apellido: "",
      telefono: "",
      codigo_coop: "",
      email: "",
      password: "",
      password_confirmation: "",
      loading: false,
      error: null,
    };
  },
  methods: {
    async handleRegister() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post(`${import.meta.env.VITE_APP_URL}/api/register`, {
          nombre: this.nombre,
          apellido: this.apellido,
          telefono: this.telefono,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
          coop_codigo: this.codigo_coop
        });

        const token = response.data.access_token;
        const user = response.data.user;

        localStorage.setItem("auth_token", token);
        localStorage.setItem("user", JSON.stringify(user));

        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        this.$router.push("/login");
      } catch (err) {
        if (err.response && err.response.data) {
          this.error =
            err.response.data.message ||
            Object.values(err.response.data.errors || {}).flat()[0] ||
            "Error al registrar.";
        } else {
          this.error = "Error de conexión. Intenta nuevamente.";
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
/* 🌌 Fondo general */
.register-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  font-family: "Inter", sans-serif;
  padding: 1rem;
}

/* 🪪 Tarjeta */
.register-card {
  background: rgba(25, 27, 31, 0.95);
  padding: 2.5rem 2rem;
  border-radius: 20px;
  width: 100%;
  max-width: 460px;
  text-align: center;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
  animation: fadeIn 0.6s ease-in-out;
}

/* 🧩 Icono superior */
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

/* 🖋️ Títulos */
.title {
  color: #ffffff;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.4rem;
}

.subtitle {
  color: #ccc;
  font-size: 0.95rem;
  margin-bottom: 2rem;
}

/* 📋 Formulario */
.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.form-row {
  display: flex;
  gap: 1rem;
}

.form-group {
  flex: 1;
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

/* 🔘 Botón */
.register-button {
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  font-weight: 700;
  padding: 0.85rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.register-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(157, 216, 106, 0.4);
}

.register-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* ⏳ Spinner */
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

/* ⚠️ Errores */
.error-message {
  color: #ff6666;
  font-size: 0.9rem;
  margin-top: 1rem;
}

/* 🔗 Texto de registro */
.signup-text {
  margin-top: 1.5rem;
  font-size: 0.9rem;
  color: #ccc;
}

.signup-text a {
  color: #9dd86a;
  font-weight: 600;
  text-decoration: none;
}

.signup-text a:hover {
  color: #b9f089;
  text-decoration: underline;
}

/* ✨ Animaciones */
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

/* 📱 Responsive */
@media (max-width: 480px) {
  .register-card {
    padding: 2rem 1.5rem;
    border-radius: 14px;
  }

  .title {
    font-size: 1.7rem;
  }

  .form-row {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>
