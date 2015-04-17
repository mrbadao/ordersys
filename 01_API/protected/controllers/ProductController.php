<?php

class ProductController extends Controller
{
    public  function actionIndex(){
        $this->forward("search");
    }

    public function actionSearch(){
        $this->_post_data = Helpers::getJsonData();
        $search = isset($this->_post_data['search']) ? $this->_post_data['search'] : null;

        $_result = array();
        $_result['count'] =0;
        $_result['products'] = array();

        $c = new CDbCriteria();
        $c->order = 'id DESC';

        if(isset($this->_post_data['category_id']) && $this->_post_data['category_id'] != ''){
            $c->addCondition('category_id = '.$this->_post_data['category_id'], 'AND');
        }

        if(isset($this->_post_data['offset']) && is_numeric($this->_post_data['offset'])){
            $c->offset =$this->_post_data['offset'];
        }

        if(isset($this->_post_data['limit']) && is_numeric($this->_post_data['limit'])){
            $c->limit =$this->_post_data['limit'];
        }

        if ($search != null) {
            foreach ($search as $k => $v) {
                if (!isset($v) && $v === '') {
                    continue;
                }
                switch ($k) {
                    case 'name':
                        $c->compare($k, $v, false, 'AND');
                        break;
                }
            }
        }

        $_data_count = ContentProduct::model()->count($c);

        $_data = $_data_count > 0 ? ContentProduct::model()->findAll($c) : null;

        if ($_data == null) {
            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1003",
                    "error_message" => "No data.",
                ))));
        }

        $_result['count'] = $_data_count;
        $_result['products'] = Helpers::_db_fetchDataArray($_data, 'products');

        Helpers::_sendResponse(200, json_encode($_result));
    }

    public function actionGetHot(){
        $this->_post_data = Helpers::getJsonData();
        $nodata = true;

        $_result = array();
        $_result['products'] = array();

        $query =' SELECT `order_relation`.`product_id` AS `id` , `content_product`.`name` , `content_product`.`thumbnail` , `content_product`.`description` , `content_product`.`price` , `content_product`.`category_id` , `content_product`.`created` , `content_product`.`modified`'.
                ' FROM `order_relation`'.
                ' JOIN `content_product` ON `content_product`.`id` = `order_relation`.`product_id`'.
                ' WHERE 1'.
                ' GROUP BY `product_id`'.
                ' ORDER BY Count( * ) DESC'.
                ' LIMIT 0, 5;';

        $_result['products'] =  Yii::app()->db->createCommand($query)->queryAll();

        $_result['count'] = count($_result['products']);


        if($_result['count'] > 0 ){
            $nodata = false;
        }

        if ($nodata) {
            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1003",
                    "error_message" => "No data.",
                ))));
        }

        Helpers::_sendResponse(200, json_encode($_result));
    }
}