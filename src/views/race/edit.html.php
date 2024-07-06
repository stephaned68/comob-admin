<?php

?>
<div class="col-md-4 mt-2 mb-2">
  <form method="post">

    <div class="row">
      <div class="col">
        <?= $fm->renderField("race", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("intitule", $race) ?>
      </div>
    </div>

    <div class="row">
      <?= $fm->renderField("description", $race) ?>
    </div>

    <div class="row">
      <div class="col">
        <?= $fm->renderField("mod_for", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("mod_dex", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("mod_con", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("mod_int", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("mod_sag", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("mod_cha", $race) ?>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <?= $fm->renderField("taille_min", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("taille_max", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("poids_min", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("poids_max", $race) ?>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <?= $fm->renderField("age_base", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("esperance_vie", $race) ?>
      </div>
      <div class="col">
        <?= $fm->renderField("type_race", $race) ?>
      </div>
      <div class="col"></div>
    </div>

    <?= $fm->renderButtons($race) ?>

  </form>
</div>
