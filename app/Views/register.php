<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FoodSwipe — Inscription</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>

<div class="auth-page">
  <div class="auth-card">

    <div class="auth-logo">
      <span class="logo-icon">🥘</span>
      <h1>Créer un compte</h1>
      <p>Rejoignez la communauté des gourmands</p>
    </div>

    <div class="form-group">
      <label>Prénom &amp; Nom</label>
      <input type="text" id="reg-name" placeholder="Jean Dupont" autocomplete="name" />
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" id="reg-email" placeholder="vous@exemple.com" autocomplete="email" />
    </div>

    <div class="form-group">
      <label>Mot de passe</label>
      <input type="password" id="reg-pwd" placeholder="8 caractères minimum" autocomplete="new-password" />
    </div>

    <div class="form-group">
      <label>Confirmer le mot de passe</label>
      <input type="password" id="reg-pwd2" placeholder="••••••••" autocomplete="new-password" />
    </div>

    <p class="form-error" id="reg-error"></p>

    <button class="btn-primary" onclick="doRegister()">Créer mon compte 🎉</button>

    <div class="auth-switch">
      Déjà un compte ? <a href="login.php">Se connecter</a>
    </div>

  </div>
</div>

<script>
  async function doRegister() {
    const name  = document.getElementById('reg-name').value.trim();
    const email = document.getElementById('reg-email').value.trim();
    const pwd   = document.getElementById('reg-pwd').value;
    const pwd2  = document.getElementById('reg-pwd2').value;
    const err   = document.getElementById('reg-error');

    if (!name || !email || !pwd || !pwd2) {
      err.textContent = 'Veuillez remplir tous les champs.';
      err.classList.add('visible');
      return;
    }

    if (name.length < 3) {
      err.textContent = 'Le nom doit contenir au moins 3 caractères.';
      err.classList.add('visible');
      return;
    }

    if (!email.includes('@') || !email.includes('.')) {
      err.textContent = 'Veuillez entrer un email valide.';
      err.classList.add('visible');
      return;
    }

    if (pwd.length < 8) {
      err.textContent = 'Le mot de passe doit contenir au moins 8 caractères.';
      err.classList.add('visible');
      return;
    }

    if (pwd !== pwd2) {
      err.textContent = 'Les mots de passe ne correspondent pas.';
      err.classList.add('visible');
      return;
    }

    err.classList.remove('visible');

    const response = await fetch("<?= base_url('/register-check') ?>", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: JSON.stringify({
        name: name,
        email: email,
        password: pwd,
        password2: pwd2
      })
    });

    const result = await response.json();

    if (result.success) {
      localStorage.removeItem('fs_liked');
      localStorage.removeItem('fs_super');
      localStorage.removeItem('fs_seen');
      localStorage.removeItem('fs_deck');

      window.location.href = "<?= base_url('/home') ?>";
    } else {
      err.textContent = result.message;
      err.classList.add('visible');
    }
  }

  document.addEventListener('keydown', e => { if (e.key === 'Enter') doRegister(); });
</script>

</body>
</html>
