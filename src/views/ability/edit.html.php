<?php

?>
<div class="col-md-4 mt-2 mb-2">
  <form method="post">

    <?= $fm->renderField("capacite", $ability) ?>
    <?= $fm->renderField("nom", $ability) ?>
    <div class="row mt-2 mb-2">
      <div class="col">
        <?= $fm->renderField("limitee", $ability) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("sort", $ability) ?>
      </div>
    </div>
    <div class="row">
      <div class="col">
      <?= $fm->renderField("action", $ability) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("type", $ability) ?>
      </div>
    </div>
    <?= $fm->renderField("description", $ability) ?>

    <?= $fm->renderButtons($ability) ?>

  </form>

</div>
