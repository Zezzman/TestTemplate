<?php
/* $this->get('uri', 'Controller@Action'); */
/**
 *  Front Page
 */
$this->get('/', 'Home@Index');
$this->get('home/', 'Home@Index');
/**
 *  Documentation
 */
$this->get('document/', 'Home@Document');
/**
 *  Upload files
 */
$this->get('storage/{dir}...', 'Storage@Index')
->addParams(['extensions' => ['jpg', 'jpeg', 'png', 'gif']]);