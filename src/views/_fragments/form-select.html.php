<?php

?>

<div class="form-group">
  <label for="<?= $fieldName ?>"><?= $fieldLabel ?? $fieldName ?></label>
  <select class="<?= $fieldClass ?? '' ?>"
         id="<?= $fieldName ?>"
         name="<?= $fieldName ?>"
    <?= $fieldReadonly ?? "" ?>
  >
    <?php foreach($fieldSelect as $optionK => $optionV) : ?>
      <option value="<?= $optionK ?>" <?= $optionK == $fieldValue ? "selected" : "" ?>><?= $optionV ?></option>
    <?php endforeach; ?>
  </select>
</div>
