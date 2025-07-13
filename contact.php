<?php require_once 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact – E-Solidarité</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<section class="contact container py-5">
  <h1 class="text-center mb-4">Nous contacter</h1>

  <div class="row g-4">
    <div class="col-md-6">

      <!-- Alerte succès (affichée seulement après redirection depuis traitement_contact.php) -->
      <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div id="successMessage" class="alert alert-success" role="alert">
          Votre message a été envoyé avec succès !
        </div>
      <?php endif; ?>

      <form id="contactForm" action="traitement_contact.php" method="post" class="p-4 border rounded shadow-sm bg-light">
        <div class="mb-3">
          <label for="nom" class="form-label">Nom</label>
          <input type="text" name="nom" id="nom" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Adresse e-mail</label>
          <input type="email" name="email" id="email" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="sujet" class="form-label">Sujet</label>
          <input type="text" name="sujet" id="sujet" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="message" class="form-label">Message</label>
          <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Envoyer</button>
      </form>
    </div>

    <div class="col-md-6">
      <div class="ratio ratio-4x3 rounded shadow-sm overflow-hidden">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d249177.0848787154!2d-17.580865824439084!3d14.692328397224036!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xec1725d8b9924a3%3A0xa91f634ff1cb8e26!2sDakar%2C%20S%C3%A9n%C3%A9gal!5e0!3m2!1sfr!2sfr!4v1718773370495!5m2!1sfr!2sfr"
          width="600" height="450" style="border:0;" allowfullscreen loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('contactForm');

  form.addEventListener('submit', function (e) {
    const nom = document.getElementById('nom');
    const email = document.getElementById('email');
    const sujet = document.getElementById('sujet');
    const message = document.getElementById('message');

    // Validation simple JS
    if (!nom.value.trim() || !email.value.trim() || !sujet.value.trim() || !message.value.trim()) {
      alert("Veuillez remplir tous les champs.");
      e.preventDefault();
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value)) {
      alert("Veuillez entrer une adresse e-mail valide.");
      email.focus();
      e.preventDefault();
      return;
    }

    if (message.value.trim().length < 10) {
      alert("Le message doit contenir au moins 10 caractères.");
      message.focus();
      e.preventDefault();
      return;
    }

    // ❌ Ne pas empêcher l'envoi ici ! Pas de e.preventDefault()
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>
