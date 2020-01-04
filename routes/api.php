<?php
/* $provider->get('uri', 'Controller@Action'); */
/**
 * Broadcast Calls
 */
$provider->get('broadcast', 'Broadcast@Index')->params(['limit' => 1]);
/**
 * Collection Calls
 */
$provider->get('collection', 'Broadcast@Collection')->header('HTTP_REQUEST_COLLECTION');