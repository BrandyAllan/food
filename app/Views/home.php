<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FoodSwipe — Découvrir</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>

<div class="app-page">

  <!-- Top Bar -->
  <div class="topbar">
    <span class="topbar-logo"><span>🍽️</span>FoodSwipe</span>
    <div class="topbar-actions">
      <a href="stats.html" title="Mes stats">📊</a>
      <a href="<?= base_url('logout') ?>" title="Se déconnecter"">🚪</a>
    </div>
  </div>

  <!-- Card Area -->
  <div class="card-area">
    <div class="card-stack" id="card-stack"></div>

    <div class="empty-state" id="empty-state">
      <div class="emoji">🍀</div>
      <h2>C'est tout pour l'instant !</h2>
      <p>Vous avez vu tous les plats disponibles.<br>Consultez vos stats !</p>
      <a href="stats.html" class="btn-primary" style="max-width:200px;margin-top:8px;text-align:center;display:block">
        Voir mes stats 📊
      </a>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="action-btns" id="action-btns">
    <button class="action-btn btn-skip"  title="Pas intéressé" onclick="swipeLeft()">✕</button>
    <button class="action-btn btn-super" title="Super Like"    onclick="superLike()">⭐</button>
    <button class="action-btn btn-like"  title="J'aime"        onclick="swipeRight()">♥</button>
  </div>

  <!-- Bottom Nav -->
  <div class="bottom-nav">
    <a href="home.html" class="active">
      <span class="nav-icon">🔥</span>Découvrir
    </a>
    <a href="add-food.html">
      <span class="nav-icon">➕</span>Ajouter
    </a>
    <a href="stats.html">
      <span class="nav-icon">📊</span>Mes stats
    </a>
  </div>

</div>

