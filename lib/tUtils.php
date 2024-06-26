<?php

function translate_date_to_english($string) {
	$string = mb_strtolower($string); // переводим строку в нижний регистр
	$string = str_replace(array('года', 'год', 'року', 'рік'), '', $string); // удаляем слова "года" и "год"

	// змінюємо російські назви місяців на англійські
	$string = str_replace(
		array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'),
		array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'),
		$string
	);

	// змінюємо українські назви місяців на англійські
	$string = str_replace(
		array('січня', 'лютого', 'березня', 'квітня', 'травня', 'червня', 'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня'),
		array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'),
		$string
	);

	// змінюємо нідерландські назви місяців на англійські
	$string = str_replace(
		array('januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'),
		array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'),
		$string
	);
	
	return $string;

	// $timestamp = strtotime($string);
	// if ($timestamp !== false) {
	// 	return $string;
	// }
	// return date($format);

	// $today = time();
	// switch ($string) {
	// 	case 'сегодня':
	// 	case 'сьогодні':
	// 		return $today;
	// 	case 'завтра':
	// 		return strtotime('+1 day', $today);
	// 	case 'вчера':
	// 	case 'вчора':
	// 		return strtotime('-1 day', $today);
	// 	default:
	// 		return $today;
	// }
}