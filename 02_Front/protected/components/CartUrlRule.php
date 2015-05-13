<?php

/**
 * Created by PhpStorm.
 * User: mrbadao
 * Date: 28/03/2015
 * Time: 14:51
 */
class CartUrlRule extends CBaseUrlRule
{
    public $connectionID = 'db';

    public function createUrl($manager, $route, $params, $ampersand)
    {
        switch($route){
            case 'cart/default/index';
                return 'gio-hang.html';
        }

        return false;
    }

    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {
        $path = explode('/', Helpers::removeSQLInjectionChar(strtolower(trim($pathInfo, '/'))));

        if (count($path) < 1) return false;

        switch($path[0]){
            case 'gio-hang.html';
                return '/cart/default/index';

            case 'thanh-toan.html';
                return '/cart/default/checkout';
        }

        return false;
    }
}