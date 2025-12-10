<template>
    <div class="settings-photo-wrapper">
        <h2 class="title">Foto de Perfil</h2>

        <div class="photo-card">
            <div class="preview-box">
                <img v-if="preview || user?.profile_picture" :src="preview || imageUrl(user?.profile_picture)"
                    class="preview-img" />
                <div v-else class="preview-placeholder">
                    {{ user?.nombre?.charAt(0) }}
                </div>
            </div>

            <label class="file-btn" for="fileInput">
                <i class="fa-solid fa-image"></i> Seleccionar foto
            </label>

            <input id="fileInput" type="file" accept="image/*" name="profile_picture" class="hidden-input"
                @change="onFileChange">


            <button class="save-btn" :disabled="loading || !file" @click="uploadPhoto">
                {{ loading ? "Subiendo..." : "Guardar Foto" }}
            </button>

            <p v-if="success" class="success-msg">✓ Foto actualizada correctamente</p>
        </div>
    </div>
</template>

<script>
import axios from "axios"

export default {
    props: ["user"],

    data() {
        return {
            preview: null,
            file: null,
            loading: false,
            success: false
        }
    },

    methods: {
        onFileChange(e) {
            const f = e.target.files[0]
            if (!f) return
            this.file = f
            this.preview = URL.createObjectURL(f)
            this.success = false
        },

        imageUrl(path) {
            return `/storage/${path}`
        },

        async uploadPhoto() {
            if (!this.file) return

            if (this.file.size > 5 * 1024 * 1024) {
                alert("La imagen es muy grande (máx 5MB).")
                return
            }

            this.loading = true
            this.success = false

            const form = new FormData()
            form.append("profile_picture", this.file)

            try {
                const res = await axios.post(
                    `${import.meta.env.VITE_APP_URL}/api/user/profile-picture`,
                    form,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
                            "X-Coop-Code": localStorage.getItem("coop_code")
                        }
                    }
                )

                this.$emit("updated", res.data.profile_picture)
                this.success = true
                setTimeout(() => (this.success = false), 3000)
            } catch (e) {
                alert("Hubo un error subiendo la foto. Intenta otra vez.")
            }

            this.loading = false
        }
    }
}
</script>


<style>
.settings-photo-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1.2rem;
}

.title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: .5rem;
}

.photo-card {
    background: #1e1e1e;
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.2rem;
}

.preview-box {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #111;
}

.preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-placeholder {
    width: 100%;
    height: 100%;
    font-size: 3rem;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #333;
    color: white;
}

.file-btn {
    width: 100%;
    background: #2c2c2c;
    padding: .8rem;
    border-radius: 8px;
    color: #ddd;
    text-align: center;
    cursor: pointer;
    transition: .2s ease;
    font-weight: 600;
}

.file-btn:hover {
    background: #3a3a3a;
}

.hidden-input {
    display: none;
}

.save-btn {
    width: 100%;
    background: #97d569;
    color: black;
    padding: .9rem;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    transition: .2s ease;
}

.save-btn:disabled {
    opacity: .6;
}

.success-msg {
    color: #97d569;
    font-weight: 600;
    margin-top: .5rem;
    animation: fadeIn .3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
