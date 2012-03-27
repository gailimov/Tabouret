<?php

namespace blog\controllers\posts;

use tabouret\View;

function show()
{
    View::renderPartial('posts/show', array('param' => $_GET['slug']));
}
