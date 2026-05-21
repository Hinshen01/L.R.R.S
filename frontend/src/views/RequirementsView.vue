<template>
  <div class="layout">
    <!-- SIDEBAR (same as dashboard) -->
    <aside class="sidebar">
      <div class="logo">
        <span class="logo-icon">📋</span>
        <span class="logo-text">LRRS</span>
      </div>
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
      <!-- TOPBAR -->
      <div class="topbar">
        <div class="search-wrap">
          <span>🔍</span>
          <input type="text" v-model="search" placeholder="Search requirements..." />
        </div>
        <div class="topbar-right">
          <div class="avatar sm">{{ initials }}</div>
          <div>
            <p class="user-name">{{ fullname }}</p>
            <p class="user-email">{{ email }}</p>
          </div>
        </div>
      </div>

      <div class="content">
        <div class="page-header">
          <div>
            <h1>Requirements</h1>
            <p class="page-sub">Manage your employment documents.</p>
          </div>
          <button class="btn-primary" @click="openModal()">+ Add Requirement</button>
        </div>

        <!-- TABLE -->
        <div class="card">
          <div v-if="loading" class="empty">Loading...</div>
          <table v-else>
            <thead>
              <tr>
                <th>Document</th>
                <th>Status</th>
                <th>Expiration Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="req in filteredReqs" :key="req.id">
                <td>{{ req.document_name }}</td>
                <td>
                  <span :class="['badge', req.status]">{{ formatStatus(req.status) }}</span>
                </td>
                <td>{{ req.expiration_date ? formatDate(req.expiration_date) : '—' }}</td>
                <td class="actions">
                  <button class="btn-edit" @click="openModal(req)">✏️ Edit</button>
                  <button class="btn-delete" @click="deleteReq(req.id)">🗑️ Delete</button>
                </td>
              </tr>
              <tr v-if="filteredReqs.length === 0">
                <td colspan="4" class="empty">No requirements found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL -->
    <div class="modal-overlay" v-if="showModal" @click.self="closeModal">
      <div class="modal">
        <h2>{{ editMode ? 'Edit Requirement' : 'Add Requirement' }}</h2>

        <div class="field">
          <label>Document Name</label>
          <select v-model="form.document_name">
            <option value="">-- Select Document --</option>
            <option>Resume</option>
            <option>Valid ID</option>
            <option>Barangay Clearance</option>
            <option>NBI Clearance</option>
            <option>Transcript of Records</option>
            <option>PSA Birth Certificate</option>
            <option>Certificate of Employment</option>
            <option>Medical Certificate</option>
          </select>
        </div>

        <div class="field">
          <label>Status</label>
          <select v-model="form.status">
            <option value="missing">Missing</option>
            <option value="submitted">Submitted</option>
            <option value="expiring_soon">Expiring Soon</option>
          </select>
        </div>

        <div class="field">
          <label>Expiration Date <span class="optional">(optional)</span></label>
          <input type="date" v-model="form.expiration_date" />
        </div>

        <p v-if="error" class="error">{{ error }}</p>

        <div class="modal-btns">
          <button class="btn-outline" @click="closeModal">Cancel</button>
          <button class="btn-primary" @click="saveReq" :disabled="saving">
            {{ saving ? 'Saving...' : (editMode ? 'Update' : 'Add') }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const fullname = ref('Applicant')
const email = ref('')
const initials = ref('A')
const search = ref('')
const requirements = ref([])
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const editMode = ref(false)
const editId = ref(null)
const error = ref('')

const form = ref({ document_name: '', status: 'missing', expiration_date: '' })

onMounted(async () => {
  const stored = localStorage.getItem('user')
  if (stored) {
    const user = JSON.parse(stored)
    fullname.value = user.name || 'Applicant'
    email.value = user.email || ''
    const parts = fullname.value.trim().split(' ')
    initials.value = parts.length >= 2
      ? parts[0][0].toUpperCase() + parts[1][0].toUpperCase()
      : parts[0][0].toUpperCase()
  }
  await fetchRequirements()
})

async function fetchRequirements() {
  try {
    const token = localStorage.getItem('token')
    const res = await fetch('http://localhost:8000/api/requirements', {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    })
    requirements.value = await res.json()
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

function openModal(req = null) {
  error.value = ''
  if (req) {
    editMode.value = true
    editId.value = req.id
    form.value = {
      document_name: req.document_name,
      status: req.status,
      expiration_date: req.expiration_date || ''
    }
  } else {
    editMode.value = false
    editId.value = null
    form.value = { document_name: '', status: 'missing', expiration_date: '' }
  }
  showModal.value = true
}

function closeModal() { showModal.value = false }

async function saveReq() {
  if (!form.value.document_name) { error.value = 'Please select a document.'; return }
  saving.value = true
  error.value = ''
  try {
    const token = localStorage.getItem('token')
    const url = editMode.value
      ? `http://localhost:8000/api/requirements/${editId.value}`
      : 'http://localhost:8000/api/requirements'
    const method = editMode.value ? 'PUT' : 'POST'
    const res = await fetch(url, {
      method,
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(form.value)
    })
    if (!res.ok) throw new Error('Failed to save')
    await fetchRequirements()
    closeModal()
  } catch (e) {
    error.value = e.message
  } finally {
    saving.value = false
  }
}

async function deleteReq(id) {
  if (!confirm('Are you sure you want to delete this requirement?')) return
  try {
    const token = localStorage.getItem('token')
    await fetch(`http://localhost:8000/api/requirements/${id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` }
    })
    await fetchRequirements()
  } catch (e) {
    console.error(e)
  }
}

const filteredReqs = computed(() =>
  requirements.value.filter(r =>
    r.document_name.toLowerCase().includes(search.value.toLowerCase())
  )
)

function formatStatus(s) {
  if (s === 'submitted') return 'Submitted'
  if (s === 'expiring_soon') return 'Expiring Soon'
  return 'Missing'
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-PH', { month: 'long', day: 'numeric', year: 'numeric' })
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
.logo-icon { font-size: 1.5rem; }
.logo-text { font-size: 1.3rem; font-weight: 800; color: #1a7a5e; }
.menu-label { font-size: 0.7rem; color: #aaa; font-weight: 700; letter-spacing: 1px; margin: 1rem 0 0.5rem; padding-left: 8px; }
nav { display: flex; flex-direction: column; gap: 2px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px; font-size: 0.88rem; color: #555; text-decoration: none; transition: all 0.2s; cursor: pointer; }
.nav-item:hover { background: #f0faf6; color: #1a7a5e; }
.nav-item.router-link-active { background: #1a7a5e; color: white; font-weight: 600; }
.nav-item.logout { color: #c0392b; }
.nav-item.logout:hover { background: #fde8e8; }
.user-card { margin-top: auto; padding: 10px; border-radius: 10px; border: 1px solid #eee; display: flex; align-items: center; gap: 8px; }
.avatar { width: 36px; height: 36px; border-radius: 50%; background: #1a7a5e; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; }
.avatar.sm { width: 32px; height: 32px; }
.user-info { flex: 1; overflow: hidden; }
.user-name { font-size: 0.82rem; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-email { font-size: 0.72rem; color: #999; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; background: white; border-bottom: 1px solid #eee; }
.search-wrap { display: flex; align-items: center; gap: 8px; background: #f5f5f5; padding: 8px 14px; border-radius: 10px; width: 280px; }
.search-wrap input { border: none; outline: none; background: transparent; font-size: 0.9rem; width: 100%; }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.content { flex: 1; overflow-y: auto; padding: 1.5rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.page-header h1 { font-size: 1.6rem; font-weight: 800; color: #1a1a2e; }
.page-sub { color: #888; font-size: 0.88rem; margin-top: 4px; }
.btn-primary { padding: 9px 18px; background: #1a7a5e; color: white; border: none; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; }
.btn-primary:hover { background: #155f49; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-outline { padding: 9px 18px; background: white; color: #1a7a5e; border: 1.5px solid #1a7a5e; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; }
.card { background: white; border-radius: 12px; padding: 1.2rem; border: 1px solid #eee; overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead tr { border-bottom: 2px solid #f0f0f0; }
th { text-align: left; padding: 10px 12px; font-size: 0.8rem; color: #aaa; font-weight: 600; text-transform: uppercase; }
td { padding: 12px; font-size: 0.88rem; color: #333; border-bottom: 1px solid #f5f5f5; }
.badge { font-size: 0.72rem; padding: 4px 10px; border-radius: 20px; font-weight: 500; }
.badge.submitted { background: #e8f5e9; color: #1a7a5e; }
.badge.missing { background: #fde8e8; color: #c0392b; }
.badge.expiring_soon { background: #fef3e2; color: #e67e22; }
.actions { display: flex; gap: 6px; }
.btn-edit { padding: 5px 10px; background: #e8f4fd; color: #2980b9; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer; }
.btn-delete { padding: 5px 10px; background: #fde8e8; color: #c0392b; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer; }
.empty { text-align: center; color: #bbb; font-size: 0.85rem; padding: 2rem; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 999; }
.modal { background: white; border-radius: 16px; padding: 2rem; width: 100%; max-width: 440px; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
.modal h2 { font-size: 1.2rem; font-weight: 700; color: #1a1a2e; margin-bottom: 1.5rem; }
.field { margin-bottom: 1rem; }
.field label { display: block; font-size: 0.83rem; font-weight: 600; color: #444; margin-bottom: 5px; }
.field input, .field select { width: 100%; padding: 10px 12px; border: 1.5px solid #ddd; border-radius: 8px; font-size: 0.93rem; outline: none; }
.field input:focus, .field select:focus { border-color: #1a7a5e; }
.optional { color: #aaa; font-weight: 400; }
.modal-btns { display: flex; gap: 10px; margin-top: 1.5rem; }
.modal-btns .btn-outline, .modal-btns .btn-primary { flex: 1; padding: 11px; }
.error { color: #c0392b; font-size: 0.83rem; background: #fde8e8; padding: 8px 12px; border-radius: 8px; margin-bottom: 0.8rem; }
</style>
