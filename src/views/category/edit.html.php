<?php

?>
<div class="col-md-4 mt-2 mb-2">
  <form method="post">

    <?= $fm->render($category) ?>

    <div class="form-group">
      <label for="props_equip">Propriétés</label>
      <select id="props_equip"
              name="properties[]"
              class="form-control select-2"
              multiple="multiple">
        <?php foreach ($propList as $propValue => $propLabel) : ?>
          <option value="<?= $propValue ?>"
            <?= (in_array($propValue, $properties)) ? "selected" : "" ?>
          ><?= $propLabel ?> (<?= $propValue ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <?= $fm->renderButtons($category) ?>

  </form>
</div>
