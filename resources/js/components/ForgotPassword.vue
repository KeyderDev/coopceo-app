<template>
  <div class="forgot-password-page">
    <div class="forgot-password-card">
      <div class="logo-container">
        <i class="fa-solid fa-key logo-icon"></i>
      </div>

      <h1 class="title">Recuperar Contraseña</h1>
      <p class="subtitle">
        Ingresa tu correo electrónico y sigue los pasos para cambiar tu contraseña.
      </p>

      <form @submit.prevent="handleForgotPassword" class="forgot-password-form">
        <!-- Paso 1: Ingresar email -->
        <div v-if="step === 1" class="form-group">
          <label>Correo electrónico</label>
          <input type="email" v-model="email" required placeholder="ejemplo@correo.com" />
          <button type="submit" :disabled="loading">
            <span v-if="!loading">Enviar OTP</span>
            <span v-else class="spinner"></span>
          </button>
        </div>

        <!-- Paso 2: Ingresar OTP -->
        <div v-else-if="step === 2" class="form-group">
          <label>OTP</label>
          <input type="text" v-model="otp" required placeholder="Ingresa el código recibido" />
          <button type="submit" :disabled="loading">
            <span v-if="!loading">Verificar OTP</span>
            <span v-else class="spinner"></span>
          </button>
        </div>

        <!-- Paso 3: Cambiar contraseña -->
        <div v-else-if="step === 3" class="form-group">
          <label>Nueva Contraseña</label>
          <input type="password" v-model="newPassword" required placeholder="Nueva contraseña" />
          <button type="submit" :disabled="loading">
            <span v-if="!loading">Cambiar Contraseña</span>
            <span v-else class="spinner"></span>
          </button>
        </div>
      </form>

      <p v-if="error" class="error-message">{{ error }}</p>
      <p v-if="success" class="success-message">{{ success }}</p>

      <router-link to="/login" class="back-to-login">
        <i class="fa-solid fa-arrow-left"></i> Volver al login
      </router-link>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ForgotPassword",
  data() {
    return {
      email: "",
      otp: "",
      newPassword: "",
      step: 1,
      loading: false,
      error: null,
      success: null,
    };
  },
  methods: {
    async handleForgotPassword() {
      this.error = null;
      this.success = null;
      this.loading = true;

      try {
        if (this.step === 1) {
          await axios.post(`${import.meta.env.VITE_APP_URL}/api/password/otp`, { email: this.email });
          this.success = "OTP enviado a tu correo.";
          this.step = 2;
        } else if (this.step === 2) {
          await axios.post(`${import.meta.env.VITE_APP_URL}/api/password/verify-otp`, {
            email: this.email,
            otp: this.otp,
          });
          this.success = "OTP verificado, ingresa tu nueva contraseña.";
          this.step = 3;
        } else if (this.step === 3) {
          await axios.post(`${import.meta.env.VITE_APP_URL}/api/password/reset`, {
            email: this.email,
            otp: this.otp,
            password: this.newPassword,
          });
          this.success = "¡Contraseña cambiada exitosamente!";
          this.step = 1;
          this.email = this.otp = this.newPassword = "";
        }
      } catch (err) {
        this.error = err.response?.data?.message || "Ocurrió un error inesperado.";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
/* 🌌 Fondo */
.forgot-password-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  font-family: "Inter", sans-serif;
  padding: 1rem;
}

/* 🪪 Tarjeta */
.forgot-password-card {
  background: rgba(25, 27, 31, 0.95);
  padding: 2.5rem 2rem;
  border-radius: 20px;
  width: 100%;
  max-width: 400px;
  text-align: center;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
  animation: fadeIn 0.6s ease-in-out;
}

/* 🔑 Icono */
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
  font-size: 1.9rem;
  font-weight: 700;
  margin-bottom: 0.3rem;
}

.subtitle {
  color: #ccc;
  font-size: 0.95rem;
  margin-bottom: 2rem;
}

/* 📋 Formulario */
.forgot-password-form {
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

/* 🔘 Botones */
button {
  background: linear-gradient(135deg, #9dd86a, #7ab55c);
  color: #fff;
  font-weight: 700;
  padding: 0.85rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s ease;
  width: 100%;
}

button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(157, 216, 106, 0.4);
}

button:disabled {
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

/* ⚠️ Mensajes */
.error-message {
  color: #ff6666;
  font-size: 0.9rem;
  margin-top: 1rem;
}

.success-message {
  color: #9dd86a;
  font-size: 0.9rem;
  margin-top: 1rem;
}

/* 🔗 Enlace */
.back-to-login {
  display: inline-block;
  margin-top: 1.5rem;
  color: #9dd86a;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.back-to-login:hover {
  color: #b9f089;
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
  0%, 100% {
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
  .forgot-password-card {
    padding: 2rem 1.5rem;
    border-radius: 14px;
  }

  .title {
    font-size: 1.6rem;
  }

  input {
    font-size: 0.9rem;
  }
}
</style>
