<?php

class NovynyLiveBridge extends BridgeAbstract
{
    const MAINTAINER = 'tougaj';
    const NAME = 'Новини.LIVE';
    const URI = 'https://novyny.live/news/';
    const DESCRIPTION = 'News from Novyny.LIVE';
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
		$dom = defaultLinkTo(getSimpleHTMLDOM('https://novyny.live/news/'), 'https://novyny.live/');
		if (!isset($dom)) returnClientError('Your error message');

		$timezone = new DateTimeZone('Europe/Kyiv');
		foreach ($dom->find('.all-news__item') as $post) {
			$title = $post->find('.all-news__item-title')[0];
			if (!$title) continue;
			// foreach ($post->find('a.news-title') as $title) {
				$item = [];

				$item['title'] = $title->plaintext;

				$item['id'] = $post->getAttribute('href');

				$item['uri'] = $post->getAttribute('href');

				if ($this->getInput('get_articles_text')){
					$post_page = getSimpleHTMLDOMCached($item['uri']);

					$content = $post_page->find('.content')[0];
					if (!$content) continue;

					$date_string = $content->find('.content__info-create')[0]->plaintext;
					if ($date_string) {
						$date_string = translate_date_to_english($date_string);
						// Оригінальна дата має вигляд: 15 вересня 2023 13:55
						// Формати тут: https://www.php.net/manual/ru/datetimeimmutable.createfromformat.php
						$date = DateTime::createFromFormat('j F Y G:i', $date_string, $timezone);
						$iso_date = $date->format("Y-m-d\\TH:i:sO");
						$item['timestamp'] = $iso_date;
					}

					$garbage = array_merge(
						$content->find('.content__sticky'),
						$content->find('.read-more'),
						$content->find('.content__subscribe'),
						$content->find('.article-categories'),
						$content->find('.content__hint'),
						$content->find('figcaption'),
						$content->find('iframe'),
					);
					foreach ($garbage as $key => $value) {
						$value->remove();
					}
					$item['content'] = $content->find('.content__container')[0]->innertext;
				}
				$this->items[] = $item;
            // }
		}
    }
}
