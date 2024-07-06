<?php

use framework\Router;

?>
<div class="col-md-4">
  <div class="row mt-4 mb-2">
    <div class="col"></div>
    <div class="col text-right">
      <a class="btn btn-success"
         href="<?= Router::route(['property', 'edit']) ?>"
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
      <?php foreach ($propertyList as $property) : ?>
        <tr>
          <td>
            <a href="<?= Router::route(['property', 'edit', $property['code']]) ?>"
            ><?= stripslashes($property["intitule"]) ?></a>
          </td>
          <td class="text-center">
            <a class="btn btn-sm btn-outline-danger confirm-delete"
               href="<?= Router::route(['property', 'delete', $property['code']]) ?>"
            >Supprimer</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
