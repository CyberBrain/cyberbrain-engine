<?php

// Подключаем библиотеку BBparser =)

require_once ($ENGINE['path']."/thirdparty/stringparser_bbcode/stringparser_bbcode.class.php");

##################################################
// Разбор страницы
function parser ($text)
{
    global $ENGINE;

    // Подключаем обработчики
    require_once ($ENGINE['path']."/lib/parser/callbacks.php");

    ##################################################
    // Создаём объект BBparser
    $bbcode = new StringParser_BBCode ();

    ##################################################
    // Добавляем объекту класса понятие о тэгах
    require_once ($ENGINE['path']."/lib/parser/bbcodes.php");

    ##################################################
    // Удаляем имеющиеся лишние переводы строк
    $text = str_replace('><br>', '>', $text);
    $text = str_replace('><br/>', '>', $text);
    $text = str_replace('><br />', '>', $text);

    // Перепиливаем теги в спецсимволы
    $text = htmlentities($text,ENT_QUOTES);
    $text = str_replace('&quot;', '"', $text);
    $text = str_replace('&lt;', '<', $text);
    $text = str_replace('&gt;', '>', $text);

    // И спецсимволы в теги
    $text = nl2br($text);

    // Удаляем получившиеся лишние переводы строк
    $text = str_replace(']<br>', ']', $text);
    $text = str_replace(']<br/>', ']', $text);
    $text = str_replace(']<br />', ']', $text);

    // Парсим тело сообщения
    $text = $bbcode->parse ($text);

    ##################################################

    unset($bbcode);

    return $text;
}

?>
