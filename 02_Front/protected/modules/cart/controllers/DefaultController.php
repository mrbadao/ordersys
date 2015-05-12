<?php

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionAddItem()
    {
        var_dump('dadadd');
    }
}