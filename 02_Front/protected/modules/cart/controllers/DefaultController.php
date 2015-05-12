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
        var_dump('dadadd');
    }
}