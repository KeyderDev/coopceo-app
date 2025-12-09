<template>
  <div class="reviews-wrapper">
    <h2 class="title">Reseñas de la Cooperativa</h2>

    <div class="review-list">
      <div class="review" v-for="r in reviews" :key="r.id">
        <div class="review-header">
          <div class="left">
            <div class="avatar">{{ r.user.nombre[0] }}</div>
            <strong>{{ r.user.nombre }} {{ r.user.apellido }}</strong>
          </div>
          <span class="stars-view">{{ '★'.repeat(r.estrellas) }}</span>
        </div>

        <p class="comment">{{ r.comentario }}</p>
        <span class="date">{{ formatDate(r.fecha) }}</span>
      </div>
    </div>

    <button class="add-btn" @click="showForm = true">+</button>

    <div class="modal-bg" v-if="showForm" @click.self="showForm = false">
      <div class="form-box">
        <div class="stars">
          <span
            v-for="n in 5"
            :key="n"
            @click="estrellas = n"
            :class="{ active: n <= estrellas }"
          >★</span>
        </div>

        <textarea
          v-model="comentario"
          placeholder="Escribe tu reseña..."
          class="textarea"
        ></textarea>

        <button class="btn" @click="sendReview">Enviar Reseña</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios"

export default {
  data() {
    return {
      reviews: [],
      estrellas: 0,
      comentario: "",
      showForm: false
    }
  },

  async mounted() {
    const res = await axios.get("/api/reviews")
    this.reviews = res.data
  },

  methods: {
    async sendReview() {
      if (!this.estrellas) return

      const res = await axios.post("/api/reviews", {
        estrellas: this.estrellas,
        comentario: this.comentario
      })

      this.reviews.unshift(res.data)
      this.estrellas = 0
      this.comentario = ""
      this.showForm = false
    },

    formatDate(date) {
      return new Date(date).toLocaleString()
    }
  }
}
</script>

<style>
.reviews-wrapper {
  padding: 20px;
  max-width: 650px;
  margin: auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
  position: relative;
}

.title {
  font-size: 26px;
  font-weight: 700;
  color: #97d569;
  text-align: center;
}

.review-list {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.review {
  background: #0f2027;
  padding: 16px;
  border-radius: 14px;
  border: 1px solid #033961;
  display: flex;
  flex-direction: column;
  gap: 8px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.avatar {
  width: 36px;
  height: 36px;
  background: #033961;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #97d569;
  font-weight: 700;
  font-size: 18px;
}

.stars-view {
  color: #97d569;
  font-size: 18px;
}

.comment {
  font-size: 14px;
  color: #d9e4ea;
}

.date {
  font-size: 12px;
  opacity: .7;
}

.add-btn {
  position: fixed;
  bottom: 28px;
  right: 28px;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #97d569;
  color: #0f2027;
  border: none;
  font-size: 32px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0,0,0,0.35);
}

.modal-bg {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.65);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 25px;
}

.form-box {
  background: #0f2027;
  padding: 18px;
  border-radius: 14px;
  width: 100%;
  max-width: 380px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  border: 1px solid #033961;
  box-shadow: 0 4px 14px rgba(0,0,0,0.4);
}

.stars {
  display: flex;
  justify-content: center;
  gap: 6px;
}

.stars span {
  font-size: 34px;
  cursor: pointer;
  color: #2c5364;
  transition: .15s ease;
}

.stars span.active {
  color: #97d569;
  transform: scale(1.15);
}

.textarea {
  width: 100%;
  background: #0d181d;
  color: white;
  padding: 14px;
  border-radius: 10px;
  border: 1px solid #033961;
  height: 120px;
  font-size: 15px;
}

.btn {
  background: #97d569;
  padding: 12px;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  text-align: center;
  font-size: 16px;
}

@media (max-width: 480px) {
  .title {
    font-size: 22px;
  }

  .avatar {
    width: 32px;
    height: 32px;
    font-size: 16px;
  }

  .review {
    padding: 14px;
  }
}
</style>
