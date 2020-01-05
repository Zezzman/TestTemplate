<?php
/* $provider->get('uri', 'Controller@Action'); */
/**
 * Broadcast Calls
 */
$provider->get('broadcast', 'Broadcast@Index')->params(['limit' => 1]);
/**
 * Collection Calls
 */
$provider->get('collection', 'Collection@Index')->header('HTTP_REQUEST_COLLECTION');