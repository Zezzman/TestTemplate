<?php
$path = trim($bag['path'], '/');
if (isset($path) && ! empty($path)) {
    if (is_file(config('PATHS.EXPAND')('STORAGE') . $path)) {
        $file = \App\Providers\FileProvider::create('storage/' . $path);
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
            <small><?= App\Helpers\HTMLHelper::breadcrumbs('storage/' . $path); ?></small>
        </div>
        <div class="col-12">
            <?= App\Helpers\HTMLHelper::backLink($path, config('LINKS.EXPAND')('STORAGE')); ?>
            <?= App\Helpers\HTMLHelper::folderFiles('storage/' . $path, [], true); ?>
        </div>
    </div>
</div>