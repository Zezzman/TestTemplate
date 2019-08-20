<?php
if (isset($bag['message']) && ! empty($bag['message']) && (! isset($bag['active']) || $bag['active'] === true)) {
    $style = $bag['style'] ?? '<div id="{id}" class="{class}">{message}</div>';
    echo App\Helpers\QueryHelper::scanCodes($bag, $style, [
        'id' => '',
        'class' => 'col-11 mx-auto my-0 alert alert-warning',
    ]);
}
?>