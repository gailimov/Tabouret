<?php

namespace main\controllers\site;

use tabouret\View;

function error404($message)
{
    echo 'Ooops, ' . $message;
}

function index()
{
    View::render();
}

