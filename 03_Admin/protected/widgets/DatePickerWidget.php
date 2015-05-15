<?php
/**
 * Created by PhpStorm.
 * User: Ho
 * Date: 11/21/2014
 * Time: 11:12 AM
 */
class DatePickerWidget extends CWidget
{
    public function init()
    {
    }

    public function run()
    {
        $baseUrl = Yii::app()->request->getBaseUrl(true);
        Yii::app()->clientScript

            ->registerScriptFile($baseUrl . '/js/jquery.json-2.4.min.js', CClientScript::POS_END)
            ->registerScriptFile($baseUrl . '/js/jquery-ui/jquery-ui.datetimepicker.js', CClientScript::POS_END);
    }
}