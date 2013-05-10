<?php
################################################################
// Session
session_start();
unset($_SESSION['script_header']);
unset($_SESSION['script_footer']);

################################################################
## //Config
$_SERVER['engine']['version'] = '0.1.6';
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

function build_header ($url)
{
    $content = "[ <a href='/' title='Home'>home</a> | <a href='/video' title='Video'>video</a> ][ <a href='/blog' title='Blog'>blog</a> | <a href='/wiki' title='Wiki'>wiki</a> ][ <a href='http://cyberbrain.dyndns.org/downloads' title='Downloads'>downloads</a> | <a href='http://cyberbrain.dyndns.org:8000' title='Radio'>radio</a> | <a href='http://ethereal.dyndns.info' title='Etherial'>etherial</a> ]";
    return $content;
}

function build_footer ()
{
    $content = '<small>Skynet <a href="http://code.google.com/p/zyxel-keenetic-packages/" title="Zyxmon&#039;s packages (russian)">gateway is running</a> on assimilated <a href="http://zyxel.ru/keenetic-giga" title="ZyXEL KEENETIC Giga (russian)">wireless internet router</a>. Would you like to <a href="http://forum.zyxmon.org/forum6-marshrutizatory-zyxel-keenetic.html" title="Zyxmon&#039;s KEENETIC forum (russian)">become a part of us</a>? # [ <a href="https://cyberbrain.dyndns.org:9091/transmission" title="Transmission">t</a> | <a href="https://cyberbrain.dyndns.org:9091" title="Router">r</a> | <a href="http://cyberbrain.dyndns.org/polygon" title="Polygon">p</a> ]</small>';
    return $content;
}

function build_head_tags ($title, $url)
{
    $head_tags = "<title>CyberBrain: ".$title."</title>";
    $head_tags = $head_tags."\n<link href =".$_SERVER['engine']['css']." rel='stylesheet' type='text/css'>";

    return $head_tags;
}

function build_page ($title, $body, $url)
{
    // Не сработает без reauire_once, который выше
    $body = parser($body,true);
    if (! isset($body))
        $body = '';

    $page = file_get_contents($_SERVER['engine']['template']);
    $page = str_replace('<!--REPLACE_HEAD_TAGS-->', build_head_tags($title,$url), $page);
    $page = str_replace('<!--REPLACE_SCRIPT_HEADER-->', @$_SESSION['script_header'], $page);
    $page = str_replace('<!--REPLACE_HEADER-->', build_header($url), $page);
    $page = str_replace('<!--REPLACE_BODY-->', $body, $page);
    $page = str_replace('<!--REPLACE_FOOTER-->', build_footer(), $page);
    $page = str_replace('<!--REPLACE_SCRIPT_FOOTER-->', @$_SESSION['script_footer'], $page);
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
    if (!empty($scripts)) {
        if (empty($_SESSION['script_header']))
            $_SESSION['script_header'] = $scripts[0];
        else
            $pos = strpos($_SESSION['script_header'], $scripts[0]);
            if (@$pos === false)
                $_SESSION['script_header'] = $_SESSION['script_header']."\n".$scripts[0];

        if (empty($_SESSION['script_footer']))
            $_SESSION['script_footer'] = $scripts[1];
        else
            $pos = strpos($_SESSION['script_footer'], $scripts[1]);
            if (@$pos === false)
                $_SESSION['script_footer'] = $_SESSION['script_footer']."\n".$scripts[1];
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
echo build_page($content['title'], $content['body'], $url);

################################################################
// Magic =)
unset($_SESSION['script_header']);
unset($_SESSION['script_footer']);
clearstatcache ();

?>
