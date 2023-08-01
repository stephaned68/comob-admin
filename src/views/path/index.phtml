<?php

use \framework\Router;

?>
<div class="col-md-6">
  <div class="row mt-4 mb-2">
    <div class="col">
      <form id="filter_form" method="post">
        <div class="form-inline">
          <label for="filter_type">Type</label>&nbsp;
          <select id="filter_type" name="filter_type" class="form-control-sm">
            <option value="*" <?= ($pathType === "*") ? "selected" : "" ?>>Toutes</option>
            <?php foreach ($pathTypes as $pathKey => $pathLabel) : ?>
              <option value="<?= $pathKey ?>"
                      <?= ($pathType != "*" && $pathKey == $pathType) ? "selected" : "" ?>
              ><?= $pathLabel ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-sm btn-outline-dark" type="submit" name="submit">Filtrer</button>
        </div>
      </form>
    </div>
    <div class="col text-right">
      <a class="btn btn-success"
         href="<?= Router::route(['path', 'edit']) ?>"
      ><i class="fas fa-plus-circle"></i> Ajouter</a>
    </div>
  </div>
  <div class="row">
    <table class="table table-bordered table-striped">
      <thead>
      <tr class="text-center">
        <th>#</th>
        <th>Intitulé</th>
        <th>Type</th>
        <th>Action</th>
      </tr>
      </thead>
      <tbody>
      <?php $ix = 0;
      foreach ($pathList as $path) : ?>
        <tr>
          <td class="text-center"><?= ++$ix ?></td>
          <td> <!--
            <a href="<?= Router::route(['path', 'edit', $path['voie']]) ?>"
            ><?= $path["nom"] ?></a> -->
            <a href="#editPopup" data-toggle="modal" data-id="<?= $path['voie'] ?>"><?= stripslashes($path["nom"]) ?></a>
          </td>
          <td><?= $pathTypes[$path['type']] ?></td>
          <td class="text-center">
            <a class="btn btn-sm btn-outline-dark"
               href="<?= Router::route(['path', 'abilities', $path['voie']]) ?>"
            >Capacités</a>
            <a class="btn btn-sm btn-outline-danger confirm-delete"
               href="<?= Router::route(['path', 'delete', $path['voie']]) ?>"
            >Supprimer</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<div id="editPopup" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modification de voie</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post">
          <?= $fm->render($path) ?>

          <?= $fm->renderButtons($path) ?>
        </form>
      </div>
    </div>
  </div>
</div>