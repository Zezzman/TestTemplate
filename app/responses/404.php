<?php
    $this->layout('fill');
    $this->section('mainNav');
?>
<div class="container">
    <div class="row text-center vh-90">
        <div class="col-12 m-auto">
            <h1>404</h1>
            <p class="lead">
                Page not found
            </p>
            <p><?= $model->Message(); ?></p>
            <p><?= $model->Exception(); ?></p>
        </div>
    </div>
</div>
