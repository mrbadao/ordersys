<?php

class DefaultController extends Controller
{
    const SESSION_KEY = '_CART';
    const ORDER_NAME_PREFIX = "ORDER_";

    public function actionIndex()
    {
        $this->setTitle('Giỏ hàng | ' . Yii::app()->params['appName']);

        $nodata = false;
        $cart = null;
        $items = array();
        $total = 0;

        $session = Yii::app()->session;
        if ($session->contains(self::SESSION_KEY)) {
            if (isset($_POST['cart'])) {
                $cart = $_POST['cart'];
                $session->remove(self::SESSION_KEY);
                for ($i = 0; $i < count($cart); $i++) {
                    if ($cart[$i]['qty'] < 1) {
                        unset($cart[$i]);
                    }
                }
                if (count($cart) > 0) {
                    $session->add(self::SESSION_KEY, $_POST['cart']);
                }
            } else {
                $cart = $session[self::SESSION_KEY];
            }
        }

        if ($cart == null) {
            $nodata = true;
        } else {
            foreach ($cart as $item) {
                $temp['id'] = $item['id'];
                $temp['qty'] = $item['qty'];

                $product = Helpers::getProduct($item['id']);
                if ($product) {
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
                    $_result['total'] += $product->price * $cart[$i]['qty'];
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

        $hasError = array('flg' => false, 'msg' => '');
        $orderStatus = array('flg' => false, 'msg' => '');
        $session = Yii::app()->session;

        if ($session->contains(self::SESSION_KEY)) {
            if ($checkoutOrder['customer_name'] != '' && $checkoutOrder['email'] != '' && $checkoutOrder['order_phone'] != '' && $checkoutOrder['customer_address'] != '') {
                $user_ip = getenv('REMOTE_ADDR');
                $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
                if ($geo) {
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
                            $OrderRelation = new OrderRelation();
                            $OrderRelation->order_id = $newOrder->id;
                            $OrderRelation->product_id = $item['id'];
                            $OrderRelation->qty = $item['qty'];
                            $OrderRelation->price = Helpers::getProduct($item['id'])->price;
                            $OrderRelation->save(false);
                            $session->remove(self::SESSION_KEY);
                            $orderStatus['flg'] = true;
                            $orderStatus['msg'] = 'Bạn đã đặt thành công đơn hàng với mã số là: ' . $newOrder->name;
                        }
                    }

                    if (!$orderStatus['flg']) {
                        $orderStatus['msg'] = 'Đã xãy ra lỗi trong quá trình thanh toán, vui lòng thữ lại sau.';
                    }

                } else {
                    $hasError['flg'] = true;
                    $hasError['msg'] = 'Chúng tôi không thể xác định vị trí của bạn. Xin vui lòng thữ lại hoặc sữ dụng android app của chúng thôi.';
                }
            } else {
                if (isset($_POST['name'])) {
                    $hasError['flg'] = true;
                    $hasError['msg'] = 'Hãy nhập đủ thông tin.';
                }
            }
        } else {
            $hasError['flg'] = true;
            $hasError['msg'] = 'Bạn không có sản phẩm nào để thanh toán.';
        }

        $this->render('checkout', compact('hasError', 'orderStatus', 'checkoutOrder'));
    }
}