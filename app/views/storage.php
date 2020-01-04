<?php
if (isset($bag['path']) && ! empty($bag['path'])) {
    if (is_file(config('PATHS.STORAGE') . $bag['path'])) {
        $file = \App\Providers\FileProvider::create($bag['path']);
        if (! $file->isValid()) {
            return false;
        }
        if ($file->read() === false) {
            return false;
        }
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Storage</h1>
            <small><?= '/' . $viewData->controller->getRequest()->uri . '/'; ?></small>
        </div>
        <div class="col-12">
            <?php $this->section('storage'); ?>
        </div>
    </div>
</div>
