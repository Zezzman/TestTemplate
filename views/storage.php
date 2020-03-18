<?php
$path = trim($bag['path'], '/');
if (isset($path) && ! empty($path)) {
    if (is_file(config('CLOSURES.PATH')('STORAGE') . $path)) {
        $file = \System\Providers\FileProviders\MediaFileProvider::create('storage/' . $path);
        if (is_null($file)
        || ! $file->isValid()) {
            throw new \System\Exceptions\RespondException(415, "");
        }
        if ($file->read() === false) {
            throw new \System\Exceptions\RespondException(204, "");
        }
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Storage</h1>
            <small><?= System\Helpers\HTMLHelper::breadcrumbs('storage/' . $path); ?></small>
        </div>
        <div class="col-12">
            <?= System\Helpers\HTMLHelper::backLink($path, config('CLOSURES.LINK')('STORAGE')); ?>
            <?= System\Helpers\HTMLHelper::folderFiles('storage/' . $path, [], true); ?>
        </div>
    </div>
</div>