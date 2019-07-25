<?php
$isAuth = App\Providers\AuthProvider::isAuthorized();

$links = [
    'Home' => ['link' => 'home/'],
    'Document' => ['link' => 'document/'],
];

$bag = array_merge_recursive(['links' => $links], $bag ?? []);

$this->section('navbar', $bag);
?>