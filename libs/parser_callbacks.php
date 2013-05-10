<?php

// Обработчик для [myvideo]
function bb_parse_video ($action, $attributes, $content, $params, &$node_object)
{
    if ($action == 'validate')
        return true;
    else {
        $tag_full = '<div ';
        // Переборка скрипта и <div>
        $scripts = get_scripts('video');

        $scripts[1] = $scripts[1].'<script type="text/javascript">';

        if (!empty($attributes['id'])) {
            $scripts[1] = $scripts[1].'jwplayer("video-'.$attributes['id'].'").setup({';
            $tag_full = $tag_full.'id="video-'.$attributes['id'].'"'; }
        else {
            $scripts[1] = 'jwplayer("video-1").setup({';
            $tag_full = $tag_full.'id="video-1"'; }

        if (!empty($attributes['source'])) {
            $pos = strripos($attributes['source'], '.rss', 1);
                if ($pos === false)
                    $scripts[1] = $scripts[1].' file: "'.$attributes['source'].'",';
                else
                    $scripts[1] = $scripts[1].' playlist: "'.$attributes['source'].'",'; }

        if (!empty($attributes['image']))
            $scripts[1] = $scripts[1].' image: "'.$attributes['image'].'",';
        if (!empty($attributes['width']))
            $scripts[1] = $scripts[1].' width: "'.$attributes['width'].'",';
        if (!empty($attributes['height']))
            $scripts[1] = $scripts[1].' height: "'.$attributes['height'].'",';
        if (!empty($attributes['volume']))
            $scripts[1] = $scripts[1].' volume: "'.$attributes['volume'].'",';
        if (!empty($attributes['autostart']))
            $scripts[1] = $scripts[1].' autostart: "'.$attributes['autostart'].'",';

        if (!empty($attributes['listbar']))
            $scripts[1] = $scripts[1].' listbar: { position: "right", size: "'.$attributes['listbar'].'", },';

        $scripts[1] = $scripts[1].' primary: "flash", startparam: "start", controlbar: "over", rtmp: { bufferlength: "5", }, });</script>';

        $scripts = publish_scripts($scripts);

        if (!empty($attributes['align']))
            $tag_full = $tag_full.' align="'.$attributes['align'].'"';
        if (!empty($attributes['valign']))
            $tag_full = $tag_full.' align="'.$attributes['valign'].'"';

        $tag_full = $tag_full.'>';

        if (!empty ($content))
            $tag_full = $tag_full.htmlspecialchars($content).'</div>';
        else
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
            $tag_full = 'href="'.htmlspecialchars($attributes['default']).'" title="'.htmlspecialchars($content).'"';
        else
            $tag_full = 'href="'.htmlspecialchars($content).'"';

        $out ='<a '.$tag_full.'>'.$content.'</a>';

        return $out;
    }
}

?>
