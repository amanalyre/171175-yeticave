<?php

function renderTemplate ($path, $data)
{
    if (!file_exists($path)) {
        return null;
        //return 'Template '. $path. ' doesn\'t exist';
    }

    extract($data);
    ob_start();
    include ($path);

    return ob_get_clean();
}
