<?php

?>
<div class="col-md-4 mt-2 mb-2">
  <form method="post">

    <?= $fm->render($profile) ?>

    <div class="form-group">
      <label for="voies_profil">Voies</label>
      <select id="voies_profil"
              name="voies[]"
              class="form-control select-2"
              multiple="multiple">
        <?php foreach ($pathList as $pathValue => $pathLabel) : ?>
        <option value="<?= $pathValue ?>"
                <?= (in_array($pathValue, $voies)) ? "selected" : "" ?>
        ><?= $pathLabel ?> (<?= $pathValue ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="voies_prestige">Voies de prestige</label>
      <select id="voies_prestige"
              name="voies[]"
              class="form-control select-2"
              multiple="multiple">
        <?php foreach ($prestList as $prestValue => $prestLabel) : ?>
          <option value="<?= $prestValue ?>"
            <?= (in_array($prestValue, $voies)) ? "selected" : "" ?>
          ><?= $prestLabel ?> (<?= $prestValue ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <?= $fm->renderButtons($profile) ?>

  </form>

</div>
