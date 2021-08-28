<?php
include_once('telegrambot.php');
include_once('simple_html_dom.php');
$token = "1990222108:AAHwkxlU8PtdDruLVHgb2-Ka6qGD5eZ2xFQ";

if (isset($data['callback_query'])) {
	$callbdata = $data['callback_query'];
	$chatid = $callbdata["from"]["id"];
	if (count(explode('Durl:',$callbdata["data"]))==2){
		$url = 'https://flibusta.is/'.explode('Durl:',$callbdata["data"])[1];
		$ty = file_get_contents($url);
		$titles = explode('</h1', explode('class="title">',$ty)[1])[0];
		$download = explode('</div>',explode('скачать:', $ty)[1])[0];
		$as = explode('<a', $download);
		$keyboards = [];
		foreach ($as as $a){
			$aa = explode('</a>', $a)[0];
			$title = '<div>'.explode('</a>', explode('>',$aa)[1])[0].'</div>';
			$gty = str_get_html($title);
			$title = ($gty->find('div')[0])->innertext;
			$href = explode('"',explode('href=', $aa)[1])[1];
			$keyboards[count($keyboards)] = new_inline($title, 'callback_data', 'Downurl:'.$href.'|'.$titles.'.'.str_replace(')','',str_replace('(','',$title)));
		}
		sendMessage_inline($chatid, 'Скачать '.$titles.':', $token, $keyboards);
	}
	if (count(explode('Downurl:',$callbdata["data"]))==2){
		$dwu = explode('Downurl:',$callbdata["data"])[1];
		$urio = explode('|',$dwu);
		$keyboards = [new_inline('Скачать', 'url', 'https://flibbot.herokuapp.com/'.$urio[1])];
		sendMessage_inline($chatid, 'Попытаюсь скачать: '.$titles.":\n".'https://flibusta.is/'.$urio[0], $token);
		$downloadedFileContents = file_get_contents('https://flibusta.is'.$urio[0]);
		if($downloadedFileContents === false){
		    echo ('Failed to download file at: ' . $url);
		}
		$fileName = $urio[1];
		$save = file_put_contents($fileName, $downloadedFileContents);
		if($save === false){
		    echo ('Failed to save file to: '.$fileName);
		}
		sendMessage_inline($chatid, 'Скачать '.$titles.':', $token, $keyboards);
	}
}

if($msg == '/start'){
	sendMessage($chatid, "Здравствуйте, вас приветствует бот бесплатных книг flibusta\nЧтобы найти книги, просто напишите название книги или автора",$token);
}else{
	if (count(explode('/downloadbookhref-', $msg)) == 2){
		sendMessage($chatid, explode('/downloadbookhref-', $msg)[1], $token);
	}else{
		$f = file_get_contents('https://flibusta.is/booksearch?ask='.urlencode($msg));
		//$html = str_get_html($f);
		//$html = $html->find('#main')[0];
		//$res = $html->find('ul li a');
		$res = explode('Найденные', $f);
		file_put_contents('me.hj', json_encode($res));
		$start = 1;
		$end = count($res);
		while ($start < $end){
			if (count(explode('книг',explode('</h3>',$res[$start])[0]))==2){
				$keyboards = [];
				$rst = explode('blockinner',$res[$start])[0];
				$el = explode('<li>',$rst);
				foreach ($el as $e){
					$r = explode('</li>',$e)[0];
					$href = explode('"',explode('href', $r)[1])[1];
					$text = explode('</a>',explode('">', $r)[1])[0];
					$text = str_replace('</b>','',str_replace('<b>', '', $text));
					$keyboards[count($keyboards)] = new_inline($text, 'callback_data', 'Durl:'.$href);
				} 
				sendMessage_inline($chatid, 'Найденные '.explode('</h3>',$res[$start])[0], $token, $keyboards);
			}
			$start+=1;
		}
	}
}
