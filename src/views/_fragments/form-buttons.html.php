<?php

?>

<div class="row form-group">
  <div class="col text-left">
    <?php if (isset($btnSubmit)) : ?>
    <button type="submit" name="submit"
            class="btn btn-primary"><?= $btnSubmit ?></button>
    <?php endif; ?>
    <?php if (isset($btnClose)) : ?>
      <button type="submit" name="close"
              class="btn btn-primary"><?= $btnClose ?></button>
    <?php endif; ?>
    <?php if (isset($indexRoute)) : ?>
      <a class="text-dark" href="<?= $indexRoute ?>">Retour</a>
    <?php endif; ?>
  </div>
  <?php if (isset($deleteRoute)) : ?>
    <div class="col text-right">
      <a class="btn btn-danger"
         href="<?= $deleteRoute ?>">Supprimer</a>
    </div>
  <?php endif; ?>
</div>
