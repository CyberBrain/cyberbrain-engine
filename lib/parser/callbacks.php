<?php

// Счётчик для [video]
$video_counter = '';

// Обработчик для [video]
function bb_parse_video ($action, $attributes, $content, $params, &$node_object)
{
    if ($action == 'validate')
        return true;
    else {
        global $video_counter;
        $video_counter = $video_counter+1;

        $tag_full = '<div class="video" ';
        // Переборка скрипта и <div>
        $scripts = scripts_get('video');

        $scripts[1] = $scripts[1].'<script type="text/javascript">';

        $scripts[1] = $scripts[1].'jwplayer("video-'.$video_counter.'").setup({';
        $tag_full = $tag_full.'id="video-'.$video_counter.'"';

        if (!empty($attributes['source'])) {
            $pos = strripos($attributes['source'], '.rss');
                if ($pos === false) {
                    $scripts[1] = $scripts[1].' file: "'.$attributes['source'].'",'; }
                else {
                    $scripts[1] = $scripts[1].' playlist: "'.$attributes['source'].'",'; } }

        if (!empty($attributes['image'])) {
            $scripts[1] = $scripts[1].' image: "'.$attributes['image'].'",';
            $img = '<img src="'.$attributes['image'].'"'; }

        if (!empty($attributes['width'])) {
            $scripts[1] = $scripts[1].' width: "'.$attributes['width'].'",';
            $img = $img.' height="'.$attributes['width'].'"'; }

        if (!empty($attributes['height'])) {
            $scripts[1] = $scripts[1].' height: "'.$attributes['height'].'",';
            $img = $img.' height="'.$attributes['height'].'"'; }

        if (!empty($attributes['volume'])) {
            $scripts[1] = $scripts[1].' volume: "'.$attributes['volume'].'",'; }

        if (!empty($attributes['autostart'])) {
            $scripts[1] = $scripts[1].' autostart: "'.$attributes['autostart'].'",'; }

        if (!empty($attributes['listbar'])) {
            $scripts[1] = $scripts[1].' listbar: { position: "right", size: "'.$attributes['listbar'].'", },'; }

        if (stristr($scripts[1], 'playlist:')) {
            $scripts[1] = $scripts[1].' primary: "flash", rtmp: { bufferlength: "5", },'; }
        else {
            if (stristr($scripts[1], 'rtmp://')) {
                $scripts[1] = $scripts[1].' primary: "flash", rtmp: { bufferlength: "5", },'; } }

        $scripts[1] = $scripts[1].' startparam: "start", controlbar: "over", });</script>';

        $scripts = scripts_publish($scripts);

        if (!empty($attributes['align'])) {
            $tag_full = $tag_full.' align="'.$attributes['align'].'"'; }
        if (!empty($attributes['valign'])) {
            $tag_full = $tag_full.' align="'.$attributes['valign'].'"'; }

        $tag_full = $tag_full.'>';

        if (stristr($img, '<img ')) {
            if (!empty ($content)) {
                $img = $img.' alt="'.htmlspecialchars($content).'"'; }
            $img = $img.'></img>';
            $tag_full = $tag_full.$img; }
        else {
            if (!empty ($content)) {
                $tag_full = $tag_full.htmlspecialchars($content); } }

        $tag_full = $tag_full.'</div>';

        return $tag_full; }
}

// Обработчик для [url]
function bb_parse_url ($action, $attributes, $content, $params, &$node_object) {
    // 1) the code is being valided
    if ($action == 'validate')
        return true;
    else {
        if (!empty($attributes['default']))
            $tag_full = 'href="'.$attributes['default'].'" title="'.htmlspecialchars($content).'"';
        else
            $tag_full = 'href="'.$content.'"';

        $out ='<a '.$tag_full.'>'.htmlspecialchars($content).'</a>';

        return $out;
    }
}

?>
