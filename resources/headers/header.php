<head>
	<meta charset="utf-8">
	<title><?= config('APP.NAME'); ?></title>
	<meta name="csrf-token" content="<?= App\Providers\SessionProvider::token(); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?= config('APP.DESCRIPTION'); ?>">
	<meta name="author" content="<?= config('APP.AUTHOR'); ?>">
	<link rel="icon" href="<?= config('LINKS.IMAGES'); ?>favicon.png">

	<link href="<?= config('LINKS.CSS'); ?>style.css" rel="stylesheet">

	<?= App\Helpers\JSHelper::varPrint(['domain' => '"'. config('LINKS.PUBLIC') . '"']); ?>
</head>