<?php
/**
 * Bot that helps set up a farming session for the Sri Lankan Ingress Enlightened.
 */

function build_response($chat_id, $text) {
    $returnvalue = 'https://api.telegram.org/bot180563680:AAGFLwqusmhx9Y3Ep6BebyimlUb-vA6ZEm8/sendMessage?chat_id='
            . $chat_id . '&text=' . $text.'&disable_web_page_preview=true&parse_mode=markdown';
    return $returnvalue;
}
function send_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }

    // Close connection
    curl_close($ch);
}
function send_response($input_raw) {
    $swears = array('fuckoff', 'fuck', 'hutto', 'ponnaya', 'pakaya', 'paka', 'fuckyou', 'redda', 'motherfucker', 'pimpiya','huththa','hukahan');
    //$response = send_curl('https://api.telegram.org/bo137061395:AAEAIkLCaw-aqUYKzDRSPdEBWGRFtBiofwI/getUpdates');
    /*$input_raw = '{
                      "update_id": 89018516,
                      "message": {
                        "message_id": 62,
                        "from": {
                          "id": 38722085,
                          "first_name": "Ramindu \"RamdeshLota\"",
                          "last_name": "Deshapriya",
                          "username": "CMNisal"
                        },
                        "chat": {
                          "id":38722085,
                          "title": "Bottest"
                        },
                        "date": 1435508622,
                        "text": "/guide someone@email.com"
                      }
                    }';*/
    // let's log the raw JSON message first
    $messageobj = json_decode($input_raw, true);
    $message_text = str_replace('@slenlnewbiebot','',strtolower ($messageobj['message']['text']));
    $message_txt_parts = explode(' ', $message_text );
    $request_message = $message_txt_parts[0];
    $chat_id = $messageobj['message']['chat']['id'];
	$user_id = $messageobj['message']['from']['id'];
	$username = $messageobj['message']['from']['username'];
	
    $reply = urlencode($messageobj['message']['from']['first_name'].' [@'.$username.'|'.$messageobj['message']['from']['id'].']');
	if($chat_id==$user_id){
		$reply .= urlencode('
-Private Message-');
	}else{
		$reply .= urlencode('
-'.$messageobj['message']['chat']['title'].' | '.$chat_id.'-');
	}
	$reply .=urlencode('
'. $messageobj['message']['text']);

	send_curl(build_response(-1001054269939,$reply));
	$reply = '';


    //check for swear words
    foreach ($swears as $swear) {
        if (strpos($messageobj['message']['text'], $swear) !== false) {
            $reply = urlencode('යකෝ මේක හදල තියෙන්නෙ ගොන් ආතල් ගන්න නෙවේ. ගොන් ආතල් ගන්න ඕන නම් මෑඩ් හව්ස් එකට පලයන්.');
            send_curl(build_response($chat_id, $reply));
            return;
        }
    }
	if (array_key_exists('new_chat_participant', $messageobj['message']) && $messageobj['message']['new_chat_participant']['first_name'] !== 'SL ENL Newbie') {	
	$newcomer = $messageobj['message']['new_chat_participant']['first_name']." ".$messageobj['message']['new_chat_participant']['last_name'];
	$reply = urlencode('Hello *'.$newcomer.',*	
We\'re glad that you\'ve joined our faction!
In order to receive the beginner\'s guide 
please type ``` /guide <your email address> ``` and send.
Thank you!');
	if($username==""){
		$reply .= "Please set your agent name as your username on Telegram.";
		}
	send_curl(build_response($chat_id, $reply));
		return;
	}
    if ($request_message == '/guide') {
    	$email = strtolower(substr($messageobj['message']['text'],7));
    	
    	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    		$reply = urlencode('@'.$username.',
Please enter Valid E-mail address.		
Thank you!');
    		send_curl(build_response($chat_id, $reply));
    		return;
    	} 
    	
    	$reply = urlencode('*Cool* 😎
You\'ll receive the *guide* within few hours! 👍 ');
    	send_curl(build_response($chat_id, $reply));
        $reply = urlencode(substr($messageobj['message']['text'], 6));
		send_curl(build_response(-1001069490161, $reply));//Newbie Mails Channel
		send_curl(build_response(70414867, $reply));//JR
		

    	return;
    }

    if ($request_message == '/help' || $request_message == '/help@slenlnewbiebot' || $request_message == '/start') {
        $reply = urlencode('Please type ``` /guide <space> <your email address> ``` and send.');

        send_curl(build_response($chat_id, $reply));
        return;
    }
}

send_response(file_get_contents('php://input'));
