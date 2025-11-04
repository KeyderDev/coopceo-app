<template>
  <div class="forgot-password-page">
    <div class="forgot-password-card">
      <h1>Recuperar Contraseña</h1>
      <p>Ingresa tu correo electrónico y sigue los pasos para cambiar tu contraseña.</p>

      <form @submit.prevent="handleForgotPassword" class="forgot-password-form">
        <!-- Paso 1: Ingresar email -->
        <div v-if="step === 1" class="form-group">
          <label>Correo electrónico</label>
          <input type="email" v-model="email" required placeholder="ejemplo@correo.com" />
          <button type="submit" :disabled="loading">
            {{ loading ? 'Enviando...' : 'Enviar OTP' }}
          </button>
        </div>

        <!-- Paso 2: Ingresar OTP -->
        <div v-else-if="step === 2" class="form-group">
          <label>OTP</label>
          <input type="text" v-model="otp" required placeholder="Ingresa el código recibido" />
          <button type="submit" :disabled="loading">
            {{ loading ? 'Verificando...' : 'Verificar OTP' }}
          </button>
        </div>

        <!-- Paso 3: Cambiar contraseña -->
        <div v-else-if="step === 3" class="form-group">
          <label>Nueva Contraseña</label>
          <input type="password" v-model="newPassword" required placeholder="Nueva contraseña" />
          <button type="submit" :disabled="loading">
            {{ loading ? 'Cambiando...' : 'Cambiar Contraseña' }}
          </button>
        </div>
      </form>

      <p v-if="error" class="error-message">{{ error }}</p>
      <p v-if="success" class="success-message">{{ success }}</p>

      <router-link to="/login" class="back-to-login">Volver al login</router-link>
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
      step: 1, // 1=Email, 2=OTP, 3=Cambiar contraseña
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
          this.success = "OTP enviado a tu correo";
          this.step = 2;
        } else if (this.step === 2) {
          await axios.post(`${import.meta.env.VITE_APP_URL}/api/password/verify-otp`, {
            email: this.email,
            otp: this.otp,
          });
          this.success = "OTP verificado, ingresa tu nueva contraseña";
          this.step = 3;
        } else if (this.step === 3) {
          await axios.post(`${import.meta.env.VITE_APP_URL}/api/password/reset`, {
            email: this.email,
            otp: this.otp,
            password: this.newPassword,
          });
          this.success = "Contraseña cambiada exitosamente!";
          this.step = 1;
          this.email = this.otp = this.newPassword = "";
        }
      } catch (err) {
        this.error = err.response?.data?.message || "Ocurrió un error inesperado";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.forgot-password-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #044271, #022d50);
  font-family: 'Inter', sans-serif;
  padding: 1rem;
}

.forgot-password-card {
  background-color: #ffffff;
  padding: 2.5rem 2rem;
  border-radius: 16px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.forgot-password-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 18px 36px rgba(0, 0, 0, 0.4);
}

h1 {
  color: #044271;
  margin-bottom: 0.5rem;
  font-size: 1.6rem;
}

p {
  color: #555;
  margin-bottom: 1rem;
  font-size: 0.95rem;
}

.forgot-password-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  text-align: left;
}

label {
  font-weight: 600;
  color: #044271;
  margin-bottom: 0.4rem;
}

input {
  padding: 0.65rem 0.9rem;
  border: 2px solid #ccd6f1;
  border-radius: 10px;
  outline: none;
  font-size: 0.95rem;
  transition: border-color 0.3s, box-shadow 0.3s;
}

input:focus {
  border-color: #99d36c;
  box-shadow: 0 0 8px rgba(153, 211, 108, 0.5);
}

button {
  background-color: #99d36c;
  color: #fff;
  font-weight: 700;
  padding: 0.75rem;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #7cbf5a;
}

.error-message {
  color: #ff4d4f;
  font-weight: 600;
  margin-top: 0.5rem;
}

.success-message {
  color: #4caf50;
  font-weight: 600;
  margin-top: 0.5rem;
}

.back-to-login {
  display: inline-block;
  margin-top: 1rem;
  color: #99d36c;
  font-weight: 600;
  text-decoration: none;
}

.back-to-login:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 480px) {
  .forgot-password-card {
    padding: 2rem 1.5rem;
    border-radius: 12px;
  }

  h1 {
    font-size: 1.4rem;
  }

  input {
    font-size: 0.9rem;
  }

  button {
    font-size: 0.95rem;
    padding: 0.65rem;
  }
}
</style>
