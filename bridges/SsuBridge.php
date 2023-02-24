<?php

class SsuBridge extends BridgeAbstract
{
    const MAINTAINER = 'tougaj';
    const NAME = 'Офіційний сайт СБ України';
    const URI = 'https://www.ssu.gov.ua/novyny';
    const DESCRIPTION = 'News from www.ssu.gov.ua';
	const CACHE_TIMEOUT = 5;

    const PARAMETERS = [
		[
            'get_articles_text' => [
                'name' => 'Get articles text',
                'type' => 'checkbox',
            ]
		]
	];

    public function collectData()
    {
		$dom = defaultLinkTo(getSimpleHTMLDOM('https://www.ssu.gov.ua/novyny'), 'https://www.ssu.gov.ua/');
		if (!isset($dom)) returnClientError('Your error message');

		foreach ($dom->find('article.news-preview') as $post) {
			$title = $post->find('a.news-title')[0];
			if (!$title) continue;
			// foreach ($post->find('a.news-title') as $title) {
				$item = [];

				$item['title'] = $title->plaintext;

				$item['id'] = $title->getAttribute('href');

				$item['uri'] = $title->getAttribute('href');

				$time = $post->find('.news-date')[0];
				$time = $time ? $time->plaintext : '';
				$item['timestamp'] = translate_date_to_english($time);

				if ($this->getInput('get_articles_text')){
					$post_page = getSimpleHTMLDOMCached($item['uri']);

					$time = $post_page->find('time')[0];
					if (!$time) continue;
					$item['timestamp'] = translate_date_to_english($time->plaintext);
					
					$content = $post_page->find('.editor-block')[0];
					if (!$content) continue;
					$item['content'] = $content->outertext;
				}
				$this->items[] = $item;
            // }
		}
    }
}

function translate_date_to_english($string) {
	$string = mb_strtolower($string); // переводим строку в нижний регистр
	$string = str_replace(array('года', 'год', 'року', 'рік'), '', $string); // удаляем слова "года" и "год"

	// заменяем русские названия месяцев на английские
	$string = str_replace(
		array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'),
		array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'),
		$string
	);

	$string = str_replace(
		array('січня', 'лютого', 'березня', 'квітня', 'травня', 'червня', 'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня'),
		array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'),
		$string
	);

	$timestamp = strtotime($string);
	if ($timestamp !== false) {
		return $string;
	}
	return date('Y-m-d\TH:i:sO');

	$today = time();
	switch ($string) {
		case 'сегодня':
		case 'сьогодні':
			return $today;
		case 'завтра':
			return strtotime('+1 day', $today);
		case 'вчера':
		case 'вчора':
			return strtotime('-1 day', $today);
		default:
			return $today;
	}
}