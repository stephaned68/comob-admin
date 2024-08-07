<?php

use framework\Router;
use framework\Tools;

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Chroniques Mobiles</title>
  <?php if (isset($_SESSION['theme'])): ?>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/cosmo/bootstrap.min.css">
  <?php else: ?>
    <link rel="stylesheet" href="/assets/bootstrap/dist/css/bootstrap.css">
  <?php endif; ?>
  <link rel="stylesheet" href="/assets/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="/assets/jquery-confirm/dist/jquery-confirm.min.css">
  <link rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="container-fluid">
<header class="card-header row">
  <div class="col-4">
    <h3 title="<?= $_SESSION['theme'] ?>">Chroniques Mobiles</h3>
  </div>
  <div class="col-4 text-center">
    <?php if (isset($fm)) : ?>
      <h3><?= $fm->getTitle() ?></h3>
    <?php else : ?>
      <h3><?= $title ?? "" ?></h3>
    <?php endif; ?>
  </div>
  <div class="col-4 text-right">
    <ul class="nav nav-pills">
      <li class="nav-item">
        <a class="nav-link" href="<?= Router::route(['home', 'select']) ?>">(<?= $_SESSION["dataset"]["name"] ?>)</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
          Personnages
        </a>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="<?= Router::route(['family']) ?>">Familles</a>
          <a class="dropdown-item" href="<?= Router::route(['profile']) ?>">Profils</a>
          <a class="dropdown-item" href="<?= Router::route(['path']) ?>">Voies</a>
          <a class="dropdown-item" href="<?= Router::route(['ability']) ?>">Capacités</a>
          <a class="dropdown-item" href="<?= Router::route(['race']) ?>">Peuples/Espèces</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?= Router::route(['ability', 'multiple']) ?>">Voie complète</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
          Equipement
        </a>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="<?= Router::route(['property']) ?>">Propriétés</a>
          <a class="dropdown-item" href="<?= Router::route(['category']) ?>">Catégories</a>
          <a class="dropdown-item" href="<?= Router::route(['equipment']) ?>">Equipement</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
          Utils
        </a>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="<?= Router::route(['home', 'checkdb']) ?>">Vérification BD</a>
        </div>
      </li>
    </ul>
  </div>
</header>

<div class="row justify-content-center">

  <?php
  $messages = Tools::getFlash();
  if (is_array($messages) && count($messages) > 0) : ?>
    <?php foreach ($messages as $type => $messageList) : ?>
      <div class="alert alert-<?= $type ?> alert-dismissible fade show col-md-6 mt-2" role="alert">
        <button type="button" class="close" data-dismiss="alert">
          <span>&times;</span>
        </button>
        <ul>
          <?php foreach ($messageList as $message) : ?>
            <li><?= $message ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>

<div class="row justify-content-center">
  <?= $content ?>
</div>


<script src="/assets/jquery/dist/jquery.js"></script>
<script src="/assets/popper.js/dist/umd/popper.js"></script>
<script src="/assets/bootstrap/dist/js/bootstrap.js"></script>
<script src="/assets/select2/dist/js/select2.min.js"></script>
<script src="/assets/bootbox/dist/bootbox.min.js"></script>
<script src="/assets/bootbox/dist/bootbox.locales.min.js"></script>
<script src="/assets/jquery-confirm/dist/jquery-confirm.min.js"></script>
<script src="https://kit.fontawesome.com/fff1f19af2.js"></script>

<script src="/js/utils.js"></script>

<script>
  <?php if ($script) : ?>
  <?php include $script ?>
  <?php endif; ?>
</script>

</body>
</html>