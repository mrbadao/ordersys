<?php

class DefaultController extends Controller
{

	const SESS_KEY = '_PRODUCT';

	public function actionIndex()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : null;
        $item = ContentProduct::model()->findByAttributes(array('id' => $id, 'del_flg' => '0'));

        if($item == null) throw new CHttpException(404,"Page not found.");

        $this->render('index',compact('item'));
	}
}