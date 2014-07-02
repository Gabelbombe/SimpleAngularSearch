<?php if (! defined('APP_PATH')) define('APP_PATH', dirname(__FILE__));

// deny without post
if(! isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ('XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH']))
{
    header('HTTP/1.0 403 Forbidden');
    exit('Forbidden');
}

$allowed = require_once(APP_PATH . '/src/config/config.php');

if (isset($_POST) && ! empty($_POST))
{
    require_once APP_PATH . '/src/Helper/Filter.php';

    $filter = New \Helper\Filter();
    $clean  = $filter->filterStructUtf8(INPUT_POST, [
        'query' => FILTER_STRUCT_FORCE_ARRAY | FILTER_STRUCT_TRIM,
        'allow' => FILTER_STRUCT_FULL_TRIM,
    ]);

    /* Abandoned
    foreach ($allowed AS $allow => $condition)
    {
        if ($condition && isset($_POST[$allow]))
        {
            // ...
        }
    }

    */


} else { /* ... */ }