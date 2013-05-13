<?php

################################################################
// (= Magic
$script_header = '';
$script_footer = '';

################################################################
// INPUT
if (isset($argv[0])) {
    if (isset($argv[1]))
        $_GET['url'] = $argv[1];
}

$url = isset($_GET['url']) ? urldecode($_GET['url']) : '';

if (!empty($url)) {

    $flag = substr($url, 0, 1);
        if ($flag === '/')
        $url = substr($url, 1);

    $flag = substr($url, -1);
        if ($flag === '/')
        $url = substr($url, 0, -1);

    //die($url);
}

################################################################
// OUTPUT

$FULL_PAGE = page_all($url);

if (empty($FULL_PAGE)) {
    // 404
    header("HTTP/1.0 404 Not Found"); 
    exit(); }
else {
    // Default scripts
    scripts_publish(scripts_get($ENGINE['script_default']));
    // Headers
    header("Content-Type: text/html; charset=UTF-8");
    // Page
    echo $FULL_PAGE;
}

################################################################
// Magic =)
unset($script_header);
unset($script_footer);
clearstatcache();

?>
