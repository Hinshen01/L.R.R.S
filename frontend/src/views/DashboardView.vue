<template>
  <div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="logo">
        <span class="logo-icon">📋</span>
        <span class="logo-text">LRRS</span>
      </div>

      <p class="menu-label">MENU</p>
      <nav>
        <RouterLink to="/dashboard" class="nav-item">
          <span>🏠</span> Dashboard
        </RouterLink>
        <RouterLink to="/requirements" class="nav-item">
          <span>📄</span> Requirements
        </RouterLink>
        <RouterLink to="/interviews" class="nav-item">
          <span>📅</span> Interviews
        </RouterLink>
      </nav>

      <p class="menu-label">GENERAL</p>
      <nav>
        <RouterLink to="/settings" class="nav-item">
          <span>⚙️</span> Settings
        </RouterLink>
        <a class="nav-item" href="#">
          <span>❓</span> Help
        </a>
        <a class="nav-item logout" @click="logout" href="#">
          <span>🚪</span> Logout
        </a>
      </nav>

      <!-- User Card -->
      <div class="user-card" @click="toggleMenu" ref="menuRef">
        <div class="avatar">{{ initials }}</div>
        <div class="user-info">
          <p class="user-name">{{ fullname }}</p>
          <p class="user-email">{{ email }}</p>
        </div>
        <span class="chevron">{{ menuOpen ? '▲' : '▼' }}</span>

        <div class="dropdown" v-if="menuOpen">
          <p class="dropdown-name">{{ fullname }}</p>
          <hr />
          <button @click.stop="goToSettings">⚙️ Settings</button>
          <button @click.stop="changeLanguage">🌐 Language</button>
          <button @click.stop="logout" class="logout-btn">🚪 Log out</button>
        </div>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main">

      <!-- TOP BAR -->
      <div class="topbar">
        <div class="search-wrap">
          <span>🔍</span>
          <input type="text" v-model="search" placeholder="Search requirements..." />
        </div>
        <div class="topbar-right">
          <button class="icon-btn">✉️</button>
          <button class="icon-btn">🔔</button>
          <div class="top-user">
            <div class="avatar sm">{{ initials }}</div>
            <div>
              <p class="user-name">{{ fullname }}</p>
              <p class="user-email">{{ email }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- CONTENT -->
      <div class="content">
        <div class="page-header">
          <div>
            <h1>Dashboard</h1>
            <p class="page-sub">Track and manage your employment requirements.</p>
          </div>
        </div>

        <!-- STAT CARDS -->
        <div class="stats-grid">
          <div class="stat-card green">
            <div class="stat-top">
              <p class="stat-label">Documents Submitted</p>
              <span class="stat-arrow">↗</span>
            </div>
            <p class="stat-value">{{ submitted }}</p>
            <p class="stat-note">✅ Completed requirements</p>
          </div>
          <div class="stat-card">
            <div class="stat-top">
              <p class="stat-label">Missing Documents</p>
              <span class="stat-arrow">↗</span>
            </div>
            <p class="stat-value">{{ missing }}</p>
            <p class="stat-note">❌ Need to submit</p>
          </div>
          <div class="stat-card">
            <div class="stat-top">
              <p class="stat-label">Upcoming Interviews</p>
              <span class="stat-arrow">↗</span>
            </div>
            <p class="stat-value">{{ interviewCount }}</p>
            <p class="stat-note">📅 Scheduled</p>
          </div>
        </div>

        <!-- BOTTOM GRID -->
        <div class="bottom-grid">

          <!-- Requirements List -->
          <div class="card req-card">
            <div class="card-header">
              <h2>Requirement Status</h2>
              <RouterLink to="/requirements" class="see-all">See all </RouterLink>
            </div>
            <div v-if="loadingReqs" class="loading">Loading...</div>
            <ul class="req-list" v-else>
              <li v-for="req in filteredRequirements" :key="req.id" class="req-item">
                <span class="req-icon">
                  {{ req.status === 'submitted' ? '✅' : '❌' }}
                </span>
                <span class="req-name">{{ req.document_name }}</span>
                <span :class="['badge', req.status]">{{ formatStatus(req.status) }}</span>
              </li>
              <li v-if="filteredRequirements.length === 0" class="empty">No requirements found.</li>
            </ul>
          </div>

          <!-- Upcoming Interview -->
          <div class="card interview-card">
            <div class="card-header">
              <h2>Upcoming Interview</h2>
              <RouterLink to="/interviews" class="see-all">See all </RouterLink>
            </div>
            <div v-if="loadingInterviews" class="loading">Loading...</div>
            <div v-else-if="nextInterview" class="interview-detail">
              <div class="interview-badge">
                <span>📅</span>
                <div>
                  <p class="int-company">{{ nextInterview.company_name }}</p>
                  <p class="int-time">{{ formatDate(nextInterview.interview_date) }}</p>
                </div>
              </div>
              <div class="int-info">
                <p>📍 <strong>Venue:</strong> {{ nextInterview.venue }}</p>
                <p>🕐 <strong>Time:</strong> {{ formatTime(nextInterview.interview_date) }}</p>
                <p>
                  <span :class="['badge', nextInterview.status]">
                    {{ nextInterview.status }}
                  </span>
                </p>
              </div>
              <button class="btn-primary full" @click="goToInterviews">View Details</button>
            </div>
            <div v-else class="empty">No upcoming interviews scheduled.</div>
          </div>

          <!-- Recent Activity -->
          <div class="card activity-card">
            <div class="card-header">
              <h2>Requirement Summary</h2>
            </div>
            <div class="progress-wrap">
              <div class="progress-item">
                <div class="progress-label">
                  <span>Submitted</span>
                  <span class="green-text">{{ submittedPercent }}%</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-fill green" :style="{ width: submittedPercent + '%' }"></div>
                </div>
              </div>
              <div class="progress-item">
                <div class="progress-label">
                  <span>Missing</span>
                  <span class="red-text">{{ missingPercent }}%</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-fill red" :style="{ width: missingPercent + '%' }"></div>
                </div>
              </div>
            </div>
            <div class="donut-wrap">
              <svg viewBox="0 0 100 100" class="donut">
                <circle cx="50" cy="50" r="35" fill="none" stroke="#e8f5e9" stroke-width="12"/>
                <circle cx="50" cy="50" r="35" fill="none" stroke="#1a7a5e" stroke-width="12"
                  stroke-dasharray="220" :stroke-dashoffset="220 - (submittedPercent / 100 * 220)"
                  stroke-linecap="round" transform="rotate(-90 50 50)"/>
              </svg>
              <div class="donut-label">
                <p class="donut-pct">{{ submittedPercent }}%</p>
                <p class="donut-sub">Complete</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const menuOpen = ref(false)
const menuRef = ref(null)
const fullname = ref('Applicant')
const email = ref('')
const initials = ref('A')
const search = ref('')
const requirements = ref([])
const interviews = ref([])
const loadingReqs = ref(true)
const loadingInterviews = ref(true)

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
  document.addEventListener('click', handleOutsideClick)
  await fetchRequirements()
  await fetchInterviews()
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleOutsideClick)
})

