<?php
/* $this->get('uri', 'Controller@Action'); */
/**
 * Broadcast Calls
 */
$this->get('broadcast', 'Broadcast@Index')->setParams(['limit' => 1]);
/**
 * Collection Calls
 */
$this->get('collection', 'Collection@Index')->hasHeader('HTTP_REQUEST_COLLECTION');