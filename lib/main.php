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

    $page_address = $ENGINE['pages']."/".$url;
    if (file_exists($page_address))
        if (is_dir($page_address))
            $page_address = $page_address."/index";
}
else
    $page_address = $ENGINE['pages']."/index";

################################################################
// OUTPUT
if (file_exists($page_address)) {

    // Default scripts
    scripts_publish(scripts_get($ENGINE['script_default']));

    // Headers
    header("Vary: Accept");
    if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml"))
        header("Content-Type: application/xhtml+xml; charset=UTF-8");
    else
        header("Content-Type: text/html; charset=UTF-8");

    // Page
    echo page_all($page_address, $url);
    }
else {
    header("HTTP/1.0 404 Not Found"); 
    exit();
}

################################################################
// Magic =)
unset($script_header);
unset($script_footer);
clearstatcache();

?>
