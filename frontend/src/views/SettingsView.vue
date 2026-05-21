<template>
  <div class="layout">
    <aside class="sidebar">
      <div class="logo"><span class="logo-icon">📋</span><span class="logo-text">LRRS</span></div>
      <p class="menu-label">MENU</p>
      <nav>
        <RouterLink to="/dashboard" class="nav-item">🏠 Dashboard</RouterLink>
        <RouterLink to="/requirements" class="nav-item">📄 Requirements</RouterLink>
        <RouterLink to="/interviews" class="nav-item">📅 Interviews</RouterLink>
      </nav>
      <p class="menu-label">GENERAL</p>
      <nav>
        <RouterLink to="/settings" class="nav-item">⚙️ Settings</RouterLink>
        <a class="nav-item" href="#">❓ Help</a>
        <a class="nav-item logout" @click="logout" href="#">🚪 Logout</a>
      </nav>
      <div class="user-card">
        <div class="avatar">{{ initials }}</div>
        <div class="user-info">
          <p class="user-name">{{ fullname }}</p>
          <p class="user-email">{{ email }}</p>
        </div>
      </div>
    </aside>

    <div class="main">
      <div class="topbar">
        <h2 style="font-size:1rem;color:#333;">Settings</h2>
        <div class="topbar-right">
          <div class="avatar sm">{{ initials }}</div>
          <div><p class="user-name">{{ fullname }}</p><p class="user-email">{{ email }}</p></div>
        </div>
      </div>

      <div class="content">
        <h1>Settings</h1>
        <p class="page-sub">Manage your account information.</p>

        <div class="card">
          <h2>Profile Information</h2>
          <div class="field">
            <label>Full Name</label>
            <input v-model="form.name" type="text" />
          </div>
          <div class="field">
            <label>Email</label>
            <input v-model="form.email" type="email" />
          </div>
          <div class="field">
            <label>Contact Number</label>
            <input v-model="form.contact_number" type="text" maxlength="11" />
          </div>
          <div class="field">
            <label>Address</label>
            <input v-model="form.address" type="text" />
          </div>
          <p v-if="success" class="success">{{ success }}</p>
          <p v-if="error" class="error">{{ error }}</p>
          <button class="btn-primary" @click="saveProfile" :disabled="saving">
            {{ saving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>

        <div class="card" style="margin-top:1rem;">
          <h2>Change Password</h2>
          <div class="field">
            <label>New Password</label>
            <input v-model="password.new" type="password" placeholder="••••••••" />
          </div>
          <div class="field">
            <label>Confirm Password</label>
            <input v-model="password.confirm" type="password" placeholder="••••••••" />
          </div>
          <p v-if="passError" class="error">{{ passError }}</p>
          <p v-if="passSuccess" class="success">{{ passSuccess }}</p>
          <button class="btn-primary" @click="changePassword" :disabled="savingPass">
            {{ savingPass ? 'Updating...' : 'Update Password' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const fullname = ref('Applicant')
const email = ref('')
const initials = ref('A')
const saving = ref(false)
const savingPass = ref(false)
const success = ref('')
const error = ref('')
const passError = ref('')
const passSuccess = ref('')

const form = ref({ name: '', email: '', contact_number: '', address: '' })
const password = ref({ new: '', confirm: '' })

onMounted(() => {
  const stored = localStorage.getItem('user')
  if (stored) {
    const user = JSON.parse(stored)
    fullname.value = user.name || 'Applicant'
    email.value = user.email || ''
    form.value.name = user.name || ''
    form.value.email = user.email || ''
    form.value.contact_number = user.contact_number || ''
    form.value.address = user.address || ''
    const parts = fullname.value.trim().split(' ')
    initials.value = parts.length >= 2
      ? parts[0][0].toUpperCase() + parts[1][0].toUpperCase()
      : parts[0][0].toUpperCase()
  }
})

async function saveProfile() {
  saving.value = true
  error.value = ''
  success.value = ''
  try {
    const token = localStorage.getItem('token')
    const res = await fetch('http://localhost:8000/api/profile', {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(form.value)
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Failed to save')
    localStorage.setItem('user', JSON.stringify(data))
    fullname.value = data.name
    email.value = data.email
    success.value = 'Profile updated successfully!'
  } catch (e) {
    error.value = e.message
  } finally {
    saving.value = false
  }
}

async function changePassword() {
  passError.value = ''
  passSuccess.value = ''
  if (password.value.new !== password.value.confirm) {
    passError.value = 'Passwords do not match.'
    return
  }
  if (password.value.new.length < 6) {
    passError.value = 'Password must be at least 6 characters.'
    return
  }
  savingPass.value = true
  try {
    const token = localStorage.getItem('token')
    const res = await fetch('http://localhost:8000/api/password', {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ password: password.value.new })
    })
    if (!res.ok) throw new Error('Failed to update password')
    passSuccess.value = 'Password updated successfully!'
    password.value = { new: '', confirm: '' }
  } catch (e) {
    passError.value = e.message
  } finally {
    savingPass.value = false
  }
}

function logout() {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }
.layout { display: flex; height: 100vh; background: #f0f2f5; overflow: hidden; }
.sidebar { width: 220px; background: white; display: flex; flex-direction: column; padding: 1.5rem 1rem; border-right: 1px solid #eee; flex-shrink: 0; }
.logo { display: flex; align-items: center; gap: 8px; margin-bottom: 1.8rem; }
.logo-icon { font-size: 1.5rem; } .logo-text { font-size: 1.3rem; font-weight: 800; color: #1a7a5e; }
.menu-label { font-size: 0.7rem; color: #aaa; font-weight: 700; letter-spacing: 1px; margin: 1rem 0 0.5rem; padding-left: 8px; }
nav { display: flex; flex-direction: column; gap: 2px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px; font-size: 0.88rem; color: #555; text-decoration: none; transition: all 0.2s; cursor: pointer; }
.nav-item:hover { background: #f0faf6; color: #1a7a5e; }
.nav-item.router-link-active { background: #1a7a5e; color: white; font-weight: 600; }
.nav-item.logout { color: #c0392b; } .nav-item.logout:hover { background: #fde8e8; }
.user-card { margin-top: auto; padding: 10px; border-radius: 10px; border: 1px solid #eee; display: flex; align-items: center; gap: 8px; }
.avatar { width: 36px; height: 36px; border-radius: 50%; background: #1a7a5e; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; }
.avatar.sm { width: 32px; height: 32px; }
.user-info { flex: 1; overflow: hidden; }
.user-name { font-size: 0.82rem; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-email { font-size: 0.72rem; color: #999; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; background: white; border-bottom: 1px solid #eee; }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.content { flex: 1; overflow-y: auto; padding: 1.5rem; }
.content h1 { font-size: 1.6rem; font-weight: 800; color: #1a1a2e; margin-bottom: 4px; }
.page-sub { color: #888; font-size: 0.88rem; margin-bottom: 1.5rem; }
.card { background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #eee; max-width: 600px; }
.card h2 { font-size: 1rem; font-weight: 700; color: #1a1a2e; margin-bottom: 1.2rem; }
.field { margin-bottom: 1rem; }
.field label { display: block; font-size: 0.83rem; font-weight: 600; color: #444; margin-bottom: 5px; }
.field input { width: 100%; padding: 10px 12px; border: 1.5px solid #ddd; border-radius: 8px; font-size: 0.93rem; outline: none; }
.field input:focus { border-color: #1a7a5e; }
.btn-primary { padding: 10px 24px; background: #1a7a5e; color: white; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; margin-top: 0.5rem; }
.btn-primary:hover { background: #155f49; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.success { color: #1a7a5e; font-size: 0.83rem; background: #e8f5e9; padding: 8px 12px; border-radius: 8px; margin-bottom: 0.8rem; }
.error { color: #c0392b; font-size: 0.83rem; background: #fde8e8; padding: 8px 12px; border-radius: 8px; margin-bottom: 0.8rem; }
</style>
