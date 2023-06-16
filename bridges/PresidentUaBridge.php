<?php

class PresidentUaBridge extends BridgeAbstract
{
    const MAINTAINER = 'tougaj';
    const NAME = 'Офіційний сайт Президента України';
    const URI = 'https://www.president.gov.ua/rss/news/all.rss';
    const DESCRIPTION = 'News from www.president.gov.ua';
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
		// Новини
		$dom = defaultLinkTo(getSimpleHTMLDOM('https://www.president.gov.ua/rss/news/all.rss'), 'https://www.president.gov.ua/');
		if (!isset($dom)) returnClientError('No news');

		foreach ($dom->find('item') as $post) {
			$item = get_post_data($post, $this->getInput('get_articles_text'));
			if ($item) $this->items[] = $item;
			// break;
		}

		// Документи
		$dom = defaultLinkTo(getSimpleHTMLDOM('https://www.president.gov.ua/rss/documents/all.rss'), 'https://www.president.gov.ua/');
		if (!isset($dom)) returnClientError('No documents');

		foreach ($dom->find('item') as $post) {
			$item = get_post_data($post, $this->getInput('get_articles_text'));
			if ($item) $this->items[] = $item;
			// break;
		}
    }
}


function get_post_data($post, $with_text){
	$title = $post->find('title')[0];
	if (!$title) return null;
	$item = [];

	$item['title'] = $title->plaintext;
	$link = $post->find('guid')[0];

	$item['id'] = $link->plaintext;
	$item['uri'] = $link->plaintext;

	$time = $post->find('pubDate')[0];
	if ($time){
		$dateString = $time->plaintext;
		$date = DateTime::createFromFormat('D, d M Y H:i:s O', $dateString);
		$isoDate = $date->format("Y-m-d\\TH:i:sO");
		$item['timestamp'] = $isoDate;
	}
	$item['title'] = $item['title'];

	if ($with_text){
		$post_page = getSimpleHTMLDOMCached($item['uri']);
		$descr = $post_page->find('.short_desc');
		$descr_text = ($descr && 0 < count($descr)) ? "<p><em>" . trim($descr[0]->plaintext) . "</em></p>" : '';

		$content = $post_page->find('.article_content')[0];
		if (!$content) return null;
		$i_fa = $content->find('i.fa', 0);
		if ($i_fa) $i_fa->remove();
		$item['content'] = $descr_text . trim($content->innertext);
	}
	return $item;
}