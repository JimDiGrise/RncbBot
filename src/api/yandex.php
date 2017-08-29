<?php 
    require "/../../vendor/autoload.php";

    use GuzzleHttp\Client;
    
    class Yandex {
        private $geo;
        private $chatId;
        public function __construct($chatId) {
            $this->httpClient = new Client();
            $this->chatId = $chatId;
            
        }
        public function getItemsByGeoLocation($category) {
            $geo = $this->getLocation();
            $response = $this->httpClient->request('GET', "https://search-maps.yandex.ru/v1/?apikey=37cc2574-03a2-444f-8f5f-caafaae8efe1&text=Российский национальный коммерческий банк $category&lang=ru-RU&type=biz&results=100&ll=" . $geo['longitude'] . "," . $geo['latitude'] . "&spn=0.015,0.015&rspn=1");
            $responseBody = json_decode($response->getBody());
            return array_filter($responseBody->features, function ($obj) {
                static $idList = array();
                if(in_array($obj->properties->CompanyMetaData->address,$idList)) {
                    return false;
                }
                $idList []= $obj->properties->CompanyMetaData->address;
                return true;
            });
        }
        private function getLocation() {
            $locations = json_decode(file_get_contents("geolocation.cfg"), true);
            return $locations[$this->chatId];
        }
        public function getSaticMap($geo) {
            $response = $this->httpClient->request('GET', "https://static-maps.yandex.ru/1.x/?ll=" . $geo[0] . "," . $geo[1] . "&size=250,250&z=17&l=map&pt=" . $geo[0] . "," . $geo[1] . ",pm2dgm&scale=1.0");
            file_put_contents("img.png", $response->getBody());  
        }
        
    }
?>