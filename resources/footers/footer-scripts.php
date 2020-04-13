<?php $plugins = config('LINKS.PUBLIC~PLUGINS'); ?>
<script src="<?= $plugins . 'node_modules\jquery\dist\jquery.min.js'; ?>"></script>
<script src="<?= $plugins . 'node_modules\jquery-validation\dist\jquery.validate.js'; ?>"></script>
<script src="<?= $plugins . 'node_modules\popper.js\dist\umd\popper.min.js'; ?>"></script>
<script src="<?= $plugins . 'node_modules\bootstrap\dist\js\bootstrap.min.js'; ?>"></script>
<?php
echo System\Helpers\FileHelper::loadScripts((array) config('LAYOUT.FOOTER.SCRIPTS'));
// Print appending scripts
echo \System\Helpers\FileHelper::loadScripts($viewData->bag['scripts'] ?? []);
?>