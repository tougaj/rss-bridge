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

		$timezone = new DateTimeZone('Europe/Kyiv');
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

					$dateString = translate_date_to_english($time->plaintext);
					// 12:00, 16 june 2023
					// Формати тут: https://www.php.net/manual/en/datetime.format.php
					$date = DateTime::createFromFormat("H:i, j F Y", $dateString, $timezone);
					$isoDate = $date->format("Y-m-d\\TH:i:sO");
					// echo $isoDate;
					$item['timestamp'] = $isoDate;
					
					$content = $post_page->find('.editor-block')[0];
					if (!$content) continue;
					$item['content'] = $content->innertext;
				}
				$this->items[] = $item;
            // }
		}
    }
}
