<?php

class DefaultController extends Controller
{
	const UPLOAD_PATH = 'upload/images/';
	const SESS_KEY = '_PAGES';

	public function actionIndex()
	{
		$this->forward('search');
	}

	public function actionEdit(){
		$this->widget('CkEditor');
		$contentPage= null;
		$id = isset($_GET['id']) ? $_GET['id'] : null;

		if($id != null){
			$contentPage = ContentPage::model()->findByPk($id);
		}

		if($contentPage == null) $contentPage = new ContentPage();

		if(isset($_POST['page'])){
			$contentPage->setAttributes($_POST['page']);

			if($contentPage->validate()){

				$contentPage->save(false);

				$this->redirect(array('view','id' => $contentPage->id, 'msg' => true));
			}
		}
		$this->title= $contentPage->id == '' ?'Add Page | CMS Order Sys': 'Edit Page | CMS Order Sys';;
		$this->render('edit',compact('contentPage'));
	}

	public function actionView(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id==null){
			$this->redirect(array('index'));
		}

		$contentPage = ContentPage::model()->findByPk($id);

		if($contentPage == null) $this->redirect(array('index'));

		$this->title='View Page | CMS Order Sys';

		$msg = isset($_GET['msg']) ? true : false;
		$this->render('view',compact('msg','contentPage'));
	}

	public function actionSearch(){
		$this->title='Manage Pages | CMS Order Sys';
		$search['key'] =  '';

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
					case 'key':
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
		$count = ContentPage::model()->count($c);

		$nodata = ($count)?false:true;
		$c->limit = 10;
		$c->offset = $c->limit * ($page-1);
		$items = ContentPage::model()->findAll($c);
		$pages = new CPagination($count);
		$pages->pageSize = $c->limit;
		$pages->applyLimit($c);

		$this->render('index',compact('items', 'catItems', 'count','pages','search','nodata'));
	}

	public function actionDelete(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id!=null){
			ContentPage::model()->deleteByPk($id);
		}
		$this->redirect(array('index'));
	}
}