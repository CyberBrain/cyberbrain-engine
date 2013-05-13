<?php

// Session
#session_start();

################################################################
## // Version
$ENGINE['version'] = '0.2.0';

## //Config
## Be sure settings in index.php are correct!
## Default settings:
// Path to engine
$ENGINE['path'] = isset($ENGINE['path']) ? $ENGINE['path'] : $_SERVER['DOCUMENT_ROOT']."/engine";
// Path to pages
$ENGINE['pages'] = isset($ENGINE['pages']) ? $ENGINE['pages'] : $_SERVER['DOCUMENT_ROOT']."/pages";
// Path to script bundles
$ENGINE['scripts'] = isset($ENGINE['scripts']) ? $ENGINE['scripts'] : $_SERVER['DOCUMENT_ROOT']."/scripts";
// Default script
$ENGINE['script_default'] = isset($ENGINE['script_default']) ? $ENGINE['script_default'] : "common";
// Path to template
$ENGINE['template'] = isset($ENGINE['template']) ? $ENGINE['template'] : $ENGINE['pages']."/template.htm";
// Path to css file
$ENGINE['css'] = isset($ENGINE['css']) ? $ENGINE['css'] : "/style.css";

################################################################
// functions =)

// function parser ($body)
require_once ($ENGINE['path']."/lib/parser.php");
////

// ready
require_once ($ENGINE['path']."/lib/main.php");
////

################################################################
// Global scripts

$script_header = '';
$script_footer = '';

################################################################
// Get url
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
    publish_scripts(get_scripts($ENGINE['script_default']));

    // Headers
    header("Vary: Accept");
    if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml"))
        header("Content-Type: application/xhtml+xml; charset=UTF-8");
    else
        header("Content-Type: text/html; charset=UTF-8");

    // Page
    $content = get_content($page_address);
    $FULL_PAGE = build_page($content['title'], $content['body'], $url);
    echo $FULL_PAGE;

    // Create "cached" page =)
    create_static_page($FULL_PAGE, $_SERVER['DOCUMENT_ROOT'].'/'.$url);
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
