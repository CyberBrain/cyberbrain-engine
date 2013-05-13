<?php

function time_stamp()
{
    return '<p align="right"><small><i>Page was built by CyberBrain engine version '.$ENGINE['version'].' at '.date('Y/m/d H:i:s').'.</i></small></p>';
}

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


function page_build ($content, $url)
{
    global $ENGINE, $script_header, $script_footer;

    if (!empty($content['header']))
        $fullbody = $fullbody.'<div class="header">'.$content['header'].'</div>'."\n";
    else
        if (!empty($content['title']))
            $fullbody = $fullbody.'<div class="header">'.$content['title'].'</div>'."\n";

    if (!empty($content['body']))
        $fullbody = $fullbody.'<div class="body">'.$content['body'].'</div>'."\n";

    if (!empty($content['footer']))
        $fullbody = $fullbody.'<div class="footer">'.$content['footer'].'<br>'.time_stamp().'</div>';
    else
        $fullbody = $fullbody.'<div class="footer">'.time_stamp().'</div>';

    $page = file_get_contents($ENGINE['includes'].'/'.$ENGINE['template']);
    $page = str_replace('<!--REPLACE_HEAD_TAGS-->', page_head_tags($content['title'],$url), $page);
    $page = str_replace('<!--REPLACE_SCRIPT_HEADER-->', $script_header, $page);
    $page = str_replace('<!--REPLACE_MENU_TOP-->', $content['menu_top'], $page);
    $page = str_replace('<!--REPLACE_BODY-->', $fullbody, $page);
    $page = str_replace('<!--REPLACE_MENU_BOTTOM-->', $content['menu_bottom'], $page);
    $page = str_replace('<!--REPLACE_SCRIPT_FOOTER-->', $script_footer, $page);

    unset($fullbody);
    unset($content);
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


function page_content ($page_address, $url)
{
    global $ENGINE;

    $content_unparsed = file_get_contents($page_address);

    // top & bottom menu
    $menu_top = menu_top_get($url);
    $menu_bottom = menu_bottom_get($url);

    $content_unparsed = '[menu_top]'.$menu_top.'[/menu_top]'.$content_unparsed;
    $content_unparsed = $content_unparsed.'[menu_bottom]'.$menu_bottom.'[/menu_bottom]';

    $content_unparsed = parser($content_unparsed);

    if (stristr($content_unparsed,'[menu_top]')) {
        $content_unparsed = str_replace('[menu_top]', '', $content_unparsed); }
    if (stristr($content_unparsed,'[/menu_top]')) {
        $content_unparsed = explode ('[/menu_top]', $content_unparsed);
        $content['menu_top'] = isset($content_unparsed[0]) ? trim($content_unparsed[0]) : '';
        $content_unparsed = isset($content_unparsed[1]) ? trim($content_unparsed[1]) : ''; }

    if (stristr($content_unparsed,'[title]')) {
        $content_unparsed = str_replace('[title]', '', $content_unparsed); }
    if (stristr($content_unparsed,'[/title]')) {
        $content_unparsed = explode ('[/title]', $content_unparsed);
        $content['title'] = isset($content_unparsed[0]) ? trim($content_unparsed[0]) : '';
        $content_unparsed = isset($content_unparsed[1]) ? trim($content_unparsed[1]) : ''; }

    if (stristr($content_unparsed,'[header]')) {
        $content_unparsed = str_replace('[header]', '', $content_unparsed); }
    if (stristr($content_unparsed,'[/header]')) {
        $content_unparsed = explode ('[/header]', $content_unparsed);
        $content['header'] = isset($content_unparsed[0]) ? trim($content_unparsed[0]) : '';
        $content_unparsed = isset($content_unparsed[1]) ? trim($content_unparsed[1]) : ''; }

    if (stristr($content_unparsed,'[body]')) {
        $content_unparsed = str_replace('[body]', '', $content_unparsed); }
    if (stristr($content_unparsed,'[/body]')) {
        $content_unparsed = explode ('[/body]', $content_unparsed);
        $content['body'] = isset($content_unparsed[0]) ? trim($content_unparsed[0]) : '';
        $content_unparsed = isset($content_unparsed[1]) ? trim($content_unparsed[1]) : ''; }

    if (stristr($content_unparsed,'[footer]')) {
        $content_unparsed = str_replace('[footer]', '', $content_unparsed); }
    if (stristr($content_unparsed,'[/footer]')) {
        $content_unparsed = explode ('[/footer]', $content_unparsed);
        $content['footer'] = isset($content_unparsed[0]) ? trim($content_unparsed[0]) : '';
        $content_unparsed = isset($content_unparsed[1]) ? trim($content_unparsed[1]) : ''; }

    if (stristr($content_unparsed,'[menu_bottom]')) {
        $content_unparsed = str_replace('[menu_bottom]', '', $content_unparsed); }
    if (stristr($content_unparsed,'[/menu_bottom]')) {
        $content_unparsed = explode ('[/menu_bottom]', $content_unparsed);
        $content['menu_bottom'] = isset($content_unparsed[0]) ? trim($content_unparsed[0]) : '';
        $content_unparsed = isset($content_unparsed[1]) ? trim($content_unparsed[1]) : ''; }

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


function page_all ($url)
{
    global $ENGINE;

    $page_address = $ENGINE['pages']."/".$url;

    if (file_exists($page_address)) {
        if (is_dir($page_address)) {
            $page_address = $page_address."/index";
                if (!file_exists($page_address)) {
                    return '404';
                }
        }
    }
    else
        return '404';

    // Get content
    $content = page_content($page_address, $url);

    // Default scripts
    scripts_publish(scripts_get($ENGINE['script_default']));

    // Build page
    $FULL_PAGE = page_build($content, $url);

    // Save "cache"
    page_cache($FULL_PAGE, $_SERVER['DOCUMENT_ROOT'].'/'.$url);

    // Return full page contents
    return $FULL_PAGE;
}


function menu_top_get ($url)
{
    global $ENGINE;
    return file_get_contents($ENGINE['includes']."/menu_top.txt");
}

function menu_bottom_get ($url)
{
    global $ENGINE;
    $menu = file_get_contents($ENGINE['includes']."/menu_bottom.txt");
    $menu = str_replace('<!--URL-->', $url, $menu);
    return $menu;
}

?>
