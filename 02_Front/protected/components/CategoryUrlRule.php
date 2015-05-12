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
            $cat_abbr_cd = ContentCategories::model()->findByPk($id)->abbr_cd;

            if (!$page) {
                $url = 'danh-muc-san-pham/' . $cat_abbr_cd . '/';
            } else {
                $url = 'danh-muc-san-pham/' . $cat_abbr_cd . '/trang-'.$page;
            }

            return $url;
        }
        return false;
    }

    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {
        $path = explode('/', Helpers::removeSQLInjectionChar(strtolower(trim($pathInfo, '/'))));

        if (count($path) < 2) return false;

        if ($path[0] != 'danh-muc-san-pham') return false;

        if(!isset($path[1])) return false;

        $catId = null;
        $catId = ContentCategories::model()->findByAttributes(array('abbr_cd' => $path[1]));

        if($catId == null) throw new CHttpException(404,'Url Site not exits');

        if (!isset($path[2])){
            return 'category/default/index/id/'.$catId->id;
        }

        if(strpos($path[2],"trang") > -1 && strpos($path[2],"trang")==0){
            $page = substr(strrchr($path[2], '-'), 1, strlen(strrchr($path[2], '-')));
            return 'category/default/index/id/'.$catId->id.'/page/'.$page;
        }else{
            $producId = $path[2];
            $product = ContentProduct::model()->findByPk($producId);
            if ($product == null) throw new CHttpException(404,'Url Site not exits');

            return 'product/default/index/id/' . $producId;
        }

        return false;
    }
}