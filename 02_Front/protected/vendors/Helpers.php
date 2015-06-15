<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Helper
 *
 * @author Administrator
 */
class Helpers
{

    /**
     *
     */
    public static function getFirstImg($contentHTML)
    {
        $image = '';
        preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $contentHTML, $image);
        return ($image != null) ? $image[1] : substr(Yii::app()->request->getBaseUrl(true), 0, -5) . 'upload/images/no_img.jpg';
    }

    public static function getNumChars($contentHTML, $num)
    {
        $contentHTML = strip_tags($contentHTML);
        $worldList = explode(' ', $contentHTML);
        return (implode($worldList) != '') ? (count($worldList) <= $num ? implode(' ', $worldList) : implode(' ', array_slice($worldList, 0, $num - 1)). ' ...') : '';
    }

    public static function getDomainFromName($str)
    {
        $str = trim($str, '/');
        $str = trim($str);
        $str = strtolower($str);
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|� �|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ớ|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|� �|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);

        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|� �|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|� �|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = str_replace(" ", "-", str_replace("&*#39;", "", $str));
        $str = preg_replace('!\-+!', '-', $str);
        return $str;
    }

    public static function removeSQLInjectionChar($str)
    {
        $str = trim($str, '/');
        $str = trim($str);
        return str_replace(array('&', '<', '>', '\\', '"', "'", '?', '+', ';'), '', $str);
    }

    public static function getPageIDFromStr($str)
    {
        $producId = strrchr($str, '-');
        return substr(strrchr($str, '-'), 1, strlen($producId) - 6);
    }

    public static function getIDFromStr($str)
    {
        return substr($str, 0, strpos($str, '-'));
    }

    public static function genUrlfromId($id)
    {
        $cat = ContentCategories::model()->findByPk($id)->name;
        return self::genUrlfromName(null, $cat);
    }

    public static function getProduct($id){
        return ContentProduct::model()->findByPk($id);
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

?>
