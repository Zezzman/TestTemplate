<?php
    $this->layout('fill');
    $this->section('mainNav');
?>
<div class="container">
    <div class="row text-center vh-90">
        <div class="col-12 m-auto">
            <h1>503</h1>
            <p class="lead">
                Service Unavailable
            </p>
            <p><?= $model->Exception(); ?></p>
        </div>
    </div>
</div>
