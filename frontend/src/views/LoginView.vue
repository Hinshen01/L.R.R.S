<template>
  <div class="page">

    <!-- Left Panel -->
    <div class="left-panel">
      <div class="brand">
        <div class="logo">LRRS</div>
        <p>Lost Requirement Reminder System</p>
      </div>
      <div class="illustration">
        <div class="circle c1"></div>
        <div class="circle c2"></div>
        <div class="circle c3"></div>
        <div class="icon-box">📋</div>
      </div>
      <p class="tagline">"Stay prepared. Never miss a requirement."</p>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
      <div class="form-box">
        <h1>Welcome Back!</h1>
        <p class="sub">Sign in to your LRRS account</p>

        <div class="field">
          <label>Email Address</label>
          <div class="input-wrap">
            <span class="icon">✉️</span>
            <input v-model="form.email" type="email" placeholder="example@gmail.com" />
          </div>
        </div>

        <div class="field">
          <label>Password</label>
          <div class="input-wrap">
            <span class="icon">🔒</span>
            <input v-model="form.password" :type="showPass ? 'text' : 'password'" placeholder="********" />
            <span class="toggle" @click="showPass = !showPass">{{ showPass ? '⊘' : '👁' }}</span>
          </div>
        </div>

        <p v-if="error" class="error">{{ error }}</p>

        <button @click="login" :disabled="loading">
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>

        <div class="divider"><span>or</span></div>

        <p class="register-link">
          Don't have an account?
          <RouterLink to="/register">Create one here</RouterLink>
        </p>
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
const form = ref({ email: '', password: '' })

async function login() {
  loading.value = true
  error.value = ''
  try {
    const res = await fetch('http://localhost:8000/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(form.value)
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Login failed')
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
  width: 45%;
  background: linear-gradient(135deg, #1a7a5e 0%, #0f4d3a 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  gap: 2rem;
  position: relative;
  overflow: hidden;
}

.brand .logo {
  font-size: 3rem;
  font-weight: 900;
  color: white;
  letter-spacing: 4px;
}

.brand p {
  color: rgba(255,255,255,0.7);
  font-size: 0.9rem;
  margin-top: 6px;
  text-align: center;
}

.illustration {
  position: relative;
  width: 200px;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.circle {
  position: absolute;
  border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.15);
}
.c1 { width: 200px; height: 200px; }
.c2 { width: 150px; height: 150px; }
.c3 { width: 100px; height: 100px; background: rgba(255,255,255,0.05); }

.icon-box {
  font-size: 4rem;
  z-index: 1;
}

.tagline {
  color: rgba(255,255,255,0.6);
  font-size: 0.85rem;
  text-align: center;
  font-style: italic;
  max-width: 260px;
}

/* Decorative blobs */
.left-panel::before {
  content: '';
  position: absolute;
  width: 300px;
  height: 300px;
  border-radius: 50%;
  background: rgba(255,255,255,0.04);
  top: -100px;
  right: -100px;
}
.left-panel::after {
  content: '';
  position: absolute;
  width: 200px;
  height: 200px;
  border-radius: 50%;
  background: rgba(255,255,255,0.04);
  bottom: -60px;
  left: -60px;
}

/* RIGHT PANEL */
.right-panel {
  width: 55%;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
}

.form-box {
  width: 100%;
  max-width: 420px;
  background: white;
  padding: 2.5rem;
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
}

.form-box h1 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 6px;
}

.sub {
  color: #888;
  font-size: 0.9rem;
  margin-bottom: 1.8rem;
}

.field {
  margin-bottom: 1.2rem;
}

.field label {
  display: block;
  font-size: 0.83rem;
  font-weight: 600;
  color: #444;
  margin-bottom: 6px;
}

.input-wrap {
  display: flex;
  align-items: center;
  border: 1.5px solid #e0e0e0;
  border-radius: 10px;
  padding: 0 12px;
  background: #fafafa;
  transition: border 0.2s;
}

.input-wrap:focus-within {
  border-color: #1a7a5e;
  background: white;
}

.input-wrap .icon {
  font-size: 1rem;
  margin-right: 8px;
}

.input-wrap input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  padding: 12px 0;
  font-size: 0.95rem;
  color: #333;
}

.input-wrap .toggle {
  cursor: pointer;
  font-size: 1rem;
  margin-left: 8px;
}

button {
  width: 100%;
  padding: 13px;
  background: #1a7a5e;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  margin-top: 0.5rem;
  transition: background 0.2s, transform 0.1s;
}

button:hover { background: #155f49; }
button:active { transform: scale(0.98); }
button:disabled { opacity: 0.6; cursor: not-allowed; }

.divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 1.2rem 0;
  color: #ccc;
  font-size: 0.85rem;
}
.divider::before, .divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #eee;
}

.register-link {
  text-align: center;
  font-size: 0.88rem;
  color: #666;
}
.register-link a {
  color: #1a7a5e;
  font-weight: 600;
  text-decoration: none;
}
.register-link a:hover { text-decoration: underline; }

.error {
  color: #c0392b;
  font-size: 0.83rem;
  background: #fde8e8;
  padding: 8px 12px;
  border-radius: 8px;
  margin-bottom: 0.8rem;
}
</style>
