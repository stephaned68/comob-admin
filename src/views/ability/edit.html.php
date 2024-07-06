<?php

?>
<div class="col-md-4 mt-2 mb-2">
  <form method="post">

    <?= $fm->renderField("capacite", $ability) ?>
    <?= $fm->renderField("nom", $ability) ?>
    <div class="row">
      <div class="col">
        <?= $fm->renderField("limitee", $ability) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("sort", $ability) ?>
      </div>
    </div>
    <?= $fm->renderField("type", $ability) ?>
    <?= $fm->renderField("description", $ability) ?>

    <?= $fm->renderButtons($ability) ?>

  </form>

</div>
