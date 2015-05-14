<?php

/**
 * Created by PhpStorm.
 * User: mrbadao
 * Date: 28/03/2015
 * Time: 14:51
 */
class ContentUrlRule extends CBaseUrlRule
{
    public $connectionID = 'db';

    public function createUrl($manager, $route, $params, $ampersand)
    {
        switch($route){
            case 'content/about';
                return 'gioi-thieu.html';

            case 'category/default/search';
                if(isset($params['page'])){
                    return 'tim-kiem/trang-'.$params['page'].'/';
                }
                return 'tim-kiem/';

            case 'content/deliveryinfo';
                return 'thong-tin-giao-hang.html';

            case 'content/contact';
                return 'lien-he.html';
        }

        return false;
    }

    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {
        $path = explode('/', Helpers::removeSQLInjectionChar(strtolower(trim($pathInfo, '/'))));

        if (count($path) < 1) return false;

        switch($path[0]){
            case 'gioi-thieu.html';
                return '/content/about';

            case 'tim-kiem';
                if(isset($path[1]) && strpos($path[1],"trang") > -1 && strpos($path[1],"trang")==0){
                    $_GET['page'] = substr(strrchr($path[1], '-'), 1, strlen(strrchr($path[1], '-')));
                }
                return 'category/default/search';

            case 'thong-tin-giao-hang.html';
                return '/content/deliveryinfo';

            case 'lien-he.html';
                return '/content/contact';
        }

        return false;
    }
}