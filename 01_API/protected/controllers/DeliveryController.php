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

        if(isset($this->_post_data['login_id']) && !isset($this->_post_data['password'])){
            $DeliveryStaff = DeliveryStaff::model()->findByAttributes(array('login_id' => $this->_post_data['login_id'], 'pasword' => md5($this->_post_data['pasword'])));

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

    public function getDeliveryOrders(){
        $this->_post_data = Helpers::getJsonData();

        if(isset($this->_post_data['token']) && isset($this->_post_data['staff_id'])){
            $token = DeliveryToken::model()->findByAttributes(array('token' => $this->_post_data['token'], 'staff_id' => $this->_post_data['staff_id']));

            if($token){
                $c = new CDbCriteria();
                $c->order = 'id ASC';
                $c->limit = isset($this->_post_data['limit']) && is_numeric($this->_post_data['limit']) ? $this->_post_data['limit'] : $c->limit;
                $c->offset = isset($this->_post_data['offset']) && is_numeric($this->_post_data['offset']) ? $this->_post_data['offset'] : $c->offset;
                $c->addCondition('delivery_id = '.$this->_post_data['staff_id'], 'AND');

            }
        }
    }
}