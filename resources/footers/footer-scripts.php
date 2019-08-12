<script src="<?= config('LINKS.PLUGINS'); ?>node_modules\jquery\dist\jquery.min.js"></script>
<script src="<?= config('LINKS.PLUGINS'); ?>node_modules\popper.js\dist\umd\popper.min.js"></script>
<script src="<?= config('LINKS.PLUGINS'); ?>node_modules\bootstrap\dist\js\bootstrap.min.js"></script>
<script src="<?= config('LINKS.JS'); ?>index.js"></script>
<?php 
use App\Helpers\JSHelper;

// Print appending scripts
if (isset($this->viewData->bag['scripts'])) {
    echo JSHelper::loadScripts($this->viewData->bag['scripts']);
}
?>