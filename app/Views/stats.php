<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FoodSwipe — Mes stats</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<div class="app-page">

  <!-- Top Bar -->
  <div class="topbar">
    <span class="topbar-logo"><span>🍽️</span>FoodSwipe</span>
    <div class="topbar-actions">
      <a href="/stats/logout" title="Se déconnecter">🚪</a>
    </div>
  </div>

  <!-- Stats Header -->
  <div class="stats-header">
    <h2>Mes statistiques 📊</h2>
    <p id="stats-subtitle">Vos plats préférés en un coup d'œil</p>
  </div>

  <!-- Stats Body -->
  <div class="stats-body">

    <!-- KPIs -->
    <div class="kpi-row">
      <div class="kpi-card">
        <span class="kpi-icon">❤️</span>
        <div class="kpi-value" id="kpi-liked">0</div>
        <div class="kpi-label">Aimés</div>
      </div>
      <div class="kpi-card">
        <span class="kpi-icon">👀</span>
        <div class="kpi-value" id="kpi-seen">0</div>
        <div class="kpi-label">Vus</div>
      </div>
      <div class="kpi-card">
        <span class="kpi-icon">⭐</span>
        <div class="kpi-value" id="kpi-super">0</div>
        <div class="kpi-label">Super Like</div>
      </div>
    </div>

    <!-- Category Bar Chart -->
    <div class="section-title">🥗 Répartition par catégorie</div>
    <div class="bar-chart" id="category-chart">
      <p class="empty-placeholder">Chargement...</p>
    </div>

    <!-- Donut -->
    <div class="section-title">📈 Taux d'appréciation</div>
    <div class="donut-wrap">
      <svg viewBox="0 0 80 80" width="90" height="90" style="flex-shrink:0">
        <circle cx="40" cy="40" r="30" fill="none" stroke="#F0F0F0" stroke-width="12"/>
        <circle cx="40" cy="40" r="30" fill="none" stroke="url(#grad)" stroke-width="12"
                stroke-dasharray="0 188.5" stroke-linecap="round"
                transform="rotate(-90 40 40)" id="donut-arc"/>
        <defs>
          <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%"   stop-color="#FF6B6B"/>
            <stop offset="100%" stop-color="#FF8E53"/>
          </linearGradient>
        </defs>
        <text x="40" y="44" text-anchor="middle" font-size="14" font-weight="800"
              fill="#2D3748" id="donut-pct">0%</text>
      </svg>
      <div class="donut-legend">
        <div class="legend-item">
          <div class="legend-dot" style="background:#FF6B6B"></div>Aimés
        </div>
        <div class="legend-item">
          <div class="legend-dot" style="background:#EEE"></div>Passés
        </div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px" id="donut-label">
          Swipez des plats<br>pour voir vos stats
        </div>
      </div>
    </div>

    <!-- Liked List -->
    <div class="section-title">💖 Plats aimés</div>
    <div class="liked-list" id="liked-list">
      <div class="empty-placeholder">Chargement...</div>
    </div>

  </div>

  <!-- Bottom Nav -->
  <div class="bottom-nav">
    <a href="/home">
      <span class="nav-icon">🔥</span>Découvrir
    </a>
    <a href="/add-food">
      <span class="nav-icon">➕</span>Ajouter
    </a>
    <a href="/stats" class="active">
      <span class="nav-icon">📊</span>Mes stats
    </a>
  </div>

</div>

<script>
  // Couleurs pour les catégories
  const CAT_COLORS = [
    '#FF6B6B','#FF8E53','#FFC371','#4ECDC4','#45B7D1',
    '#96CEB4','#DDA0DD','#FF69B4','#20B2AA','#9370DB','#F08080','#3CB371',
  ];

  // Charger les statistiques depuis le serveur
  async function loadStats() {
    try {
      const response = await fetch('/stats/getStatsData');
      const result = await response.json();

      if (!result.success) {
        window.location.href = '/login';
        return;
      }

      const data = result.data;
      
      // Mettre à jour les KPIs
      document.getElementById('kpi-liked').textContent = data.kpis.liked;
      document.getElementById('kpi-seen').textContent = data.kpis.seen;
      document.getElementById('kpi-super').textContent = data.kpis.super;

      // Mettre à jour le donut
      updateDonut(data.percentage, data.kpis.liked, data.kpis.seen);

      // Mettre à jour le graphique des catégories
      updateCategoryChart(data.categoryStats, data.allFoods);

      // Mettre à jour la liste des plats aimés
      updateLikedList(data.likedFoods, data.superIds, data.allFoods);

    } catch (error) {
      console.error('Erreur lors du chargement des stats:', error);
    }
  }

  function updateDonut(pct, likedCount, total) {
    const circ = 2 * Math.PI * 30;
    document.getElementById('donut-arc').setAttribute(
      'stroke-dasharray', `${circ * pct / 100} ${circ}`
    );
    document.getElementById('donut-pct').textContent = pct + '%';
    document.getElementById('donut-label').innerHTML = total > 0
      ? `<strong>${likedCount}</strong> aimé${likedCount > 1 ? 's' : ''} sur <strong>${total}</strong> vus`
      : 'Swipez des plats<br>pour voir vos stats';
  }

  function updateCategoryChart(categoryStats, allFoods) {
    const cats = Object.entries(categoryStats).sort((a, b) => b[1] - a[1]);
    const max = cats.length ? cats[0][1] : 1;
    const chart = document.getElementById('category-chart');

    if (cats.length === 0) {
      chart.innerHTML = '<p class="empty-placeholder">Aucune donnée encore</p>';
      return;
    }

    const allCats = [...new Set(allFoods.map(f => f.category))];
    const catColor = cat => CAT_COLORS[allCats.indexOf(cat) % CAT_COLORS.length];

    chart.innerHTML = cats.map(([cat, count]) => `
      <div class="bar-row">
        <div class="bar-label">${cat}</div>
        <div class="bar-track">
          <div class="bar-fill" style="width:${Math.round((count / max) * 100)}%;background:${catColor(cat)}"></div>
        </div>
        <div class="bar-count">${count}</div>
      </div>`).join('');
  }

  function updateLikedList(likedFoods, superIds, allFoods) {
    const list = document.getElementById('liked-list');
    const allCats = [...new Set(allFoods.map(f => f.category))];
    const catColor = cat => CAT_COLORS[allCats.indexOf(cat) % CAT_COLORS.length];

    if (likedFoods.length === 0) {
      list.innerHTML = '<div class="empty-placeholder">Vous n\'avez encore aimé aucun plat 🍽️</div>';
      return;
    }

    list.innerHTML = likedFoods.map((f, i) => `
      <div class="liked-item" style="animation-delay:${i * .05}s">
        <div class="liked-item-thumb">
          ${f.image
            ? `<img src="${f.image}" alt="${f.name}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">`
            : ''}
          <span class="liked-item-emoji" style="${f.image ? 'display:none' : ''}">${f.emoji}</span>
        </div>
        <div class="liked-item-info">
          <div class="liked-item-name">${f.name}</div>
          <div class="liked-item-cat">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${catColor(f.category)};margin-right:4px;vertical-align:middle"></span>
            ${f.category} · ⏱ ${f.time} · 🔥 ${f.calories}
          </div>
        </div>
        <div class="liked-item-heart">${superIds.includes(f.id) ? '⭐' : '❤️'}</div>
      </div>`).join('');
  }

  // Charger les stats au démarrage
  loadStats();
</script>

</body>
</html>