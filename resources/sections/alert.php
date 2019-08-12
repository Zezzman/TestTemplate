<?php
if (isset($bag['message']) && ! empty($bag['message']) && (! isset($bag['active']) || $bag['active'] === true)) {
    $bag['id'] = $bag['id'] ?? '';
    $bag['class'] = $bag['class'] ?? 'col-11 mx-auto my-0 alert alert-warning';
    $style = $bag['style'] ?? '<div id="{id}" class="{class}">{message}</div>';
    echo App\Helpers\QueryHelper::insertCodes($bag, $style);
}
?>