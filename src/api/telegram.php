<?php 
    require "/../../vendor/autoload.php";

    use GuzzleHttp\Client;

    class Telegram {
        private $httpClient;
        private $offset;
        public $lastChatId;


        public function __construct($botUrl, $botToken) {
            $this->httpClient = new Client([
                'base_uri' => $botUrl . $botToken,
                
		    ]);
        }
        public function getChatId() {
            return $this->lastChatId;
        }
        public function getLastMessage() {
            
            $response = $this->httpClient->request('POST', 'getUpdates');
            $responseBody = json_decode($response->getBody());
            
           $length = count($responseBody->result);
           if(empty($responseBody->result[$length - 1])) {
               return FALSE;
           }
           
           $this->offset = (int)$responseBody->result[$length - 1]->update_id;
           $this->lastChatId = $responseBody->result[$length - 1]->message->chat->id;
            if(!empty($responseBody->result[$length - 1]->message->location)) {
                return $responseBody->result[$length - 1]->message->location;
            }
            return $responseBody->result[$length - 1]->message->text;
            
        }
        public function confirmMessage() {
            $response = $this->httpClient->request('POST', 'getUpdates', [
                'json' => [
                    'offset' => $this->offset + 1
                ]
            ]);    
        }
        public function sendMessage($chatId, $message, $keyboard ) { 
            $response = $this->httpClient->request('POST', 'sendMessage', [
			'json' => ['chat_id' => $this->lastChatId, 
						'text' => $message, 
                        'reply_markup' => json_encode($keyboard)
                    ]
            ]);
            return $response->getStatusCode();
        }
        

    }
    
?>