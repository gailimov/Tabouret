<?php

namespace blog\posts;

use tabouret\View;

View::useModuleLayout();

function index()
{
    View::render();
}

function show()
{
    View::renderPartial('posts/show', array('param' => $_GET['slug']));
}
