<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    public $_post_data;

    protected function beforeAction(){
        $this->_post_data = Helpers::getJsonData();
    }
}