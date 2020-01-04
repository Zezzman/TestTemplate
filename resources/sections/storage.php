<?php
if (isset($bag['path'])) {
    $path = trim($bag['path'], '/');
    $extensions = $bag['extensions'];
    $html = '';
    if (! empty($bag['path'])) {
        $back = \App\Helpers\DataCleanerHelper::dataMap(config('LINKS.STORAGE') . $path, '/',
        function ($result, $item) { return $result . '/' . $item; }, -1, 3);
        $html .= ('<a href="' . $back . '/" style="display:block">/back/</a>');
    }
    if (is_dir(config('PATHS.STORAGE') . $path)) {
        $files = \App\Providers\FileProvider::listFiles(config('PATHS.STORAGE') . $path, true);
        foreach ($files as $file) {
            $html .= '<a href="' . \App\Helpers\DataCleanerHelper::dataMap($file, '/',
            function ($result, $item) { return $result . '/' . $item; }, 0, 5)
            . '/" style="display:block">' . \App\Helpers\DataCleanerHelper::dataMap($file, '/',
            function ($result, $item) { return $result . '/' . $item; }, 0, -1) . '/</a>';
        }
    }
    echo $html;
}
?>