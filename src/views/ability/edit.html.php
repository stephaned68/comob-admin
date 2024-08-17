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
    <div class="row">
      <div class="col">
        <?= $fm->renderField("utilisations", $ability) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("utilisations_freq", $ability) ?>
      </div>
    </div>
    

    <?= $fm->renderButtons($ability) ?>

  </form>

</div>
