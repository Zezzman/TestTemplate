<?php
$isAuth = App\Providers\AuthProvider::isAuthorized();

$links = config('NAV');

$bag = array_merge_recursive(['links' => $links], $bag ?? []);

$this->section('navbar', $bag);
?>