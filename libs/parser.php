<?php

// Подключаем библиотеку BBparser =)

require_once ($_SERVER['engine']['path']."/libs/stringparser_bbcode/stringparser_bbcode.class.php");


##################################################
// Разбор страницы
function parser ($text, $nl_2_br = false)
{
    // Создаём объект BBparser
    $bbcode = new StringParser_BBCode ();

    ##################################################
    # Подключаем обработчики
    require_once ($_SERVER['engine']['path']."/libs/parser_callbacks.php");

    ##################################################
    //Добавляем объекту класса понятие о тэгах
    require_once ($_SERVER['engine']['path']."/libs/parser_bbcodes.php");

    ##################################################
    // Удаляем имеющиеся лишние переводы строк
    $text = str_replace('><br>', '>', $text);
    $text = str_replace('><br/>', '>', $text);
    $text = str_replace('><br />', '>', $text);

    // Перепиливаем теги в спецсимволы
    $text = htmlentities($text,ENT_QUOTES);
    $text = str_replace('&quot;', '"', $text);

    // И спецсимволы в теги
    if ($nl_2_br === true)
        $text = nl2br($text);

    // Удаляем получившиеся лишние переводы строк
    $text = str_replace(']<br>', ']', $text);
    $text = str_replace(']<br/>', ']', $text);
    $text = str_replace(']<br />', ']', $text);

    // Парсим тело сообщения
    $text = $bbcode->parse ($text);

    ##################################################

    return $text;
}

?>
