<?php

class DefaultController extends Controller
{
    public function actionIndex()
    {
        throw new CHttpException(404,'Page not exists.');
    }

    public function actionAddItem()
    {
        if(!Yii::app()->request->isAjaxRequest) throw new CHttpException(404,'Page not exists.');

        $id = isset($_POST['pid']) ? $_POST['pid'] : null;
        $qty = isset($_POST['qty']) ? $_POST['qty'] : null;

        echo $id.$qty;
    }
}