<?php

class SlovoIDiloBridge extends BridgeAbstract
{
    const MAINTAINER = 'tougaj';
    const NAME = 'Слово і Діло';
    const URI = 'https://www.slovoidilo.ua/publikacii';
    const DESCRIPTION = 'News from slovoidilo';
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
		$dom = defaultLinkTo(getSimpleHTMLDOM('https://www.slovoidilo.ua/publikacii'), 'https://www.slovoidilo.ua/');
		if (!isset($dom)) returnClientError('Your error message');

		$timezone = new DateTimeZone(date_default_timezone_get());
		foreach ($dom->find('.story') as $post) {
			$title = $post->find('.story-heading>a')[0];
			if (!$title) continue;
			// foreach ($post->find('a.news-title') as $title) {
				$item = [];

				$item['title'] = $title->plaintext;

				$item['id'] = $title->getAttribute('href');

				$item['uri'] = $title->getAttribute('href');

				$dateString = $post->find('time')[0];
				$dateString = $dateString->getAttribute('datetime');
				$date = DateTime::createFromFormat('Y-m-d H:i:s', $dateString, $timezone);
				$isoDate = $date->format('Y-m-d\TH:i:sO');
				echo $isoDate . ' ';
				$item['timestamp'] = $isoDate;

				$i = 0;
				if ($this->getInput('get_articles_text')){
					$post_page = getSimpleHTMLDOMCached($item['uri']);

					$content = $post_page->find('.article-body')[0];
					if (!$content) continue;

					$garbage = array_merge(
						$content->find('script'),
						$content->find('style'),
						$content->find('a[style="text-decoration:none"]')
					);
					foreach ($garbage as $key => $value) {
						$value->remove();
					}
					$item['content'] = $content->innertext;
				}
				$this->items[] = $item;
            // }
		}
    }
}
