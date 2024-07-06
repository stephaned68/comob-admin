<?php

?>
<div class="col-md-4 mt-2 mb-2">
  <form method="post">

    <?= $main->render($equipment) ?>

    <?= $props->render($properties) ?>

    <?= $main->renderButtons($equipment) ?>

  </form>
</div>
