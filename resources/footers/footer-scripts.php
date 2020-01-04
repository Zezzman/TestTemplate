<script src="<?= config('LINKS.PLUGINS') . 'node_modules\jquery\dist\jquery.min.js'; ?>"></script>
<script src="<?= config('LINKS.PLUGINS') . 'node_modules\popper.js\dist\umd\popper.min.js'; ?>"></script>
<script src="<?= config('LINKS.PLUGINS') . 'node_modules\bootstrap\dist\js\bootstrap.min.js'; ?>"></script>
<?php
echo App\Helpers\FileHelper::loadScripts((array) config('LAYOUT.FOOTER.SCRIPTS'));
// Print appending scripts
echo \App\Helpers\FileHelper::loadScripts($this->viewData->bag['scripts'] ?? []);
?>