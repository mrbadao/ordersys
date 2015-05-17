<?php

class DefaultController extends Controller
{
	const SESS_KEY = '_ORDERS';
	const SESS_STATISTICAL_KEY = '_STATISTICAL_ORDERS';

	public function actionIndex()
	{
		$this->forward('search');
	}

	public function actionStatistical(){
		$this->widget('DatePickerWidget');
		$this->title='Orders Statistical | CMS Order Sys';
		$search['fromdate'] = $search['todate'] = '';
		$total = 0;
		$nodata = true;

		$session = Yii::app()->session;

		if(isset($_POST['search']))
		{

			if($session->contains(self::SESS_STATISTICAL_KEY))
				$session->remove(self::SESS_STATISTICAL_KEY);

			$data['search'] = $_POST['search'];
			$data['page'] = 1;
			$session->add(self::SESS_STATISTICAL_KEY, $data);
		}


		$c = new CDbCriteria();
		$c->alias = "t";
		$c->together = true;

		if(isset($session[self::SESS_STATISTICAL_KEY]['search']))
		{
			$search = $session[self::SESS_STATISTICAL_KEY]['search'];

			if(isset($search['fromdate']) && $search['fromdate'] != ''){
				$c->addCondition('t.created >= "'.$search['fromdate'].'"', 'AND');
			}

			if(isset($search['todate']) && $search['todate'] != ''){
				$c->addCondition('t.created <= "'.$search['todate'].'"', 'AND');
			}
		}

		$sess_data = $session[self::SESS_STATISTICAL_KEY];
		if(isset($_GET['page']))
			$page = $sess_data['page'] = $_GET['page'];

		else
			$page = $sess_data['page'] = 1;
		$session->add(self::SESS_STATISTICAL_KEY,$sess_data);

		$c->select = 't.*';
		$c->group = 't.id';
		$c->order = 't.id DESC';
		$c->addCondition('t.status = 2', 'AND');

		$count = ContentOrder::model()->count($c);
		$items = ContentOrder::model()->findAll($c);
		foreach($items as $item){
			$total += $item->unit_price;
		}

		$nodata = ($count)?false:true;

		$c->limit = 10;
		$c->offset = $c->limit * ($page-1);

		$items = ContentOrder::model()->findAll($c);

		$pages = new CPagination($count);
		$pages->pageSize = $c->limit;
		$pages->applyLimit($c);

		$this->render('statistical',compact('items','count','pages','search','nodata', 'total'));
	}

	public function actionView(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id==null){
			$this->redirect(array('index'));
		}

		$contentOrder = ContentOrder::model()->findByPk($id);

		if($contentOrder == null) $this->redirect(array('index'));

		$this->title='View Order | CMS Order Sys';

		$data = array();
		$total = 0;

		$orderRelation = OrderRelation::model()->findAllByAttributes(array('order_id' => $id));

		foreach($orderRelation as $item){
			$product = ContentProduct::model()->findByPk($item->product_id);
			$data[] = array(
				'name' => $product->name,
				'price' => $item->price,
				'qty' => $item->qty,
				'unit_price' => $item->qty * $item->price,
			);
			$total += $item->qty * $item->price;
		}

		$staffs = DeliveryStaff::model()->findAll();
		$msg = false;
		if(isset($_POST['deliveryStaff']) && $_POST['deliveryStaff'] != null){
			$contentOrder->delivery_id = $_POST['deliveryStaff'];
			$contentOrder->status = 1;
			$contentOrder->save(false);
			$msg = true;
		}

		$this->render('view',compact('msg','contentOrder', 'data', 'total', 'staffs'));
	}

	public function actionSearch(){
		$this->widget('DatePickerWidget');
		$this->title='Manager Orders | CMS Order Sys';
		$search['name'] = $search['customer_name'] = $search['order_phone'] = $search['created'] = '';

		$session = Yii::app()->session;

		if(isset($_POST['search']))
		{

			if($session->contains(self::SESS_KEY))
				$session->remove(self::SESS_KEY);

			$data['search'] = $_POST['search'];
			$data['page'] = 1;
			$session->add(self::SESS_KEY, $data);
		}


		$c = new CDbCriteria();
		$c->alias = "t";
		$c->together = true;

		if(isset($session[self::SESS_KEY]['search']))
		{

			$search = $session[self::SESS_KEY]['search'];
			foreach($search as $k => $v)
			{
				if(!isset($v) || $v === '')
				{
					continue;
				}
				switch($k)
				{
					case 'name':
						$c->compare($k, $v, true,'AND');
						break;
					case 'customer_name':
						$c->compare($k, $v, true,'AND');
						break;
					case 'order_phone':
						$c->compare($k, $v, true,'AND');
						break;
					case 'created':
						$c->compare($k, $v, true,'AND');
						break;
				}
			}
		}

		$sess_data = $session[self::SESS_KEY];
		if(isset($_GET['page']))
			$page = $sess_data['page'] = $_GET['page'];

		else
			$page = $sess_data['page'] = 1;
		$session->add(self::SESS_KEY,$sess_data);

		$c->select = 't.*';
		$c->group = 't.id';
		$c->order = 't.id DESC';
		$count = ContentOrder::model()->count($c);

		$nodata = ($count)?false:true;
		$c->limit = 10;
		$c->offset = $c->limit * ($page-1);
		$items = ContentOrder::model()->findAll($c);
		$pages = new CPagination($count);
		$pages->pageSize = $c->limit;
		$pages->applyLimit($c);
		$this->render('index',compact('items','count','pages','search','nodata'));
	}

	public function actionDelete(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id!=null){
			OrderRelation::model()->deleteAllByAttributes(array('order_id' => $id));
			ContentOrder::model()->deleteByPk($id);
		}
		$this->redirect(array('index'));
	}
}