async function fetchRequirements() {
  try {
    const token = localStorage.getItem('token')
    const res = await fetch('http://localhost:8000/api/requirements', {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    })
    const data = await res.json()
    requirements.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loadingReqs.value = false
  }
}

async function fetchInterviews() {
  try {
    const token = localStorage.getItem('token')
    const res = await fetch('http://localhost:8000/api/interviews', {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    })
    const data = await res.json()
    interviews.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loadingInterviews.value = false
  }
}

const filteredRequirements = computed(() =>
  requirements.value.filter(r =>
    r.document_name.toLowerCase().includes(search.value.toLowerCase())
  )
)

const submitted = computed(() => requirements.value.filter(r => r.status === 'submitted').length)
const missing = computed(() => requirements.value.filter(r => r.status === 'missing').length)
const interviewCount = computed(() => interviews.value.filter(i => i.status === 'scheduled').length)
const nextInterview = computed(() => interviews.value.find(i => i.status === 'scheduled') || null)

const total = computed(() => requirements.value.length || 1)
const submittedPercent = computed(() => Math.round((submitted.value / total.value) * 100))
const missingPercent = computed(() => Math.round((missing.value / total.value) * 100))

function formatStatus(s) {
  if (s === 'submitted') return 'Submitted'
  return 'Missing'
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-PH', { month: 'long', day: 'numeric', year: 'numeric' })
}

