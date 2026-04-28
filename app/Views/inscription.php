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
      <span class="logo-icon">🍽️</span>
      <h1>FoodSwipe</h1>
      <p>Créez votre compte et commencez à swiper !</p>
    </div>

    <div class="form-group">
      <label>Nom</label>
      <input type="text" id="reg-name" placeholder="Votre nom" />
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" id="reg-email" placeholder="vous@exemple.com" />
    </div>

    <div class="form-group">
      <label>Mot de passe</label>
      <input type="password" id="reg-pwd" placeholder="••••••••" />
    </div>

    <div class="form-group">
      <label>Confirmer le mot de passe</label>
      <input type="password" id="reg-pwd2" placeholder="••••••••" />
    </div>

    <p class="form-error" id="reg-error"></p>

    <button class="btn-primary" onclick="doRegister()">S'inscrire 🍴</button>

    <div class="auth-switch">
      Déjà un compte ? <a href="<?= base_url('/') ?>">Se connecter</a>
    </div>

  </div>
</div>

<script>
async function doRegister() {
  const name = document.getElementById('reg-name').value.trim();
  const email = document.getElementById('reg-email').value.trim();
  const pwd = document.getElementById('reg-pwd').value;
  const pwd2 = document.getElementById('reg-pwd2').value;
  const err = document.getElementById('reg-error');

  // Reset erreur
  err.classList.remove('visible');

  // 🔍 VALIDATIONS
  if (name.length < 3) {
    err.textContent = "Le nom doit contenir au moins 3 caractères.";
    err.classList.add('visible');
    return;
  }

  if (!email.includes("@") || !email.includes(".")) {
    err.textContent = "Email invalide (ex: test@mail.com).";
    err.classList.add('visible');
    return;
  }

  if (pwd.length < 8) {
    err.textContent = "Le mot de passe doit contenir au moins 8 caractères.";
    err.classList.add('visible');
    return;
  }

  if (pwd !== pwd2) {
    err.textContent = "Les mots de passe ne correspondent pas.";
    err.classList.add('visible');
    return;
  }

  // 🔄 ENVOI AU SERVEUR
  const response = await fetch("<?= base_url('/register') ?>", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest"
    },
    body: JSON.stringify({
      name: name,
      email: email,
      password: pwd
    })
  });

  const result = await response.json();

  if (result.success) {
    alert("Compte créé !");
    window.location.href = "<?= base_url('/') ?>";
  } else {
    err.textContent = result.message;
    err.classList.add('visible');
  }
}

// Enter key
document.addEventListener('keydown', e => {
  if (e.key === 'Enter') doRegister();
});
</script>

</body>
</html>