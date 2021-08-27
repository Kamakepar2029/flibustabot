<?php
echo('<pre>');
require('../../../configs/vt.php');
require('../telegram_library.php');
require('simple_html_dom.php');
$url = 'http://kakoysegodnyaprazdnik.ru/';
$domain = parse_url($url)['host'];
$limit = 30;
$count = 0;
$links_1 = get_inner_links($url);
$links_2 = [];
foreach ($links_2 as $link) {
	if ($count = $limit) {
		break;
	}
	$count++;
	$links_2[] = get_inner_links($link);
}
$links = array_merge($links_1, $links_2);

function get_http_code($url) {
	$headers = get_headers($url);
	return (int) mb_substr($headers[0], 9, -3);
}
function get_inner_links($url) {
	$domain = parse_url($url)['host'];
	$html = new simple_html_dom();
	$html->load_file($url);
	$a = $html->find('a[href*='.$domain.']');
	foreach ($a as $link) {
		$html->load_file($link->href);
		if (is_null($html->find('title', 0))) {
			$title = NULL;
		}
		else {
			$title = $html->find('title', 0);
			$title = $title->plaintext;
		}
		if (is_null($html->find('meta[name="keywords"]', 0))) {
			$keywords = NULL;
		}
		else {
			$keywords = $html->find('meta[name="keywords"]', 0);
			$keywords = $keywords->getAttribute('content');
		}
		$output[$link->href]= ['title'=>$title, 'keywords'=>$keywords];
	}
	// foreach ($links as $link) {
	//     if (strpos($link, $domain) !== false) {
	// 		$output[] = build_url($url);
	//     }
	// }
	return $output;
}
function build_url($url) {
	$components = parse_url($url);
	if ($components == false) {
		return false;
	}
	if (isset($components['query'])) {
		return 'http://'.$components['host'].$components['path'].$components['query'];
	}
	else {
		return 'http://'.$components['host'].$components['path'];
	}
}