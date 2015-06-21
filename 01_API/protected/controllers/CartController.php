<?php

class CartController extends Controller
{
    const SESSION_KEY = '_CART';
    const ORDER_NAME_PREFIX = "ORDER_";

    public  function actionIndex(){
        $this->forward("search");
    }

    public function actionEdit(){
        $cart = null;
        $this->_post_data = Helpers::getJsonData();
        $session = Yii::app()->session;

        if($session->contains(self::SESSION_KEY)){
            $cart = $session[self::SESSION_KEY];
            $session->remove(self::SESSION_KEY);
        }

        if($cart == null){
            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1006",
                    "error_message" => "No Items found.",
                ))));
        }

        if(!isset($this->_post_data['cartItems'])){
            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1004",
                    "error_message" => "No Item to add.",
                ))));
        }

        $_result = array();

        foreach($this->_post_data['cartItems'] as $item){
            if($cart !=null){
                foreach($cart as $existsItem){
                    $flg = false;
                    if($item['id'] == $existsItem['id']){
                        if($item['qty'] != '0')
                            $existsItem['qty'] = $item['qty'];
                        else $flg =true;
                    }
                    if(!$flg)
                        $_result[] = $existsItem;
                }
            }
        }

        $cart = $_result;
        $session->add(self::SESSION_KEY, $cart);

        Helpers::_sendResponse(200, json_encode(array(
            'status' => array(
                "status_code" => "1007",
                "status_message" => "Item is edited successly.",
            ))));
    }

    public function actionDelete(){

    }

    public function actionDestroy(){
        $session = Yii::app()->session;

        if($session->contains(self::SESSION_KEY)){
            $session->remove(self::SESSION_KEY);
        }

        Helpers::_sendResponse(200, json_encode(array(
            'status' => array(
                "status_code" => "1008",
                "status_message" => "Cart has been destroied.",
            ))));
    }

    public function actionAdd(){
        $cart = null;
        $this->_post_data = Helpers::getJsonData();
        $session = Yii::app()->session;

        if($session->contains(self::SESSION_KEY)){
            $cart = $session[self::SESSION_KEY];
            $session->remove(self::SESSION_KEY);
        }

        if(!isset($this->_post_data['cartItems'])){
            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1004",
                    "error_message" => "No Item to add.",
                ))));
        }

        $_result= array();

        $isNewCartItem = true;
        foreach($this->_post_data['cartItems'] as $item){
            if($cart != null)
            {
                foreach($cart as $existsItem){
                    if($item['id'] == $existsItem['id']){
                        if($item['qty'] == '0'){
                            $existsItem = null;
                        }else {
                            $existsItem['qty'] += $item['qty'];
                        }
                        $isNewCartItem = false;
                    }

                    if($existsItem != null)
                    {
                        $_result[] = $existsItem;
                    }
                }
            }
            if($isNewCartItem){
                $_result[] = $item;
            }
            $isNewCartItem = true;
        }

        $cart = $_result;

        $session->add(self::SESSION_KEY, $cart);

        Helpers::_sendResponse(200, json_encode(array(
            'status' => array(
                "status_code" => "1005",
                "status_message" => "Item is added successly to cart.",
            ))));
    }

    public function actionGetCart(){
        $this->_post_data = Helpers::getJsonData();
        $session = Yii::app()->session;

        if($session->contains(self::SESSION_KEY)){
            $cart = $session[self::SESSION_KEY];
            $_result =array();
            foreach($cart as $item){
                $product = Helpers::getProduct($item['id']);
                if($product){
                    $item['name'] = $product->name;
                    $item['price'] = $product->saleoff_price ? $product->saleoff_price : $product->price;
                }
                $_result[] = $item;
            }
            Helpers::_sendResponse(200, json_encode(array(
                'count' => count($cart),
                'Cart' => $_result,
            )));
        }
        else{

            Helpers::_sendResponse(200, json_encode(array(
                'status' => array(
                    "status_code" => "1006",
                    "status_message" => "No items found.",
            ))));
        }
    }

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

    public function actionCheckout(){
        $this->_post_data = Helpers::getJsonData();
        $session = Yii::app()->session;

        if($session->contains(self::SESSION_KEY)){
            $cart = $session[self::SESSION_KEY];

            $newOrder = new ContentOrder();
            $newOrder->name = self::ORDER_NAME_PREFIX. date('HisdmY');
            $newOrder->order_phone = $this->_post_data['phone'];
            $newOrder->email = $this->_post_data['email'];
            $newOrder->customer_address = $this->_post_data['address'];
            $newOrder->customer_name = $this->_post_data['name'];
            $newOrder->coordinate_long = $this->_post_data['coordinate_long'];
            $newOrder->coordinate_lat = $this->_post_data['coordinate_lat'];
            $newOrder->status = '0';
            $newOrder->created = date("Y-m-d H:i:s");

            if(!Helpers::checkDeistanceBetween2Point(array('lat'=>$newOrder->coordinate_lat, 'lng' => $newOrder->coordinate_long)))
            {
                Helpers::_sendResponse(200, json_encode(array(
                    'error' => array(
                        "error_code" => "1018",
                        "error_message" => "Distance invalid.",
                    ))));
            }

            if($newOrder->validate()){
                $newOrder->save(false);

                $OrderRelation = null;
                foreach($cart as $item){
                    $OrderProduct = Helpers::getProduct($item['id']);

                    if($OrderProduct) {
                        $OrderRelation = new OrderRelation();
                        $OrderRelation->order_id = $newOrder->id;
                        $OrderRelation->product_id = $item['id'];
                        $OrderRelation->qty = $item['qty'];
                        $OrderRelation->price = $OrderProduct->saleoff_id != null ? $OrderProduct->saleoff_price : $OrderProduct->price;
                        $OrderRelation->saleoff_id = $OrderProduct->saleoff_id;
                        $OrderRelation->save(false);
                    }
                }

                $session->remove(self::SESSION_KEY);

                Helpers::_sendResponse(200, json_encode(array(
                    'status' => array(
                        "status_code" => "1009",
                        "status_message" => "Cart has been checkout.",
                        "order_id" => $newOrder->name,
                    ))));
            }

            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1010",
                    "error_message" => "Validate failed.",
                ))));
        }
        else{
            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1006",
                    "error_message" => "No items found.",
                ))));
        }
    }

    public function actionTest(){
        Helpers::checkDeistanceBetween2Point(array('lat'=>'10.8142', 'lng' => '106.643799'));
    }
}