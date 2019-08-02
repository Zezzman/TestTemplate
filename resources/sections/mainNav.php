<?php
$isAuth = App\Providers\AuthProvider::isAuthorized();

$links = config('NAV', [
    'Home' => ['link' => 'home/'],
]);

$bag = array_merge_recursive(['links' => $links], $bag ?? []);

$this->section('navbar', $bag);
?>