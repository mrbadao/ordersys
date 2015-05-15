<?php

class DefaultController extends Controller
{
	const SESS_KEY = '_FEEDBACK';

	public function actionIndex()
	{
		$this->forward('search');
	}

	public function actionView(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id==null){
			$this->redirect(array('index'));
		}

		$contentFeedBack = ContentContact::model()->findByPk($id);

		if($contentFeedBack == null) $this->redirect(array('index'));

		$this->title='View Feedback | CMS Order Sys';

		$msg = isset($_GET['msg']) ? true : false;
		$this->render('view',compact('msg','contentFeedBack'));
	}

	public function actionSearch(){
        $this->widget('DatePickerWidget');

		$this->title='Manager Feedback | CMS Order Sys';
		$search['name'] = $search['email'] = $search['created'] = '';

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
                    case 'email':
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
		$count = ContentContact::model()->count($c);

		$nodata = ($count)?false:true;
		$c->limit = 10;
		$c->offset = $c->limit * ($page-1);
		$items = ContentContact::model()->findAll($c);
		$pages = new CPagination($count);
		$pages->pageSize = $c->limit;
		$pages->applyLimit($c);

		$this->render('index',compact('items','count','pages','search','nodata'));
	}

	public function actionDelete(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id!=null){
			ContentContact::model()->deleteByPk($id);
		}
		$this->redirect(array('index'));
	}
}