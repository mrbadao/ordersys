<?php
/**
 * Created by PhpStorm.
 * User: HieuNguyen
 * Date: 4/23/2015
 * Time: 1:51 PM
 */

class OrderController extends Controller{

    public function actionGetOrderDetail(){
        $this->_post_data = Helpers::getJsonData();

        $_result = array();
        $_result['count'] =0;
        $_result['orders'] = array();

        $id = isset($this->_post_data['id']) ? $this->_post_data['id'] : null;

        if($id){
            $order = ContentOrder::model()->findByPk($id);
            if($order){
                $_result = array();
                $_result['count'] =0;
                $_result['order'] = array();

                $c = new CDbCriteria();
                $c->addCondition(' order_id = '.$id, 'AND');
                $c->order = 'id DESC';

                $_result['count'] = OrderRelation::model()->count($c);

                if($_result['count'] > 0){
                    $orderDetail = OrderRelation::model()->findAll($c);

                    foreach($orderDetail as $item){
                        $product = Helpers::getProduct($item->product_id);
                        if($product){
                            $_OrderItem['id'] = $product->id;
                            $_OrderItem['name'] = $product->name;
                            $_OrderItem['price'] = $item->price;
                            $_OrderItem['qty'] = $item->qty;
                            $_result['order'][] = $_OrderItem;
                        }
                    }

                    Helpers::_sendResponse(200, json_encode($_result));
                }
            }
        }
        Helpers::_sendResponse(200, json_encode(array(
            'error' => array(
                "error_code" => "1011",
                "error_message" => "Order not found.",
            ))));

    }

    public function actionGetOrder(){
        $this->_post_data = Helpers::getJsonData();

        $_result = array();
        $_result['count'] =0;
        $_result['orders'] = array();

        if($this->_post_data['phone'] && $this->_post_data['email']){
            $c = new CDbCriteria();
            $c->order = 'id DESC';

            if($this->_post_data['limit'] && is_numeric($this->_post_data['limit'])){
                $c->limit = $this->_post_data['limit'];
            }

            if($this->_post_data['offset'] && is_numeric($this->_post_data['offset'])){
                $c->offset = $this->_post_data['offset'];
            }

            $c->addCondition('order_phone = "'.$this->_post_data['phone'].'"', 'AND');
            $c->addCondition('email = "'.$this->_post_data['email'].'"', 'AND');

            $_data_count = ContentOrder::model()->count($c);

            $_data = $_data_count > 0 ? ContentOrder::model()->findAll($c) : null;

            if ($_data == null) {
                Helpers::_sendResponse(200, json_encode(array(
                    'error' => array(
                        "error_code" => "1003",
                        "error_message" => "No data.",
                    ))));
            }

            $_result['count'] = $_data_count;
            $_result['orders'] = Helpers::_db_fetchDataArray($_data, 'orders');

            Helpers::_sendResponse(200, json_encode($_result));
        }

        Helpers::_sendResponse(200, json_encode(array(
            'error' => array(
                "error_code" => "1011",
                "error_message" => "Order not found.",
            ))));
    }

    public function actionCancelOrder(){
        $this->_post_data = Helpers::getJsonData();

        $id = isset($this->_post_data['id']) ? $this->_post_data['id'] : null;

        if($id){
            $order = ContentOrder::model()->findByPk($id);

            if($order && $order->status == "0"){
                $c = new CDbCriteria();
                $c->addCondition(' order_id = '.$id, 'AND');
                $c->order = 'id DESC';

                OrderRelation::model()->deleteAll($c);

                ContentOrder::model()->deleteByPk($id);

                Helpers::_sendResponse(200, json_encode(array(
                    'status' => array(
                        "status_code" => "1012",
                        "status_message" => "Order has been deleted.",
                        "id" => $id,
                    ))));
            }

            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1013",
                    "error_message" => "Order can not be deleted.",
                ))));

        }
        Helpers::_sendResponse(200, json_encode(array(
            'error' => array(
                "error_code" => "1011",
                "error_message" => "Order not found.",
            ))));

    }
}