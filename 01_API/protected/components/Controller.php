<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    public $_post_data;

    public function beforeActions(){
        die();
        $this->_post_data = Helpers::getJsonData();
        var_dump($this->_post_data);
        die;
    }
}