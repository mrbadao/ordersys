<?php

class DefaultController extends Controller
{
    const SESSION_KEY = '_CART';

    public function actionIndex()
    {
        $nodata = false;
        $cart = null;
        $items = array();
        $total = 0;

        $session = Yii::app()->session;
        if ($session->contains(self::SESSION_KEY)) {
            if(isset($_POST['cart'])){
                $cart = $_POST['cart'];
                $session->remove(self::SESSION_KEY);
                for($i=0; $i<count($cart); $i++){
                    if($cart[$i]['qty'] < 1){
                        unset($cart[$i]);
                    }
                }
                if(count($cart) > 0) {
                    $session->add(self::SESSION_KEY, $_POST['cart']);
                }
            }else {
                $cart = $session[self::SESSION_KEY];
            }
        }

        if($cart == null){
            $nodata = true;
        }else{
            foreach($cart as $item){
                $temp['id'] =$item['id'];
                $temp['qty'] =$item['qty'];

                $product = Helpers::getProduct($item['id']);
                if($product) {
                    $temp['name'] = $product->name;
                    $temp['unit_total'] = number_format($item['qty'] * $product->price);
                    $total += $item['qty'] * $product->price;
                }
                $items[] = $temp;
            }
            $total = number_format($total);
        }


        $this->render('index', compact('nodata', 'items', 'total'));
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