<div class="container">
    <div class="row text-center vh-85">
        <div class="col-12 m-auto">
            <h1><?= $model->responseCode; ?></h1>
            <p class="lead"><?= $model->responseTitle; ?></p>
            <p><?= $model->Messages(); ?></p>
            <p><?= $model->Exception(); ?></p>
        </div>
    </div>
</div>