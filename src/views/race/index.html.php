<?php

use framework\Router;

?>
<div class="col-md-4">
  <div class="row mt-4 mb-2">
    <div class="col"></div>
    <div class="col text-right">
      <a class="btn btn-success"
         href="<?= Router::route(['race', 'edit']) ?>"
      >Ajouter</a>
    </div>
  </div>
  <div class="row">
    <table class="table table-bordered table-striped">
      <thead>
      <tr class="text-center">
        <th>Intitulé</th>
        <th>Action</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($raceList as $race) : ?>
        <tr>
          <td>
            <a href="<?= Router::route(['race', 'edit', $race['race']]) ?>"
            ><?= stripslashes($race['intitule']) ?></a>
          </td>
          <td class="text-center">
            <a class="btn btn-sm btn-outline-dark"
               href="<?= Router::route(['race', 'traits', $race['race']]) ?>"
            >Traits</a>
            <a class="btn btn-sm btn-outline-dark"
               href="<?= Router::route(['race', 'abilities', $race['race']]) ?>"
            >Capacité(s) raciale(s)</a>
            <a class="btn btn-sm btn-outline-dark"
               href="<?= Router::route(['path', 'abilities', $race['race']]) ?>"
            >Voie</a>
            <a class="btn btn-sm btn-outline-danger confirm-delete"
               href="<?= Router::route(['race', 'delete', $race['race']]) ?>"
            >Supprimer</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
