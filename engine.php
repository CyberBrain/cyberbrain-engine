<?php
################################################################
// Session
#session_start();

################################################################
## //Config
$_SERVER['engine']['version'] = '0.1.7';
## Be sure settings in index.php are correct!

## Default settings:
// Path to engine
$_SERVER['engine']['path'] = isset($_SERVER['engine']['path']) ? $_SERVER['engine']['path'] : $_SERVER['DOCUMENT_ROOT']."/engine";
// Path to pages
$_SERVER['engine']['pages'] = isset($_SERVER['engine']['pages']) ? $_SERVER['engine']['pages'] : $_SERVER['DOCUMENT_ROOT']."/pages";
// Path to script bundles
$_SERVER['engine']['scripts'] = isset($_SERVER['engine']['scripts']) ? $_SERVER['engine']['scripts'] : $_SERVER['DOCUMENT_ROOT']."/scripts";
// Default script
$_SERVER['engine']['default_script'] = isset($_SERVER['engine']['default_script']) ? $_SERVER['engine']['default_script'] : "common";
// Path to template
$_SERVER['engine']['template'] = isset($_SERVER['engine']['template']) ? $_SERVER['engine']['template'] : $_SERVER['DOCUMENT_ROOT']."/template.htm";
// Path to css file
$_SERVER['engine']['css'] = isset($_SERVER['engine']['css']) ? $_SERVER['engine']['css'] : "/style.css";

################################################################
// Global scripts

$script_header = '';
$script_footer = '';

################################################################

// Get url
$url = isset($_GET['url']) ? escapeshellcmd(strip_tags(urldecode($_GET['url']))) : '';
$place = escapeshellcmd(strip_tags(urldecode($_SERVER['REQUEST_URI'])));

if (! empty($url)) {
    $pos = strpos($place, $url);
    $link = substr ($place, $pos);
}

if (! isset($link))
    $link = '';

################################################################
// functions =)

// function parser ($body)
require_once ($_SERVER['engine']['path']."/libs/parser.php");

function build_head_tags ($title, $url)
{
    $head_tags = "<title>CyberBrain: ".$title."</title>";
    $head_tags = $head_tags."\n".'<link href ="'.$_SERVER['engine']['css'].'" rel="stylesheet" type="text/css" />';
    if (!stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml"))
        $head_tags = $head_tags."\n".'<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    else
        $head_tags = $head_tags."\n".'<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />';
    return $head_tags;
}


function body_join($body)
{
    // Header & footer
    $page_header = file_get_contents($_SERVER['engine']['pages']."/header.txt");
    $page_footer = file_get_contents($_SERVER['engine']['pages']."/footer.txt");

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
    $content['footer'] = $body[1];

    unset($body);

    return $content;
}

function build_page ($title, $body, $url)
{
    global $script_header, $script_footer;

    $content = body_join($body);
    unset($body);
    $content = parser($content);
    $content = body_split($content);


    $page = file_get_contents($_SERVER['engine']['template']);
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
    if (!empty($type)) {
        $script_address = $_SERVER['engine']['scripts']."/".$type.".script";
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
    return $content;
}

function http_error ($code)
{
    if ($code == '404') {
        header('HTTP/1.0 404 Not Found');
        header('Status: 404 Not Found'); }

    $page_address = $_SERVER['engine']['pages']."/errors/$code.txt";

    return $page_address;
}

################################################################
// Working with URLs
if (! empty($url)) {
    $page_address = $_SERVER['engine']['pages']."/".$url.".txt";
    if (! file_exists($page_address))
        $page_address = $_SERVER['engine']['pages']."/".$url."/index.txt";
}
else
    $page_address = $_SERVER['engine']['pages']."/index.txt";

if (!file_exists($page_address))
    $page_address = http_error('404');

$content = get_content($page_address);

################################################################
// Default scripts
publish_scripts(get_scripts($_SERVER['engine']['default_script']));


################################################################
// Output
header("Vary: Accept");
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml"))
    header("Content-Type: application/xhtml+xml; charset=UTF-8");
else
    header("Content-Type: text/html; charset=UTF-8");
echo parser(build_page($content['title'], $content['body'], $url));

################################################################
// Magic =)
unset($script_header);
unset($script_footer);
clearstatcache();

?>
