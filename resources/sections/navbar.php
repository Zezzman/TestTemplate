<?php
/**
 * Generates Navbar from $bag
 */
if (isset($bag['links'])) {
    $uri = $this->viewData->controller->getRequest()->uri;
    $links = $bag['links'];
    $content = ('<nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <button class="navbar-toggler" type="button" data-toggle="collapse" 
            data-target="#navbar-collapse-div" aria-controls="navbar-collapse-div"
            aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar-collapse-div">{left}{right}</div>
        </nav>');
    $leftLinks = [];
    $rightLinks = [];
    foreach ($links as $name => $link) {
        if (! isset($link['hide']) || $link['hide'] === false) {
            if (isset($link['link'])) {
                $linkObj = [
                    'name' => $name,
                    'link' => config('LINKS.PUBLIC') . $link['link'],
                    'active' => ($link['link'] === $uri) ? 'active' : '',
                ];
                if (isset($link['align'])) {
                    if ($link['align'] === 'right') {
                        $rightLinks[] = $linkObj;
                    }
                } else {
                    $leftLinks[] = $linkObj;
                }
            }
        }
    }
    $left = '';
    if (count($leftLinks) > 0) {
        $left = '<ul class="navbar-nav mr-auto">' . 
        App\Helpers\QueryHelper::insertCodes($leftLinks,
            '<li class="nav-item {active}"><a class="nav-link" href="{link}">{name}</a></li>', true) .
        '</ul>';
    }
    $right = '';
    if (count($rightLinks) > 0) {
        $right = '<ul class="navbar-nav ml-auto">' . 
        App\Helpers\QueryHelper::insertCodes($rightLinks,
            '<li class="nav-item {active}"><a class="nav-link" href="{link}">{name}</a></li>', true) .
        '</ul>';
    }
    echo App\Helpers\QueryHelper::insertCodes(['left' => $left, 'right' => $right], $content);
}
?>