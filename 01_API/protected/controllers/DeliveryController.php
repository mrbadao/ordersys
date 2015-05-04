<?php
/**
 * Created by PhpStorm.
 * User: HieuNguyen
 * Date: 4/28/2015
 * Time: 7:50 PM
 */

class DeliveryController extends Controller{

    public function actionIndex(){
        $this->forward('login');
    }

    public function actionLogin(){
        $this->_post_data = Helpers::getJsonData();

        if(isset($this->_post_data['login_id']) && isset($this->_post_data['password'])){
            $DeliveryStaff = DeliveryStaff::model()->findByAttributes(array('login_id' => $this->_post_data['login_id'], 'pasword' => md5($this->_post_data['password'])));

            if($DeliveryStaff){
                DeliveryToken::model()->deleteAllByAttributes(array('staff_id' => $DeliveryStaff->id));
                $token = Helpers::generalDeliveryToken($DeliveryStaff->id);

                if($token != null){
                    $DeliveryStaff = $DeliveryStaff->getAttributes();
                    Helpers::_sendResponse('200', json_encode(array(
                        'token' => $token,
                        'staff' => $DeliveryStaff
                    )));
                }

                Helpers::_sendResponse(200, json_encode(array(
                    'error' => array(
                        "error_code" => "1015",
                        "error_message" => "Login failed.",
                    ))));
            }
        }

        Helpers::_sendResponse(200, json_encode(array(
            'error' => array(
                "error_code" => "1014",
                "error_message" => "Unvalid usernam or password.",
            ))));
    }

    public function actionCheckToken()
    {
        $this->_post_data = Helpers::getJsonData();
        if (isset($this->_post_data['token']) && isset($this->_post_data['staff_id'])) {
            $token = DeliveryToken::model()->findByAttributes(array('token' => $this->_post_data['token'], 'staff_id' => $this->_post_data['staff_id']));

            if($token) {
                Helpers::_sendResponse(200, json_encode(array(
                    'status' => array(
                        "status_code" => "1017",
                        "status_message" => "Token valid."
                    ))));
            }
        }

        Helpers::_sendResponse(200, json_encode(array(
            'error' => array(
                "error_code" => "1015",
                "error_message" => "Invalid Token.",
            ))));
    }

    public function actionGetDeliveryOrderDetail(){
        $this->_post_data = Helpers::getJsonData();

        if(isset($this->_post_data['token']) && isset($this->_post_data['staff_id'])) {
            $token = DeliveryToken::model()->findByAttributes(array('token' => $this->_post_data['token'], 'staff_id' => $this->_post_data['staff_id']));

            if ($token) {
                $_result = array();
                $_result['count'] = 0;
                $_result['orders'] = array();

                $id = isset($this->_post_data['order_id']) ? $this->_post_data['order_id'] : null;

                if ($id) {
                    $order = ContentOrder::model()->findByPk($id);

                    if ($order) {
                        $_result = array();
                        $_result['count'] = 0;
                        $_result['order'] = array();

                        $c = new CDbCriteria();
                        $c->addCondition(' order_id = ' . $id, 'AND');
                        $c->addCondition(' delivery_id = ' . $this->_post_data['staff_id'], 'AND');
                        $c->order = 'id DESC';

                        $_result['count'] = OrderRelation::model()->count($c);

                        if ($_result['count'] > 0) {
                            $orderDetail = OrderRelation::model()->findAll($c);

                            foreach ($orderDetail as $item) {
                                $product = Helpers::getProduct($item->product_id);
                                if ($product) {
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
        }

        Helpers::_sendResponse(200, json_encode(array(
            'error' => array(
                "error_code" => "1015",
                "error_message" => "Invalid Token.",
            ))));
    }

    public function actionGetDeliveryOrders(){
        $this->_post_data = Helpers::getJsonData();

        if(isset($this->_post_data['token']) && isset($this->_post_data['staff_id'])){
            $token = DeliveryToken::model()->findByAttributes(array('token' => $this->_post_data['token'], 'staff_id' => $this->_post_data['staff_id']));

            if($token){
                $_result = array();
                $_result['count'] =0;
                $_result['orders'] = array();

                $c = new CDbCriteria();
                $c->order = "id ASC";
                $c->addCondition('delivery_id = '.$this->_post_data['staff_id'], 'AND');
                $c->addCondition('status = 1', 'AND');

                $_result['count'] = ContentOrder::model()->count($c);

                $c->limit = isset($this->_post_data['limit']) && is_numeric($this->_post_data['limit']) ? $this->_post_data['limit'] : $c->limit;
                $c->offset = isset($this->_post_data['offset']) && is_numeric($this->_post_data['offset']) ? $this->_post_data['offset'] : $c->offset;

                $_result['orders'] = ContentOrder::model()->findAll($c);

                if($_result['orders']){
                    $_result['orders'] = Helpers::_db_fetchDataArray($_result['orders'],'orders');
                    Helpers::_sendResponse(200, json_encode($_result));
                }

                Helpers::_sendResponse(200, json_encode(array(
                    'error' => array(
                        "error_code" => "1011",
                        "error_message" => "Order not found.",
                    ))));
            }
        }

        Helpers::_sendResponse(200, json_encode(array(
            'error' => array(
                "error_code" => "1015",
                "error_message" => "Invalid Token.",
            ))));
    }

    public function actionCompleteDeliveryOrder(){
        $this->_post_data = Helpers::getJsonData();

        if(isset($this->_post_data['token']) && isset($this->_post_data['staff_id']) && isset($this->_post_data['order_id'])){
            $token = DeliveryToken::model()->findByAttributes(array('token' => $this->_post_data['token'], 'staff_id' => $this->_post_data['staff_id']));

            if($token){
                $order = ContentOrder::model()->findByPk($this->_post_data['order_id']);

                if($order){
                    $order->status = "2";
                    $order->save(false);

                    Helpers::_sendResponse(200, json_encode(array(
                        'status' => array(
                            "status_code" => "1016",
                            "status_message" => "Completed Order."
                        ))));
                }

                Helpers::_sendResponse(200, json_encode(array(
                    'error' => array(
                        "error_code" => "1011",
                        "error_message" => "Order not found.",
                    ))));
            }
        }

        Helpers::_sendResponse(200, json_encode(array(
            'error' => array(
                "error_code" => "1015",
                "error_message" => "Invalid Token.",
            ))));
    }
}