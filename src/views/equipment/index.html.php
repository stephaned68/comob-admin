<?php

use framework\Router;

$eqCount = 0;
?>
<div class="col-md-8">
  <div class="row mt-4 mb-2">
    <div class="col-8">
      <form id="filter_form" method="post">
        <div class="form-inline">
          <label for="filter_category">Catégorie</label>&nbsp;
          <select id="filter_category" name="filter_category" class="form-control-sm">
            <option value="*" <?= ($categoryFilter === "*") ? "selected" : "" ?>>Toutes</option>
            <?php foreach ($categories as $categoryKey => $categoryLabel) : ?>
              <option value="<?= $categoryKey ?>"
                <?= ($categoryFilter != "*" && $categoryKey == $categoryFilter) ? "selected" : "" ?>
              ><?= $categoryLabel ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-sm btn-outline-dark" type="submit" name="submit"><i class="fas fa-search"></i> Filtrer</button>
        </div>
      </form>
    </div>
    <div class="col-4 text-right">
      <a class="btn btn-success"
         href="<?= Router::route(['equipment', 'edit']) ?>"
      ><i class="fas fa-plus-circle"></i> Ajouter</a>
    </div>
  </div>
  <div class="row">
    <table class="table table-bordered table-striped">
      <thead>
      <tr class="text-center">
        <th>Intitulé</th>
        <th>Catégorie</th>
        <th>Prix</th>
        <th>Propriétés</th>
        <th>Action</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($equipmentList as $equipment) :
            $eqCount++; ?>
        <tr>
          <td>
            <a href="<?= Router::route(['equipment', 'edit', $equipment['code']]) ?>"
            ><?= stripslashes($equipment["designation"]) ?></a>
          </td>
          <td>
            <?= $equipment["categorie"] ?>
          </td>
          <td class="text-right">
            <?= $equipment["prix"] ?>
          </td>
          <td>
            <?php
              if (strpos($equipment['props'],"\n") != 0 || $equipment['props'] != "") {
                $props = explode("\n", $equipment['props']);
                echo "<details>";
                echo "<summary>Afficher</summary>";
                echo "<ul>";
                foreach ($props as $prop) {
                  echo "<li>" . trim($prop) . "</li>";
                }
                echo "</ul>";
                echo "</details>";
              }
            ?>
          </td>
          <td class="text-center">
            <a class="btn btn-sm btn-outline-dark"
               href="<?= Router::route(['equipment', 'properties', $equipment['code']]) ?>"
            ><i class="fas fa-edit"></i> Propriétés</a>
            <a class="btn btn-sm btn-outline-danger confirm-delete"
               href="<?= Router::route(['equipment', 'delete', $equipment['code']]) ?>"
            ><i class="fas fa-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
