<?php
# Теги BBCode

#######################################################################
//// Простые
$bbcode->addCode ('h1', 'simple_replace', null, array ('start_tag' => '<h1>', 'end_tag' => '</h1>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('h2', 'simple_replace', null, array ('start_tag' => '<h2>', 'end_tag' => '</h2>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('h3', 'simple_replace', null, array ('start_tag' => '<h3>', 'end_tag' => '</h3>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('h4', 'simple_replace', null, array ('start_tag' => '<h4>', 'end_tag' => '</h4>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('h5', 'simple_replace', null, array ('start_tag' => '<h5>', 'end_tag' => '</h5>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('h6', 'simple_replace', null, array ('start_tag' => '<h6>', 'end_tag' => '</h6>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('h7', 'simple_replace', null, array ('start_tag' => '<h7>', 'end_tag' => '</h7>'), 'inline', array ('block', 'inline'), array ());

$bbcode->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('i', 'simple_replace', null, array ('start_tag' => '<i>', 'end_tag' => '</i>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('u', 'simple_replace', null, array ('start_tag' => '<u>', 'end_tag' => '</u>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('s', 'simple_replace', null, array ('start_tag' => '<s>', 'end_tag' => '</s>'), 'inline', array ('block', 'inline'), array ());

$bbcode->addCode ('p', 'simple_replace', null, array ('start_tag' => '<p>', 'end_tag' => '</p>'), 'inline', array ('block', 'inline'), array ());

$bbcode->addCode ('ul', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('li', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'), 'inline', array ('block', 'inline'), array ());

$bbcode->addCode ('small', 'simple_replace', null, array ('start_tag' => '<small>', 'end_tag' => '</small>'), 'inline', array ('block', 'inline'), array ());
$bbcode->addCode ('center', 'simple_replace', null, array ('start_tag' => '<center>', 'end_tag' => '</center>'), 'inline', array ('block', 'inline'), array ());

#######################################################################
//// Сложные

// Гиперссылки
$bbcode->addCode ('url', 'usecontent?', 'bb_parse_url', array ('usecontent_param' => 'default'), 'link', array ('block', 'inline'), array ('link'));

// Учим парсер разбирать видео
$bbcode->addCode ('video', 'usecontent?', 'bb_parse_video', array ('usecontent_param' => 'default'), 'link', array ('block', 'inline'), array ('link'));

?>
