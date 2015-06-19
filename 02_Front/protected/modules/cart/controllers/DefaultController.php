<?php

class DefaultController extends Controller
{
    const SESSION_KEY = '_CART';
    const SESSION_SUB_KEY = '_CART_RECOMMEND';
    const ORDER_NAME_PREFIX = "ORDER_";

    public function actionIndex()
    {
        $this->setTitle('Giỏ hàng | ' . Yii::app()->params['appName']);

        $nodata = false;
        $cart = null;
        $items = array();
        $total = 0;
        $tempCart = array();

        $session = Yii::app()->session;

        if ($session->contains(self::SESSION_KEY)) {
            $cart = $session[self::SESSION_KEY];
            $session->remove(self::SESSION_KEY);
        }

        if (isset($_POST['cart'])) {
            $cart = $_POST['cart'];

            for($i=0;$i<count($cart); $i++){
                if ($cart[$i]['qty'] > 0) {
                    $tempCart[] = $cart[$i];
                }
            }

            $cart = $tempCart;
        }

        $nodata = count($cart) < 1 ? true : $nodata;

        if (!$nodata) {
            $session->add(self::SESSION_KEY, $cart);
            foreach ($cart as $item) {
                $temp['id'] = $item['id'];
                $temp['qty'] = $item['qty'];

                $product = Helpers::getProduct($item['id']);
                if ($product) {
                    $temp['name'] = $product->name;
                    $temp['unit_total'] = $product->saleoff_price != '' ? number_format($item['qty'] * $product->saleoff_price) : number_format($item['qty'] * $product->price);
                    $total += $product->saleoff_price != '' ? $item['qty'] * $product->saleoff_price : $item['qty'] * $product->price;
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
                for ($i = 0; $i < count($cart); $i++) {
                    if ($id == $cart[$i]['id']) {
                        $cart[$i]['qty'] += $qty;
                        $flg = true;
                    }
                }
            }

            if (!$flg) {
                $cart[] = array('id' => $id, 'qty' => $qty);
            }

            $_result['count'] = count($cart);
            $_result['total'] = 0;

            for ($i = 0; $i < count($cart); $i++) {
                $product = Helpers::getProduct($cart[$i]['id']);

                if ($product) {
                    $_result['total'] += $product->saleoff_price !='' ? $product->saleoff_price * $cart[$i]['qty'] : $product->price * $cart[$i]['qty'];
                } else {
                    unset($cart[$i]);
                }
            }

            $_result['total'] = number_format($_result['total']);

            $session->add(self::SESSION_KEY, $cart);

            echo json_encode($_result);
        }
    }

    public function actionCheckout()
    {
        $this->setTitle('Thanh toán | ' . Yii::app()->params['appName']);

        $checkoutOrder = array(
            'customer_name' => isset($_POST['name']) ? $_POST['name'] : '',
            'email' => isset($_POST['email']) ? $_POST['email'] : '',
            'order_phone' => isset($_POST['phone']) ? $_POST['phone'] : '',
            'customer_address' => isset($_POST['address']) ? $_POST['address'] : '',
        );

        $recommend = array();
        $hasError = array('flg' => false, 'msg' => '');
        $orderStatus = array('flg' => false, 'msg' => '');

        $session = Yii::app()->session;
        if ($session->contains(self::SESSION_KEY)) {

            $cart = $session[self::SESSION_KEY];

            $tempCart = $cart;
            do{
                $findResult = self::getRecommend($tempCart);
                if($findResult){
                    $tempCart = $findResult['new_cart'];
                    $recommend[] = $findResult['combo_id'];

                }
            }while($findResult !=null);

            if ($checkoutOrder['customer_name'] != '' && $checkoutOrder['email'] != '' && $checkoutOrder['order_phone'] != '' && $checkoutOrder['customer_address'] != '') {
                $user_ip = getenv('REMOTE_ADDR');
                $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
                if ($geo) {
                    if(Helpers::checkDeistanceBetween2Point(array('lat' => $geo['geoplugin_latitude'], 'lng' => $geo['geoplugin_longitude']))) {
                        $cart = $session[self::SESSION_KEY];

                        $checkoutOrder['name'] = self::ORDER_NAME_PREFIX . date('HisdmY');
                        $checkoutOrder['coordinate_lat'] = $geo['geoplugin_latitude'];
                        $checkoutOrder['coordinate_long'] = $geo['geoplugin_longitude'];
                        $checkoutOrder['created'] = date("Y-m-d H:i:s");


                        $newOrder = new ContentOrder();
                        $newOrder->attributes = $checkoutOrder;

                        if ($newOrder->validate()) {
                            $newOrder->save(false);
                            $OrderRelation = null;
                            foreach ($cart as $item) {
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

                                $session->remove(self::SESSION_KEY);

                                $orderStatus['flg'] = true;
                                $orderStatus['msg'] = 'Bạn đã đặt thành công đơn hàng với mã số là: ' . $newOrder->name;
                            }
                        }

                        if (!$orderStatus['flg']) {
                            $orderStatus['msg'] = 'Đã xãy ra lỗi trong quá trình thanh toán, vui lòng thữ lại sau.';
                        }

                    }else{
                        $hasError['flg'] = true;
                        $hasError['msg'] = 'Vị trí của bạn quá xa chúng tôi không thể giao hàng.';
                    }

                } else {
                    $hasError['flg'] = true;
                    $hasError['msg'] = 'Chúng tôi không thể xác định vị trí của bạn. Xin vui lòng thữ lại hoặc sữ dụng android app của chúng tôi.';
                }

            } else {
                $hasError['flg'] = true;
                $hasError['msg'] = 'Hãy nhập đủ thông tin cần thiêt.';
            }

        } else {
            $hasError['flg'] = true;
            $hasError['msg'] = 'Bạn không có sản phẩm nào để thanh toán.';
        }

        $this->render('checkout', compact('hasError', 'orderStatus', 'checkoutOrder', 'recommend'));
    }

    function getRecommend($cart){
        $recommend = array();
        $chooseId =null;
        $max = -1;

        foreach ($cart as $item) {
            $comboRelation = ComboRelation::model()->findAllByAttributes(array('rid' => $item['id']));
            if($comboRelation){
                foreach($comboRelation as $relation){
                    if(is_array($recommend[$relation->combo_id])){
                        array_push($recommend[$relation->combo_id], $relation->rid);
                    }else{
                        $recommend[$relation->combo_id] = array($relation->rid);
                    }
                }
            }
        }

        if(count($recommend)<1) return null;

        foreach($recommend as $key => $item){
            if(count($item) > $max){
                $chooseId = $key;
                $max = count($item);
            }
        }

        $chooseId;

        $i=0;
        $_loop = count($cart);

        $new_cart = array();

        while($i< $_loop){
            if(!(in_array($cart[$i]['id'], $recommend[$chooseId]))){
                $new_cart[] = array('id'=>$cart[$i]['id'], 'qty' => $cart[$i]['qty']);
            }
            $i++;
        }

        return array('combo_id' => Helpers::getProduct($chooseId), 'new_cart' => $new_cart);
    }
}