function formatTime(d) {
  return new Date(d).toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' })
}

function handleOutsideClick(e) {
  if (menuRef.value && !menuRef.value.contains(e.target)) menuOpen.value = false
}

function toggleMenu() { menuOpen.value = !menuOpen.value }
function goToSettings() { menuOpen.value = false; router.push('/settings') }
function changeLanguage() { menuOpen.value = false; alert('Language options coming soon!') }
function goToInterviews() { router.push('/interviews') }

function logout() {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.layout { display: flex; height: 100vh; background: #f0f2f5; overflow: hidden; }

/* SIDEBAR */
.sidebar {
  width: 220px;
  background: white;
  display: flex;
  flex-direction: column;
  padding: 1.5rem 1rem;
  border-right: 1px solid #eee;
  flex-shrink: 0;
}

.logo { display: flex; align-items: center; gap: 8px; margin-bottom: 1.8rem; }
.logo-icon { font-size: 1.5rem; }
.logo-text { font-size: 1.3rem; font-weight: 800; color: #1a7a5e; }

.menu-label { font-size: 0.7rem; color: #aaa; font-weight: 700; letter-spacing: 1px; margin: 1rem 0 0.5rem; padding-left: 8px; }

nav { display: flex; flex-direction: column; gap: 2px; }

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 8px;
  font-size: 0.88rem;
  color: #555;
  text-decoration: none;
  transition: all 0.2s;
  cursor: pointer;
}
.nav-item:hover { background: #f0faf6; color: #1a7a5e; }
.nav-item.router-link-active { background: #1a7a5e; color: white; font-weight: 600; }
.nav-item.logout { color: #c0392b; }
.nav-item.logout:hover { background: #fde8e8; }

.user-card {
  margin-top: auto;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #eee;
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background 0.2s;
}
.user-card:hover { background: #f9f9f9; }

/* TOPBAR */
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  background: white;
  border-bottom: 1px solid #eee;
}

.search-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f5f5f5;
  padding: 8px 14px;
  border-radius: 10px;
  width: 280px;
}
.search-wrap input { border: none; outline: none; background: transparent; font-size: 0.9rem; width: 100%; }

.topbar-right { display: flex; align-items: center; gap: 16px; justify-content: flex-end; }
.icon-btn { background: #f5f5f5; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 1rem; transition: background 0.2s; display: flex; align-items: center; justify-content: center; }
.icon-btn:hover { background: #e8e8e8; }

.top-user { display: flex; align-items: center; gap: 12px; }

/* AVATAR */
.avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: #1a7a5e; color: white;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.8rem; font-weight: 700; flex-shrink: 0;
}
.avatar.sm { width: 32px; height: 32px; font-size: 0.75rem; }

.user-info { flex: 1; overflow: hidden; }
.user-name { font-size: 0.82rem; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-email { font-size: 0.72rem; color: #999; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.chevron { font-size: 0.6rem; color: #aaa; }

/* DROPDOWN */
.dropdown {
  position: absolute; bottom: 54px; left: 0; right: 0;
  background: white; border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.12);
  overflow: hidden; z-index: 100;
}
.dropdown-name { font-size: 0.78rem; color: #888; padding: 10px 14px 6px; font-weight: 600; }
.dropdown hr { border: none; border-top: 1px solid #eee; }
.dropdown button { width: 100%; text-align: left; background: none; border: none; padding: 10px 14px; font-size: 0.85rem; color: #333; cursor: pointer; display: block; }
.dropdown button:hover { background: #f5f5f0; }
.logout-btn { color: #c0392b !important; }

/* MAIN */
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

.content { flex: 1; overflow-y: auto; padding: 1.5rem; }

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 2rem; }
.page-header > div:first-child { flex: 1; }
.page-header h1 { font-size: 1.8rem; font-weight: 800; color: #1a1a2e; margin: 0; }
.page-sub { color: #888; font-size: 0.88rem; margin-top: 6px; }

.btn-primary { padding: 10px 22px; background: #1a7a5e; color: white; border: none; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: background 0.2s; white-space: nowrap; }
.btn-primary:hover { background: #155f49; }
.btn-primary.full { width: 100%; margin-top: 1rem; padding: 11px; }
.btn-outline { padding: 10px 22px; background: white; color: #1a7a5e; border: 1.5px solid #1a7a5e; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: background 0.2s; white-space: nowrap; }
.btn-outline:hover { background: #f0faf6; }

/* STAT CARDS */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.2rem; margin-bottom: 2rem; }

.stat-card {
  background: white; border-radius: 12px; padding: 1.2rem;
  border: 1px solid #eee; transition: box-shadow 0.2s;
}
.stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); }
.stat-card.green { background: #1a7a5e; border-color: #1a7a5e; }
.stat-card.green .stat-label { color: rgba(255,255,255,0.8); }
.stat-card.green .stat-value { color: white; }
.stat-card.green .stat-note { color: rgba(255,255,255,0.7); }
.stat-card.green .stat-arrow { color: white; border-color: rgba(255,255,255,0.4); }

.stat-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.stat-label { font-size: 0.8rem; color: #888; font-weight: 500; }
.stat-arrow { font-size: 0.75rem; border: 1px solid #ddd; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; color: #666; }
.stat-value { font-size: 2rem; font-weight: 800; color: #1a1a2e; margin-bottom: 4px; }
.stat-note { font-size: 0.75rem; color: #aaa; }

/* BOTTOM GRID */
.bottom-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.2rem; }

.card { background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #eee; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; }
.card-header h2 { font-size: 1.05rem; font-weight: 700; color: #1a1a2e; margin: 0; }
.see-all { font-size: 0.82rem; color: #1a7a5e; text-decoration: none; font-weight: 500; cursor: pointer; }
.see-all:hover { text-decoration: underline; }

/* REQ LIST */
.req-list { list-style: none; display: flex; flex-direction: column; gap: 12px; }
.req-item { display: flex; align-items: center; gap: 10px; font-size: 0.9rem; padding: 8px 0; }
.req-icon { font-size: 1.1rem; }
.req-name { flex: 1; color: #333; font-weight: 500; }
.badge { font-size: 0.72rem; padding: 3px 10px; border-radius: 20px; font-weight: 500; }
.badge.submitted { background: #e8f5e9; color: #1a7a5e; }
.badge.missing { background: #fde8e8; color: #c0392b; }
.badge.scheduled { background: #e8f4fd; color: #2980b9; }

/* INTERVIEW */
.interview-badge { display: flex; align-items: flex-start; gap: 12px; background: #f0faf6; border-radius: 10px; padding: 12px; margin-bottom: 1rem; }
.int-company { font-weight: 700; font-size: 0.95rem; color: #1a1a2e; }
.int-time { font-size: 0.8rem; color: #888; margin-top: 2px; }
.int-info { display: flex; flex-direction: column; gap: 6px; font-size: 0.85rem; color: #555; margin-bottom: 0.5rem; }

/* PROGRESS */
.progress-wrap { display: flex; flex-direction: column; gap: 14px; margin-bottom: 1.2rem; }
.progress-item { padding: 4px 0; }
.progress-label { display: flex; justify-content: space-between; font-size: 0.82rem; color: #666; margin-bottom: 5px; }
.progress-bar { height: 7px; background: #f0f0f0; border-radius: 10px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 10px; transition: width 0.5s ease; }
.progress-fill.green { background: #1a7a5e; }
.progress-fill.red { background: #e74c3c; }
.progress-fill.orange { background: #e67e22; }
.green-text { color: #1a7a5e; font-weight: 600; }
.red-text { color: #e74c3c; font-weight: 600; }
.orange-text { color: #e67e22; font-weight: 600; }

/* DONUT */
.donut-wrap { position: relative; width: 120px; height: 120px; margin: 0 auto; }
.donut { width: 100%; height: 100%; }
.donut-label { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; }
.donut-pct { font-size: 1.3rem; font-weight: 800; color: #1a7a5e; }
.donut-sub { font-size: 0.7rem; color: #aaa; }

.loading { text-align: center; color: #aaa; font-size: 0.88rem; padding: 2rem 1rem; }
.empty { text-align: center; color: #bbb; font-size: 0.85rem; padding: 2rem 1rem; }
</style>