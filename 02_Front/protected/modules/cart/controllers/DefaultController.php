<?php

class DefaultController extends Controller
{
    const SESSION_KEY = '_CART';

    public function actionIndex()
    {
        throw new CHttpException(404, 'Page not exists.');
    }

    public function actionAddItem()
    {
        if (!Yii::app()->request->isAjaxRequest) throw new CHttpException(404, 'Page not exists.');

        $id = isset($_POST['pid']) ? $_POST['pid'] : null;
        $qty = isset($_POST['qty']) ? $_POST['qty'] : null;

        if ($id != null && $qty != null) {
            $session = Yii::app()->session;
            $cart = array();
            $flg = false;

            if ($session->contains(self::SESSION_KEY)) {
                $cart = $session[self::SESSION_KEY];
                $session->remove(self::SESSION_KEY);
            }

            if ($cart != null) {
                for($i=0; $i<count($cart); $i++) {
                    if ($id == $cart[$i]['id']) {
                        $cart[$i]['qty'] += $qty;
                        $flg = true;
                    }
                }
            }

            if(!$flg) {
                $cart[] = array('id' => $id, 'qty' => $qty);
            }

            $_result['count'] = count($cart);
            $_result['total'] = 0;

            for($i=0; $i<count($cart); $i++){
                $product = Helpers::getProduct($cart[$i]['id']);

                if($product){
                    $_result['total'] += $product->price * $cart[$i]['qty'];
                }else{
                    unset($cart[$i]);
                }
            }

            $_result['total'] = number_format($_result['total']);

            $session->add(self::SESSION_KEY, $cart);

            echo json_encode($_result);
        }
    }
}