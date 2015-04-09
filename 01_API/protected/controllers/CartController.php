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
        }

        if(!isset($this->_post_data['cartItems'])){
            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1004",
                    "error_message" => "No Item to add.",
                ))));

            Yii::log('1004 - No Item to add.', CLogger::LEVEL_INFO, 'system.application');
        }

        $cartItems = $this->_post_data['cartItems'];

        foreach($cartItems as $item){
            Yii::log("Item: ".$item['id'] , CLogger::LEVEL_INFO, 'system.application');
        }
    }
}