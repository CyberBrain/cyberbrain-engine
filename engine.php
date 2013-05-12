<?php

// Session
#session_start();

################################################################
## // Version
$ENGINE['version'] = '0.1.8';

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
// Global scripts

$script_header = '';
$script_footer = '';

################################################################
// Get url
if (isset($argv[0])) {
    if (isset($argv[1]))
        $_GET['url'] = $argv[1];
}

$url = isset($_GET['url']) ? escapeshellcmd(strip_tags(urldecode($_GET['url']))) : '';

################################################################
// functions =)

// function parser ($body)
require_once ($ENGINE['path']."/libs/parser.php");
////

function build_head_tags ($title, $url)
{
    global $ENGINE;

    $head_tags = "<title>CyberBrain: ".$title."</title>";
    $head_tags = $head_tags."\n".'<link href ="'.$ENGINE['css'].'" rel="stylesheet" type="text/css" />';
    if (!stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml"))
        $head_tags = $head_tags."\n".'<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    else
        $head_tags = $head_tags."\n".'<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />';
    return $head_tags;
}


function body_join($body)
{
    global $ENGINE;

    // Header & footer
    $page_header = file_get_contents($ENGINE['pages']."/header.txt");
    $page_footer = file_get_contents($ENGINE['pages']."/footer.txt");

    $page_header = str_replace('<!--HEADER-->', '', $page_header);
    $page_header = str_replace('<!--FOOTER-->', '', $page_header);

    $full = str_replace('<!--HEADER-->', '', $body);
    $body = str_replace('<!--FOOTER-->', '', $body);

    $page_footer = str_replace('<!--HEADER-->', '', $page_footer);
    $page_footer = str_replace('<!--FOOTER-->', '', $page_footer);

    $body = $page_header.'<!--HEADER-->'.$body.'<!--FOOTER-->'.$page_footer;

    return $body;
}

function body_split($body)
{
    $body = str_replace('&lt;!--HEADER--&gt;', '<!--HEADER-->', $body);
    $body = str_replace('&lt;!--FOOTER--&gt;', '<!--FOOTER-->', $body);

    $body = explode ('<!--HEADER-->', $body);

    $content['header'] = $body[0];

    $body = explode ('<!--FOOTER-->', $body[1]);

    $content['body'] = $body[0];
    $content['footer'] = $body[1].'<small><p><i>Page was built by CyberBrain engine version '.$ENGINE['version'].' at '.date('Y/m/d H:i:s').'.</i></p></small>';

    unset($body);

    return $content;
}

function build_page ($title, $body, $url)
{
    global $ENGINE, $script_header, $script_footer;

    $content = body_join($body);
    unset($body);
    $content = parser($content);
    $content = body_split($content);


    $page = file_get_contents($ENGINE['template']);
    $page = str_replace('<!--REPLACE_HEAD_TAGS-->', build_head_tags($title,$url), $page);
    $page = str_replace('<!--REPLACE_SCRIPT_HEADER-->', $script_header, $page);
    $page = str_replace('<!--REPLACE_HEADER-->', $content['header'], $page);
    $page = str_replace('<!--REPLACE_BODY-->', $content['body'], $page);
    $page = str_replace('<!--REPLACE_FOOTER-->', $content['footer'], $page);
    $page = str_replace('<!--REPLACE_SCRIPT_FOOTER-->', $script_footer, $page);
    return $page;
}

function get_scripts ($type)
{
    global $ENGINE;

    if (!empty($type)) {
        $script_address = $ENGINE['scripts']."/".$type.".script";
        if (file_exists($script_address)) {
            $scripts = file_get_contents($script_address);
            $scripts = explode ('<!--SEPARATOR-->', $scripts); } }

    if (empty($scripts[0]))
        $scripts[0] = '';
    else
        $scripts[0] = trim($scripts[0]);

    if (empty($scripts[1]))
        $scripts[1] = '';
    else
        $scripts[1] = trim($scripts[1]);

    return $scripts;
}

function publish_scripts ($scripts)
{
    global $script_header, $script_footer;

    if (!empty($scripts)) {
        if (!empty($scripts[0])) {
            if (empty($script_header))
                $script_header = $scripts[0];
            else
                $pos = strpos($script_header, $scripts[0]);
                if (@$pos === false)
                    $script_header = $script_header."\n".$scripts[0]; }

        if (!empty($scripts[1])) {
            if (empty($script_footer))
                $script_footer = $scripts[1];
            else
                $pos = strpos($script_footer, $scripts[1]);
                if (@$pos === false)
                    $script_footer = $script_footer."\n".$scripts[1]; }
    }
}

function get_content ($page_address)
{
    $content_unparsed = file_get_contents($page_address);
    $content_unparsed = explode ('<!--SEPARATOR-->', $content_unparsed);
    $content['title'] = isset($content_unparsed[0]) ? trim(str_replace("\n", "", strip_tags(urldecode($content_unparsed[0])))) : 'o_O';
    $content['body'] = isset($content_unparsed[1]) ? trim($content_unparsed[1]) : 'O_o';
    unset($content_unparsed);
    return $content;
}

################################################################
// "Cache" functions

function create_static_page($content,$url)
{
    if (!empty($url)) {
/*        if (!stristr($url,"index.php")) {
            $url = str_replace('index.php?url=', '', $url);
            $url = str_replace('index.php?', '', $url);
            $url = str_replace('index.php', '', $url);
        } */
        $dirname = $_SERVER['DOCUMENT_ROOT'].'/'.$url;
        if (!file_exists($dirname))
            mkdir($dirname, 0755, true);
        else
            if (!is_dir($dirname))
                return; }
    else
        $dirname = $_SERVER['DOCUMENT_ROOT'];

    $filename = $dirname.'/index.htm';
    file_put_contents($filename,$content);
}

################################################################
// Working with URLs
if (! empty($url)) {
    $page_address = $ENGINE['pages']."/".$url.".txt";
    if (! file_exists($page_address))
        $page_address = $ENGINE['pages']."/".$url."/index.txt";
}
else
    $page_address = $ENGINE['pages']."/index.txt";

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
    create_static_page($FULL_PAGE,$url);
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
