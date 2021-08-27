<?php
$data = json_decode(file_get_contents('php://input'), TRUE);

$message = $data["message"]["from"];
$name = $message["first_name"];
$surname = $message["last_name"];
$chatid = strval($message["id"]);
$username = $message["username"];
$msg = $data["message"]["text"];

function add_msg_tr($from, $to){
	$jh = file_get_contents('lines.json');
	$jh = json_decode($jh);
	$jhs = ["from" => $from, "to"=>"to"];
	$jh[count($jh)] = $jhs;
	file_put_contents('lines.json', json_encode($jh));
}

function get_reply($msg){
	$jh = file_get_contents('lines.json');
	$jh = json_decode($jh);
	foreach ($jh as $i){
		$from = $i->from;
		if ($from == $msg){
			return $i->to;
		}else{
			if ($from == strtolower($msg)){
				return $i->to;
			}
		}
	}
	return 'У меня нет ответа на это.';
}

function get_all_lines(){
	$jh = json_decode(file_get_contents('lines.json'));
	$strings = 'All lines: ';
	foreach ($jh as $it){
		$strings .= "\n".$it->from.' -> '.$it->to;
	}
	return $strings;
}

function popping($mass, $key){
	$index = 0;
	$ready = [];
	foreach ($mass as $item){
		if ($index != $key){
			$ready[count($ready)] = $item;
		}
		$index=$index+1;
	}
	return $ready;
}

function makebtn($text, $ex='none'){
	if ($ex == 'none'){
		$inline_button = array("text"=>$text);
		return [$inline_button];
	}else{
		$inline_button = array("text"=>$text);
		return $inline_button;
	}
}
function new_keyboard($text, $ex='none'){
	if ($ex == 'none'){
		$inline_button = array("text"=>$text);
		return [$inline_button];
	}else{
		$inline_button = array("text"=>$text);
		return $inline_button;
	}
}

function new_inline($text, $type, $value, $ex='none'){
	if ($ex == 'none'){
		$inline_button = array("text"=>$text, $type=>$value);
		return [$inline_button];
	}else{
		$inline_button = array("text"=>$text, $type=>$value);
		return $inline_button;
	}
}

function sendMessage($chat_id, $msg, $token){
		$sendToTelegram = file_get_contents(("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text=".urlencode($msg)),"r");
}

function sendMessage_keyboard($chat_id, $msg, $token, $keyboard){
		$inline_keyboard = $keyboard;
		$keyboard=array("keyboard"=>$inline_keyboard, "resize_keyboard"=>true);
		$replyMarkup = json_encode($keyboard);
		$sendToTelegram = file_get_contents(("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text=".urlencode($msg)."&reply_markup=".urlencode($replyMarkup)),"r");
}

function sendMessage_inline($chat_id, $msg, $token, $keyboard){
		$inline_keyboard = $keyboard;
		$keyboard=array("inline_keyboard"=>$inline_keyboard);
		$replyMarkup = json_encode($keyboard);
		$sendToTelegram = file_get_contents(("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text=".urlencode($msg)."&reply_markup=".urlencode($replyMarkup)),"r");
}