<?php
/* $provider->command(command, [params], 'Controller@Action'); */
$provider->default('Home@Index');
$provider->command('database', [], 'Database@Index');