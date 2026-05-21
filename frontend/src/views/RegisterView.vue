<template>
  <div class="page">

    <!-- Left Panel -->
    <div class="left-panel">
      <RouterLink to="/" class="back-link">&#8592; Home Page</RouterLink>

      <div class="left-content">
        <h1>Get Started</h1>
        <p>Already have an account?</p>
        <RouterLink to="/login" class="login-btn">Log in</RouterLink>
      </div>

      <div class="blob b1"></div>
      <div class="blob b2"></div>
      <div class="blob b3"></div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
      <div class="top-bar">
        <span></span>
        <a href="#" class="help">Need help?</a>
      </div>

      <div class="form-box">
        <h2>Create account</h2>

        <div class="field">
          <label>Full Name</label>
          <input v-model="form.full_name" type="text" placeholder="Juan dela Cruz" />
        </div>

        <div class="field">
          <label>Email</label>
          <input v-model="form.email" type="email" placeholder="your@email.com" />
        </div>

        <div class="field">
          <label>Contact Number</label>
          <input
            v-model="form.contact_number"
            type="text"
            placeholder="09XXXXXXXXX"
            maxlength="11"
            @input="form.contact_number = form.contact_number.replace(/\D/g, '').slice(0, 11)"
          />
        </div>

        <div class="field">
          <label>Address</label>
          <input v-model="form.address" type="text" placeholder="Barangay, City" />
        </div>

        <div class="field">
          <label>Password</label>
          <div class="input-wrap">
            <input v-model="form.password" :type="showPass ? 'text' : 'password'" placeholder="••••••••••••" />
            <span class="toggle" @click="showPass = !showPass">{{ showPass ? '⊘' : '👁' }}</span>
          </div>
        </div>

        <div class="checkbox">
          <input type="checkbox" id="terms" v-model="agreed" />
          <label for="terms">I accept the <a href="#">terms of the agreement</a></label>
        </div>

        <p v-if="error" class="error">{{ error }}</p>

        <button @click="register" :disabled="loading || !agreed">
          {{ loading ? 'Creating account...' : 'Sign up' }}
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)
const error = ref('')
const showPass = ref(false)
const agreed = ref(false)
const form = ref({
  full_name: '',
  email: '',
  contact_number: '',
  address: '',
  password: ''
})

async function register() {
  loading.value = true
  error.value = ''
  try {
    const res = await fetch('http://localhost:8000/api/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(form.value)
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Registration failed')
    localStorage.setItem('token', data.token)
    localStorage.setItem('user', JSON.stringify(data.user))
    router.push('/dashboard')
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.page {
  display: flex;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
}

/* LEFT PANEL */
.left-panel {
  width: 42%;
  background: #1a7a5e;
  display: flex;
  flex-direction: column;
  padding: 2rem;
  position: relative;
  overflow: hidden;
}

.back-link {
  color: rgba(255,255,255,0.85);
  font-size: 0.88rem;
  text-decoration: none;
  z-index: 2;
}
.back-link:hover { color: white; }

.left-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  z-index: 2;
  padding-left: 1rem;
}

.left-content h1 {
  font-size: 2.8rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.75rem;
  line-height: 1.1;
}

.left-content p {
  color: rgba(255,255,255,0.75);
  font-size: 0.95rem;
  margin-bottom: 1.2rem;
}

.login-btn {
  display: inline-block;
  padding: 10px 32px;
  border: 2px solid rgba(255,255,255,0.7);
  border-radius: 25px;
  color: white;
  font-size: 0.95rem;
  text-decoration: none;
  transition: background 0.2s;
}
.login-btn:hover { background: rgba(255,255,255,0.15); }

/* Blobs */
.blob {
  position: absolute;
  border-radius: 50%;
  background: rgba(255,255,255,0.08);
}
.b1 { width: 280px; height: 280px; top: -80px; right: -80px; }
.b2 { width: 200px; height: 200px; bottom: 40px; right: 20px; background: rgba(255,255,255,0.05); }
.b3 { width: 140px; height: 140px; bottom: -40px; left: 40px; background: rgba(255,255,255,0.06); }

/* RIGHT PANEL */
.right-panel {
  width: 58%;
  background: white;
  display: flex;
  flex-direction: column;
  padding: 2rem 3rem;
}

.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.help {
  color: #888;
  font-size: 0.88rem;
  text-decoration: none;
}
.help:hover { color: #1a7a5e; }

.form-box {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  max-width: 420px;
  width: 100%;
  margin: 0 auto;
}

.form-box h2 {
  font-size: 1.6rem;
  font-weight: 700;
  color: #1a7a5e;
  margin-bottom: 1.5rem;
  text-align: center;
}

.field {
  margin-bottom: 1rem;
}

.field label {
  display: block;
  font-size: 0.82rem;
  color: #555;
  margin-bottom: 5px;
}

.field input,
.input-wrap input {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid #ddd;
  border-radius: 6px;
  font-size: 0.93rem;
  color: #333;
  outline: none;
  transition: border 0.2s;
  background: white;
}

.field input:focus,
.input-wrap input:focus {
  border-color: #4a4af4;
}

.input-wrap {
  display: flex;
  align-items: center;
  border: 1.5px solid #ddd;
  border-radius: 6px;
  padding-right: 12px;
  transition: border 0.2s;
}
.input-wrap:focus-within { border-color: #4a4af4; }
.input-wrap input { border: none; padding-right: 0; }
.toggle { cursor: pointer; font-size: 1rem; margin-left: 8px; }

.checkbox {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0.8rem 0 1rem;
  font-size: 0.85rem;
  color: #555;
}
.checkbox a { color: #1a7a5e; text-decoration: none; }
.checkbox a:hover { text-decoration: underline; }
.checkbox input[type="checkbox"] { width: 16px; height: 16px; accent-color: #1a7a5e; }

button {
  width: 100%;
  padding: 13px;
  background: #1a7a5e;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, transform 0.1s;
}
button:hover { background: #3333d1; }
button:active { transform: scale(0.98); }
button:disabled { opacity: 0.55; cursor: not-allowed; }

.error {
  color: #c0392b;
  font-size: 0.83rem;
  background: #fde8e8;
  padding: 8px 12px;
  border-radius: 6px;
  margin-bottom: 0.8rem;
}
</style>