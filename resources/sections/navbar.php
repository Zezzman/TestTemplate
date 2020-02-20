<?php
/**
 * Generates Navbar from $bag
 */
if (isset($bag['links'])) {
    $uri = $this->controller->getRequest()->uri;
    $links = $bag['links'];
    $left = '';
    $right = '';
    foreach ($links as $name => $link) {
        if (! isset($link['hide']) || $link['hide'] === false) {
            if (isset($link['link'])) {
                $linkObj = [
                    'name' => $name,
                    'link' => config('CLOSURES.LINK')('PUBLIC') . $link['link'],
                    'active' => ($link['link'] === $uri) ? 'active' : '',
                ];
                $item = App\Helpers\QueryHelper::insertCodes($linkObj, '<li class="nav-item {active}"><a class="nav-link" href="{link}">{name}</a></li>');
                if (isset($link['align'])) {
                    if ($link['align'] === 'right') {
                        $right .= $item;
                    }
                } else {
                    $left .= $item;
                }
            } elseif (isset($link['mail'])) {
                $linkObj = [
                    'name' => $name,
                    'mail' => $link['mail'],
                ];
                $item = App\Helpers\QueryHelper::insertCodes($linkObj, '<li class="nav-item"><a class="nav-link" href="mailto:{mail}">{name}</a></li>');
                if (isset($link['align'])) {
                    if ($link['align'] === 'right') {
                        $right .= $item;
                    }
                } else {
                    $left .= $item;
                }
            }
        }
    }
    if ($left != '')
    {
        $left = '<ul class="navbar-nav mr-auto">'. $left . '</ul>';
    }
    if ($right != '')
    {
        $right = '<ul class="navbar-nav ml-auto">' . $right . '</ul>';
    }
    echo (
        '<nav class="navbar navbar-expand-md navbar-dark bg-dark">
                <button class="navbar-toggler" type="button" data-toggle="collapse" 
                data-target="#navbar-collapse-div" aria-controls="navbar-collapse-div"
                aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>'
            . App\Helpers\QueryHelper::insertCodes(['left' => $left, 'right' => $right],
            ('<div class="collapse navbar-collapse" id="navbar-collapse-div">{left}{right}</div>'))
        . '</nav>'
    );
}
?>