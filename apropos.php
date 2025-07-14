<?php require_once 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>À propos – E-Solidarité</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<section class="apropos container">
  <h1 class="text-center mb-5">À propos de E-Solidarité</h1>

  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <div class="img-container">
        <img src="images/apropos.jpg" alt="E-Solidarité" class="img-fluid">
      </div>
    </div>
    <div class="col-md-6">
      <p class="lead">
        <strong>E-Solidarité</strong> est une plateforme numérique qui favorise l'entraide communautaire. Elle met en relation des donateurs, bénévoles, et partenaires avec les personnes en situation de besoin, à travers des actions concrètes et locales.
      </p>
    </div>
  </div>

  <section class="mb-5">
    <h2 class="text-center">Notre mission</h2>
    <p class="fs-5 text-center">
      Offrir une solution simple, accessible et transparente pour venir en aide aux plus vulnérables via des projets, dons, aides médicales et sociales.
    </p>
  </section>

  <section class="row align-items-center mb-5">
    <div class="col-md-6 order-md-2">
      <div class="img-container">
        <img src="images/vision.jpg" alt="Vision E-Solidarité" class="img-fluid">
      </div>
    </div>
    <div class="col-md-6">
      <h2 class="text-center">Notre vision</h2>
      <p class="fs-5">
        Construire une société où la technologie est au service de l’humain. Nous croyons que chacun peut agir simplement pour un monde plus solidaire.
      </p>
    </div>
  </section>

  <section class="mb-5">
    <h2 class="text-center">Nos objectifs</h2>
    <ul class="fs-5">
      <li>Relier donateurs et causes fiables</li>
      <li>Faciliter les dons sécurisés (financiers & matériels)</li>
      <li>Encourager le bénévolat local</li>
      <li>Assurer une gestion transparente des aides</li>
    </ul>
  </section>

  <section class="mb-5">
    <h2 class="text-center">Nos valeurs</h2>
    <div class="row text-center">
      <div class="col-md-4">
        <i class="bi bi-people-fill fs-1"></i>
        <h5>Solidarité</h5>
        <p>Chaque geste compte pour améliorer une vie.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-eye-fill fs-1"></i>
        <h5>Transparence</h5>
        <p>La confiance est au cœur de notre action collective.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-heart-fill fs-1"></i>
        <h5>Engagement</h5>
        <p>Nous nous mobilisons pour un monde plus équitable.</p>
      </div>
    </div>
  </section>

  <section class="text-center">
    <h2 class="mb-3">Rejoignez-nous</h2>
    <p class="fs-5">
      Citoyen solidaire, entreprise engagée ou association, vous pouvez faire la différence avec E-Solidarité. Ensemble, transformons les vies.
    </p>
    <a href="contact.php" class="btn btn-outline-primary mt-3">Nous contacter</a>
  </section>
</section>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>
