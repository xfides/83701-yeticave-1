<?
// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

// записать в эту переменную оставшееся время в этом формате (ЧЧ:ММ)
$lot_time_remaining = "00:00";

// временная метка для полночи следующего дня
$tomorrow = strtotime('tomorrow midnight');

// временная метка для настоящего времени
$now = strtotime('now');

// далее нужно вычислить оставшееся время до начала следующих суток и записать его в переменную $lot_time_remaining
$lot_time_remaining = gmdate("H:i", $tomorrow - $now);

$goodsCategories = [
    'Доски и лыжи',
    'Крепления',
    'Ботинки',
    'Одежда',
    'Инструменты',
    'Разное'
];

$classesForCategories = [
    'promo__item--boards',
    'promo__item--attachment',
    'promo__item--boots',
    'promo__item--clothing',
    'promo__item--tools',
    'promo__item--other',
];

return [
    'goodsCategories' => $goodsCategories,
    'classesForCategories'=> $classesForCategories,
    'lot_time_remaining'=> $lot_time_remaining
];


?>