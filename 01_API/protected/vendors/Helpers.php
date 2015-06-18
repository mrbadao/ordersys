<?php
/**
 * Created by PhpStorm.
 * User: HieuNguyen
 * Date: 4/3/2015
 * Time: 2:34 PM
 */

class Helpers{
    const format = 'json';
    const SECRET_KEY = '6dn9T3t2760yypWAhdhURmz7oZQrhdXjqRoTorybjWU=';

    /**
     * getJsonData
     *
     * @return mixed $data
     */
    public static function getJsonData()
    {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData, true);

        if (empty($data) || $data == null || $data =='')
            self::_sendResponse(200, json_encode(array(
                "error" => array(
                    "error_code" => "1000",
                    "error_message" => "No json received"
                )
            )));

        if (empty($data['secret_key']) || $data['secret_key'] != self::SECRET_KEY) {
            self::_sendResponse(200, json_encode(array(
                "error" => array(
                    "error_code" => "1002",
                    "error_message" => "Secret key is invalid."
                )
            )));
        }

        return $data;
    }

    public static function removeHtmlTag($contentHTML){
        $contentHTML = strip_tags($contentHTML);
        return html_entity_decode($contentHTML);
    }

    /**
     * Sends the API response
     *
     * @param int $status
     * @param string $body
     * @param string $content_type
     * @access private
     * @return void
     */
    public static function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
    {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . self::_getStatusCodeMessage($status);

        header($status_header);
        header('Content-Type: ' . $content_type);

        // pages with body are easy
        if ($body != '') {
            echo $body;
            exit;
        }
        else {
            // create some body messages
            $message = '';

            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch ($status) {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            // servers don't always have a signature turned on (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

            // this should be templatized in a real-world solution
            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
                        <html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                                <title>' . $status . ' ' . self::_getStatusCodeMessage($status) . '</title>
                            </head>
                            <body>
                                <h1>' . self::_getStatusCodeMessage($status) . '</h1>
                                <p>' . $message . '</p>
                                <hr />
                                <address>' . $signature . '</address>
                            </body>
                        </html>';

            echo $body;
            exit;
        }
    }

    /**
     * Gets the message for a status code
     *
     * @param mixed $status
     * @access private
     * @return string
     */
    private static function _getStatusCodeMessage($status)
    {
        $codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    public static function _db_fetchDataArray($data, $arr_name){
        $_arr_data = array();

        foreach($data as $item){
            $_arr_data[$arr_name][]=$item->getAttributes();
        }
        return $_arr_data[$arr_name];
    }

    public static function getProduct($id){
        return ContentProduct::model()->findByPk($id);
    }

    public static function randString($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";

        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }

        return $str;
    }

    public static function generalDeliveryToken($staff_id){

        $DeliveryToken = new DeliveryToken();

        $DeliveryToken->token = md5(self::randString(10));
        $DeliveryToken->staff_id = $staff_id;
        $DeliveryToken->created = date("Y-m-d H:i:s");
        $DeliveryToken->modified = date("Y-m-d H:i:s");

        if($DeliveryToken->validate()){
            $DeliveryToken->save(false);
            return $DeliveryToken->token;
        }
        return null;
    }

    public static function checkDeistanceBetween2Point($detination){
        $content = null;
        $ggeo = null;

        $base_lat = Setting::model()->findByAttributes(array('key' =>'Coordinate-lat'))->value;
        $base_lng = Setting::model()->findByAttributes(array('key' =>'Coordinate-lng'))->value;
        $base_distance = Setting::model()->findByAttributes(array('key' =>'Distance'))->value;

        $origin = array('lat' => $base_lat, 'lng' => $base_lng, 'maxDistance' => $base_distance);

        $url = 'http://maps.googleapis.com/maps/api/directions/json?origin='.$origin['lat'].','.$origin['lng'].'&destination='.$detination['lat'].','.$detination['lng'].'&sensor=true&mode=driving';

        $content = file_get_contents($url);

        if($content == null) return false;

        $ggeo = json_decode($content);

        if($ggeo == null) return false;

        if($ggeo->routes[0]->legs['0']->distance->value > $origin['maxDistance']) return false;
        return true;
    }
}