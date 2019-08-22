<?php
    $links = $model->Messages('links', '{message}');
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1><?= $viewData->controller->getRequest()->uri; ?></h1>
        </div>
        <div class="col-12">
            <?= $links ?>
        </div>
    </div>
</div>
