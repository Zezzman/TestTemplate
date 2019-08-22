<!DOCTYPE html>
<html lang="en">
<?php 
$this->header('header');

$sections = config('LAYOUT.SECTIONS');
if (is_array($sections)) {
    foreach ($sections as $section) {
        $this->section($section, $bag); 
    }
}
?>

<body>
    <?= $this->body();?>
</body>

<?php $this->footer('footer'); ?>

</html>