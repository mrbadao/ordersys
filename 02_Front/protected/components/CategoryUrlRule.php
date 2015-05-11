<?php

/**
 * Created by PhpStorm.
 * User: mrbadao
 * Date: 28/03/2015
 * Time: 14:51
 */
class CategoryUrlRule extends CBaseUrlRule
{
    public $connectionID = 'db';

    public function createUrl($manager, $route, $params, $ampersand)
    {
        if ($route == 'category/default/index') {
            $id = isset($params['id']) ? $params['id'] : null;
            $page = isset($params['page']) ? $params['page'] : null;
            if (!$id) return false;
            $cat_name = ContentCategories::model()->findByPk($id)->name;
            if (!$page) {
                $url = 'san-pham/' . Helpers::genUrlfromName(null, $cat_name) . '/';
            } else {
                $url = 'san-pham/' . Helpers::genUrlfromName(null, $cat_name, $page) . '/';
            }
            return $url;
        }
        return false;
    }

    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {
        $path = explode('/', Helpers::removeSQLInjectionChar(strtolower(trim($pathInfo, '/'))));
        var_dump($path);die;

        if (count($path) < 2) return false;

        if ($path[0] != 'danh-muc-san-pham') return false;

        if(!isset($path[1])) return false;

        $catId = null;
        $catId = ContentCategories::model()->findByAttributes(array('abbr_cd' => $path[1]));

        if($catId == null) return false;

        if (!isset($path[2])){
            return 'category/default/index';
        }

        $page = substr(strrchr($path[2], '-'), 1, strlen(strrchr($path[1], '-')));

        if (is_numeric($page)) {
            $path[1] = substr($path[1], 0, strlen($path[1]) - strlen($page) - 1);
        }



        if (!isset($path[2])) {

            return is_numeric($page) ? 'category/default/index/id/' . $catId->id . '/page/' . $page : 'category/default/index/id/' . $catId->id;
        }

        $producId = Helpers::getIDFromStr($path[2]);
        $product = ContentProduct::model()->findByPk($producId);
        if (!$product) return false;

        return 'content/product/id/' . $producId;
    }
}