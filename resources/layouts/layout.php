<!DOCTYPE html>
<html lang="en">
<?php 
$this->header('resources/headers/header');

$links = config('NAV', [
    'Home' => ['link' => 'home/'],
]);
$this->section('resources/sections/navbar', [
    'links' => config('NAV', ['Home' => ['link' => 'home/']])
]);
?>

<body>
    <?= $this->content; ?>
</body>

<?php $this->footer('resources/footers/footer'); ?>

</html>