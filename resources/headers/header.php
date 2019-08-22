<head>
	<meta charset="utf-8">
	<title><?= config('APP.NAME'); ?></title>
	<meta name="csrf-token" content="<?= App\Providers\SessionProvider::token(); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?= config('APP.DESCRIPTION'); ?>">
	<meta name="author" content="<?= config('APP.AUTHOR'); ?>">

	<?= App\Helpers\FileHelper::loadLinks((array) config('LAYOUT.HEADER.LINKS')); ?>
	<?= App\Helpers\FileHelper::loadScripts((array) config('LAYOUT.HEADER.SCRIPTS')); ?>
</head>