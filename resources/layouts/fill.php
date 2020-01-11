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

<body style="height: 100vh">
    <?= $this->content; ?>
</body>

<?php $this->footer('resources/footers/fixed-footer'); ?>

</html>