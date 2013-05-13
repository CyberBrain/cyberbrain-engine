<?php

function scripts_get ($type)
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

function scripts_publish ($scripts)
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


function page_body_join($body)
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

function page_body_split($body)
{
    global $ENGINE;

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


function page_build ($title, $body, $url)
{
    global $ENGINE, $script_header, $script_footer;

    $content = page_body_join($body);
    unset($body);
    $content = parser($content);
    $content = page_body_split($content);


    $page = file_get_contents($ENGINE['template']);
    $page = str_replace('<!--REPLACE_HEAD_TAGS-->', page_head_tags($title,$url), $page);
    $page = str_replace('<!--REPLACE_SCRIPT_HEADER-->', $script_header, $page);
    $page = str_replace('<!--REPLACE_HEADER-->', $content['header'], $page);
    $page = str_replace('<!--REPLACE_BODY-->', $content['body'], $page);
    $page = str_replace('<!--REPLACE_FOOTER-->', $content['footer'], $page);
    $page = str_replace('<!--REPLACE_SCRIPT_FOOTER-->', $script_footer, $page);
    return $page;
}


function page_cache ($content, $cache_path)
{
    if (!file_exists($cache_path))
        mkdir($cache_path, 0755, true);
    else
        if (!is_dir($cache_path))
            return false;

    $filename = $cache_path.'/index.htm';
    file_put_contents($filename, $content);
    return true;
}


function page_content ($page_address)
{
    $content_unparsed = file_get_contents($page_address);
    $content_unparsed = explode ('<!--SEPARATOR-->', $content_unparsed);
    $content['title'] = isset($content_unparsed[0]) ? trim(str_replace("\n", "", strip_tags(urldecode($content_unparsed[0])))) : 'o_O';
    $content['body'] = isset($content_unparsed[1]) ? trim($content_unparsed[1]) : 'O_o';
    unset($content_unparsed);
    return $content;
}


function page_head_tags ($title, $url)
{
    global $ENGINE;

    $head_tags = "<title>CyberBrain: ".$title."</title>";
    $head_tags = $head_tags."\n".'<link href ="'.$ENGINE['css'].'" rel="stylesheet" type="text/css" />';
    $head_tags = $head_tags."\n".'<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    return $head_tags;
}


function page_all ($page_address, $url)
{
    // Get content
    $content = page_content($page_address);

    // Build page
    $FULL_PAGE = page_build($content['title'], $content['body'], $url);

    // Save "cache"
    page_cache($FULL_PAGE, $_SERVER['DOCUMENT_ROOT'].'/'.$url);

    return $FULL_PAGE;
}

?>
