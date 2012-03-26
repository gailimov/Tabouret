<?php

namespace blog\controllers\posts;

function show()
{
    echo 'blog.posts.show(' . $_GET['slug'] . ')';
}
