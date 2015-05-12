<?php

class DefaultController extends Controller
{
    const SESSION_KEY = '_CART';

    public function actionIndex()
    {
        throw new CHttpException(404,'Page not exists.');
    }

    public function actionAddItem()
    {
        if(!Yii::app()->request->isAjaxRequest) throw new CHttpException(404,'Page not exists.');

        $id = isset($_POST['pid']) ? $_POST['pid'] : null;
        $qty = isset($_POST['qty']) ? $_POST['qty'] : null;

        if($id != null && $qty != null){
            $session = Yii::app()->session;
            $cart = null;

            if($session->contains(self::SESSION_KEY)){
                $cart = $session[self::SESSION_KEY];
                $session->remove(self::SESSION_KEY);
            }

            $_result = array();
            $flg = false;


                if($cart !=null){
                    foreach($cart as $existsItem){
                        if($id == $existsItem['id']){
                            $existsItem['qty'] += $qty;
                            $flg = true;
                        }
                        $_result[] = $existsItem;
                    }
                }else{
                    $cart[] = array('id' => $id, 'qty' => $qty);
                }

            $cart = $_result;
            $session->add(self::SESSION_KEY, $cart);

            Helpers::_sendResponse(200, json_encode(array(
                'status' => array(
                    "status_code" => "1007",
                    "status_message" => "Item is edited successly.",
                ))));
        }
    }
}