<?php if (! defined('APP_PATH')) define('APP_PATH', dirname(__FILE__));

// deny without post
if(! isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ('XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH']))
{
    header('HTTP/1.0 403 Forbidden');
    exit('Forbidden');
}

// needs an autloader =/ composer come help me, lol
$config = require_once(APP_PATH . '/src/config/config.php');
          require_once(APP_PATH . '/src/Database/DataMapper.php');
          require_once(APP_PATH . '/src/Models/Search.php');

if (isset($_POST) && ! empty($_POST))
{
    require_once APP_PATH . '/src/Helper/Filter.php';

    $filter = New \Helper\Filter();
    $clean  = $filter->filterStructUtf8(INPUT_POST, [
        'allow' => FILTER_STRUCT_FULL_TRIM,
        'query' => '',
    ]);

    $search = New \Models\Search();

    echo ('true' != $clean['allow'] || empty($clean['query']))
        ? 'Broke the interwebs...'
        : $search->query();

} else { /* ... */ }