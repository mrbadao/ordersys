<?php

class CartController extends Controller
{
    const SESSION_KEY = '_CART';

    public  function actionIndex(){
        $this->forward("search");
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

        foreach($this->_post_data['cartItems'] as $item){
            $cart[] = $item;
        }


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
}