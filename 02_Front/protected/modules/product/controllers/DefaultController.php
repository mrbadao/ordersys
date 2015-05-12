<?php

class DefaultController extends Controller
{

	const SESS_KEY = '_PRODUCT';

	public function actionIndex()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : null;
        $item = ContentProduct::model()->findByAttributes(array('id' => $id, 'del_flg' => '0'));

        if($item == null) throw new CHttpException(404,"Page not found.");

        $this->setTitle($item->name.' | '.Yii::app()->params['appName']);

        $c = new CDbCriteria();
        $c->order = " id DESC";
        $c->offset = 0;
        $c->limit = 4;
        $c->addCondition('id != '.$id, 'AND');
        $c->addCondition('del_flg = 0', 'AND');

        $relatedItems = ContentProduct::model()->findAll($c);

        $this->render('index',compact('item', 'relatedItems'));
	}
}