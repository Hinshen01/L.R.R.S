<template>
  <div class="layout">
    <aside class="sidebar">
      <h2>LRRS</h2>
      <nav>
        <RouterLink to="/dashboard">Dashboard</RouterLink>
        <RouterLink to="/requirements">Requirements</RouterLink>
        <RouterLink to="/interviews">Interviews</RouterLink>
      </nav>
      <button class="logout" @click="logout">Logout</button>
    </aside>

    <main class="main">
      <h1>Welcome back!</h1>

      <div class="cards">
        <div class="stat-card">
          <span class="label">Documents Submitted</span>
          <span class="value green">3</span>
        </div>
        <div class="stat-card">
          <span class="label">Missing Documents</span>
          <span class="value red">2</span>
        </div>
        <div class="stat-card">
          <span class="label">Expiring Soon</span>
          <span class="value orange">1</span>
        </div>
      </div>

      <div class="section">
        <h2>Requirement Status</h2>
        <ul class="req-list">
          <li>✅ Resume</li>
          <li>✅ Barangay Clearance</li>
          <li>✅ PSA Birth Certificate</li>
          <li>❌ Valid ID <span class="badge missing">Missing</span></li>
          <li>❌ Transcript of Records <span class="badge missing">Missing</span></li>
          <li>⚠️ NBI Clearance <span class="badge expiring">Expiring Soon</span></li>
        </ul>
      </div>

      <div class="section">
        <h2>Upcoming Interview</h2>
        <div class="interview-card">
          <p>📅 <strong>May 20, 2026</strong> — 10:00 AM</p>
          <p>🏢 Company: <strong>PESO Pasay</strong></p>
          <p>📍 Venue: City Hall, Room 3</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
function logout() {
  localStorage.removeItem('token')
  router.push('/login')
}
</script>

<style scoped>
.layout { display: flex; min-height: 100vh; }
.sidebar { width: 200px; background: #1a7a5e; color: white; padding: 1.5rem 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
.sidebar h2 { font-size: 1.4rem; margin: 0 0 1.5rem; letter-spacing: 1px; }
.sidebar nav { display: flex; flex-direction: column; gap: 0.5rem; flex: 1; }
.sidebar nav a { color: rgba(255,255,255,0.8); text-decoration: none; padding: 8px 12px; border-radius: 8px; font-size: 0.9rem; }
.sidebar nav a:hover, .sidebar nav a.router-link-active { background: rgba(255,255,255,0.15); color: white; }
.logout { background: rgba(255,255,255,0.15); color: white; border: none; padding: 9px; border-radius: 8px; cursor: pointer; font-size: 0.9rem; }
.main { flex: 1; padding: 2rem; background: #f5f5f0; }
h1 { font-size: 1.6rem; margin: 0 0 1.5rem; color: #222; }
h2 { font-size: 1.1rem; color: #333; margin: 0 0 0.75rem; }
.cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
.stat-card { background: white; padding: 1.25rem; border-radius: 10px; display: flex; flex-direction: column; gap: 6px; }
.label { font-size: 0.8rem; color: #888; }
.value { font-size: 1.8rem; font-weight: 700; }
.green { color: #1a7a5e; } .red { color: #c0392b; } .orange { color: #e67e22; }
.section { background: white; padding: 1.25rem; border-radius: 10px; margin-bottom: 1rem; }
.req-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
.req-list li { font-size: 0.95rem; display: flex; align-items: center; gap: 8px; }
.badge { font-size: 0.75rem; padding: 2px 8px; border-radius: 20px; }
.missing { background: #fde8e8; color: #c0392b; }
.expiring { background: #fef3e2; color: #e67e22; }
.interview-card { font-size: 0.95rem; line-height: 1.9; color: #444; }
</style>