<script>
  /* ── Data depuis la base ── */
  let FOODS = [];
  let ALL_CATS = [];
  let deck = [];

  const CAT_COLORS = [
    '#FF6B6B','#FF8E53','#FFC371','#4ECDC4','#45B7D1',
    '#96CEB4','#DDA0DD','#FF69B4','#20B2AA','#9370DB','#F08080','#3CB371',
  ];

  const catColor = cat => CAT_COLORS[ALL_CATS.indexOf(cat) % CAT_COLORS.length];

  async function loadFoodsFromDatabase() {
    try {
      const response = await fetch("<?= base_url('/foods') ?>", {
        method: "GET",
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        }
      });

      if (!response.ok) {
        console.error("Erreur HTTP :", response.status);
        return;
      }

      const databaseFoods = await response.json();
      const customs = JSON.parse(localStorage.getItem('fs_custom_foods') || '[]');

      FOODS = [...databaseFoods, ...customs];
      ALL_CATS = [...new Set(FOODS.map(f => f.cat))];

      const state = loadState();
      deck = state.deck;

      renderDeck();
    } catch (error) {
      console.error("Erreur chargement foods :", error);
    }
  }

  /* ── State ── */
  function loadState() {
    const seenIds  = JSON.parse(localStorage.getItem('fs_seen')  || '[]').map(Number);
    const likedIds = JSON.parse(localStorage.getItem('fs_liked') || '[]').map(Number);
    const superIds = JSON.parse(localStorage.getItem('fs_super') || '[]').map(Number);

    const deck = FOODS.filter(f => !seenIds.includes(Number(f.id)));

    return { deck, likedIds, superIds, seenIds };
  }

  async function saveSwipe(food, action) {
    try {
      await fetch("<?= base_url('/save-swipe') ?>", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest"
        },
        body: JSON.stringify({
          food_id: food.id,
          action: action
        })
      });
    } catch (error) {
      console.error("Erreur sauvegarde swipe :", error);
    }
  }

  /* ── Render ── */
  function renderDeck() {
    const stack = document.getElementById('card-stack');
    const empty = document.getElementById('empty-state');
    const btns  = document.getElementById('action-btns');

    stack.innerHTML = '';

    if (deck.length === 0) {
      empty.classList.add('visible');
      btns.style.display = 'none';
      return;
    }

    empty.classList.remove('visible');
    btns.style.display = 'flex';

    deck.slice(0, 3).reverse().forEach(food => {
      stack.appendChild(buildCard(food));
    });

    attachDrag(stack.querySelector('.food-card:last-child'), deck[0]);
  }

  function buildCard(food) {
    const col = catColor(food.cat);
    const el = document.createElement('div');

    el.className = 'food-card';
    el.dataset.id = food.id;

    const imageUrl = food.img ? "<?= base_url() ?>" + food.img : "";

    const imgHTML = food.img
      ? `<img src="<?= base_url('images/') ?>${imageUrl}" alt="${food.name}" class="food-card-photo" onerror="this.parentElement.innerHTML='<span class=food-card-emoji>${food.emoji}</span>'">`
      : `<span class="food-card-emoji">${food.emoji}</span>`;

    el.innerHTML = `
      <div class="food-card-img" style="background:linear-gradient(135deg,${col}22,${col}55)">${imgHTML}</div>
      <div class="stamp stamp-like">J'aime ❤️</div>
      <div class="stamp stamp-nope">Nope 👎</div>
      <div class="food-card-info">
        <div class="food-card-top">
          <div class="food-card-name">${food.name}</div>
          <div class="food-card-rating">⭐ ${food.rating}</div>
        </div>
        <div class="food-card-meta">
          <span class="badge category">${food.cat}</span>
          <span class="badge time">⏱ ${food.time}</span>
          <span class="badge cal">🔥 ${food.cal}</span>
        </div>
        <div class="food-card-desc">${food.desc || food.description || ''}</div>
      </div>`;

    return el;
  }

  /* ── Drag ── */
  function attachDrag(card, food) {
    if (!card) return;

    let startX = 0, startY = 0, currentX = 0, dragging = false;
    const likeStamp = card.querySelector('.stamp-like');
    const nopeStamp = card.querySelector('.stamp-nope');

    function onStart(e) {
      dragging = true;
      startX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
      startY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
      card.style.transition = 'none';
    }

    function onMove(e) {
      if (!dragging) return;

      const x = (e.type === 'touchmove' ? e.touches[0].clientX : e.clientX) - startX;
      const y = (e.type === 'touchmove' ? e.touches[0].clientY : e.clientY) - startY;

      currentX = x;
      card.style.transform = `translateX(${x}px) translateY(${y * .2}px) rotate(${x / 18}deg)`;

      const pct = Math.min(Math.abs(x) / 80, 1);
      likeStamp.style.opacity = x > 20 ? pct : 0;
      nopeStamp.style.opacity = x < -20 ? pct : 0;
    }

    function onEnd() {
      if (!dragging) return;

      dragging = false;
      likeStamp.style.opacity = 0;
      nopeStamp.style.opacity = 0;

      if (currentX > 90) performSwipe('right', food);
      else if (currentX < -90) performSwipe('left', food);
      else {
        card.style.transition = 'transform .4s cubic-bezier(.22,.61,.36,1)';
        card.style.transform = '';
      }

      currentX = 0;
    }

    card.addEventListener('mousedown', onStart);
    card.addEventListener('touchstart', onStart, { passive: true });
    window.addEventListener('mousemove', onMove);
    window.addEventListener('touchmove', onMove, { passive: true });
    window.addEventListener('mouseup', onEnd);
    window.addEventListener('touchend', onEnd);
  }

  /* ── Swipe ── */
  async function performSwipe(dir, food) {
    const stack = document.getElementById('card-stack');
    const top = stack.querySelector('.food-card:last-child');

    if (!top) return;

    top.classList.add(dir === 'right' ? 'swiping-right' : 'swiping-left');

    const action = dir === 'right' ? 'like' : 'skip';
    await saveSwipe(food, action);

    setTimeout(() => {
      deck.shift();
      renderDeck();
    }, 320);
  }

  function swipeRight() {
    if (deck.length) performSwipe('right', deck[0]);
  }

  function swipeLeft() {
    if (deck.length) performSwipe('left', deck[0]);
  }

  function superLike() {
    if (!deck.length) return;

    const food = deck[0];
    const stack = document.getElementById('card-stack');
    const top = stack.querySelector('.food-card:last-child');

    if (!top) return;

    top.classList.add('swiping-right');
    saveSwipe(food, 'super');

    setTimeout(() => {
      deck.shift();
      renderDeck();
    }, 320);
  }

  /* ── Init ── */
  loadFoodsFromDatabase();
</script>

</body>
</html>
