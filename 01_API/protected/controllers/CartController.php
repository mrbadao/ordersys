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
                    if($item['id'] == $existsItem['id']){
                        $existsItem['qty'] = $item['qty'];
                    }
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
                        $existsItem['qty'] += $item['qty'];
                        $_result[] = $existsItem;
                        $isNewCartItem = false;
                    }else{
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
                    $item['price'] = $product->price;
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

    public function actionGetOrder(){
        $this->_post_data = Helpers::getJsonData();

        $_result = array();
        $_result['count'] =0;
        $_result['orders'] = array();

        if($this->_post_data['phone'] && $this->_post_data['email']){
            $c = new CDbCriteria();
            $c->order = 'id DESC';
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
            $newOrder->order_name = $this->_post_data['name'];
            $newOrder->coordinate_long = '-74.00594130000002';
            $newOrder->coordinate_lat = '40.7127837';
            $newOrder->status = '0';
            $newOrder->created = date("Y-m-d H:i:s");

            if($newOrder->validate()){
                $newOrder->save(false);
                $OrderRelation = null;
                foreach($cart as $item){
                    $OrderRelation = new OrderRelation();
                    $OrderRelation->order_id = $newOrder->id;
                    $OrderRelation->product_id = $item['id'];
                    $OrderRelation->qty = $item['qty'];
                    $OrderRelation->price = Helpers::getProduct($item['id'])->price;
                    $OrderRelation->save(false);
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
}