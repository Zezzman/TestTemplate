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
$provider->get('storage/uploads/{file}/', 'Storage@Uploads')
->auth()->isValid()->extension(['jpg', 'jpeg', 'png']);