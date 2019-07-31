<?php
/**
 *  Front Page
 */
$provider->get('/', 'Home@Index');
$provider->get('home/', 'Home@Index');
/**
 *  Documentation
 */
$provider->get('document/', 'Home@Document');
/**
 *  Upload files
 */
$provider->get('storage/uploads/', 'Storage@ListUploads')
->params(['extensions' => ['jpg', 'jpeg', 'png', 'gif']]);
$provider->get('storage/uploads/{file}/', 'Storage@Uploads')
->extension(['jpg', 'jpeg', 'png', 'gif']);