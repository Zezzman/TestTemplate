<head>
	<meta charset="utf-8">
	<title>Test Landing</title>
	<meta name="test-token" content="<?= App\Providers\SessionProvider::token(); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="test project">
	<meta name="author" content="Francois Le Roux">
	<link rel="icon" href="<?= config('LINKS.IMAGES'); ?>favicon.png">

	<link href="<?= config('LINKS.CSS'); ?>style.css" rel="stylesheet">

	<?= App\Helpers\JSHelper::varPrint(['domain' => '"'. config('LINKS.PUBLIC') . '"']); ?>
</head>