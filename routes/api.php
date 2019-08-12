<?php
/**
 * Broadcast Calls
 */
$provider->get('broadcast', 'Broadcast@Index')->params(['limit' => 1]);
/**
 * Collector Calls
 */
$provider->get('collection', 'Broadcast@Collection')->header('HTTP_REQUEST_COLLECTION');
$provider->get('collection/images/', 'Broadcast@Collection')->params(['images'])->header('HTTP_REQUEST_COLLECTION');