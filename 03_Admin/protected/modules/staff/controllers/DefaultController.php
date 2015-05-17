<?php

class DefaultController extends Controller
{
	const SESS_KEY = '_STAFF';

	public function actionIndex()
	{
		$this->forward('search');
	}

	public function actionSetOrder(){
		$this->render('setorder');
	}

	public function actionEdit(){
        $contentStaff= null;
		$id = isset($_GET['id']) ? $_GET['id'] : null;

		if($id != null){
            $contentStaff = DeliveryStaff::model()->findByPk($id);
		}

		if($contentStaff == null) $contentStaff = new DeliveryStaff();

		if(isset($_POST['staff'])){
            $contentStaff->setAttributes($_POST['staff']);

			if($contentStaff->validate()){
                if(isset($_POST['staff']['pasword'])){
                    $contentStaff->pasword = md5($_POST['staff']['pasword']);
                }
                $contentStaff->save(false);
				$this->redirect(array('view','id' => $contentStaff->id, 'msg' => true));
			}
		}
		$this->title= $contentStaff->id == '' ?'Add Staff | CMS Order Sys': 'Edit Staff | CMS Order Sys';
		$this->render('edit',compact('contentStaff'));
	}

	public function actionView(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id==null){
			$this->redirect(array('index'));
		}

        $contentStaff = DeliveryStaff::model()->findByPk($id);

		if($contentStaff == null) $this->redirect(array('index'));

		$this->title='View Staff | CMS Order Sys';

		$msg = isset($_GET['msg']) ? true : false;
		$this->render('view',compact('msg','contentStaff'));
	}

	public function actionSearch(){
		$this->title='Manager Staff | CMS Order Sys';
		$search['login_id'] = $search['phone'] = $search['name'] = '';

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
                    case 'login_id':
                        $c->compare($k, $v, true,'AND');
                        break;
                    case 'name':
                        $c->compare($k, $v, true,'AND');
                        break;
					case 'phone':
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
		$count = DeliveryStaff::model()->count($c);

		$nodata = ($count)?false:true;
		$c->limit = 10;
		$c->offset = $c->limit * ($page-1);
		$items = DeliveryStaff::model()->findAll($c);
		$pages = new CPagination($count);
		$pages->pageSize = $c->limit;
		$pages->applyLimit($c);
		$this->render('index',compact('items','count','pages','search','nodata'));
	}

	public function actionDelete(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id!=null){
			DeliveryStaff::model()->deleteByPk($id);
		}
		$this->redirect(array('index'));
	}
}