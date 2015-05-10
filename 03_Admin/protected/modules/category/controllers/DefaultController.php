<?php

class DefaultController extends Controller
{
	const SESS_KEY = '_CATEGORIES';

	public function actionIndex()
	{
		$this->forward('search');
	}

	public function actionEdit(){
		$contentCats= null;
		$id = isset($_GET['id']) ? $_GET['id'] : null;

		if($id != null){
			$contentCats = ContentCategories::model()->findByPk($id);
		}

		if($contentCats == null) $contentCats = new ContentCategories();

		if(isset($_POST['cat'])){
			if($contentCats->getIsNewRecord()){
				$contentCats->created = date("Y-m-d H:m:i");
			}
			$contentCats->modified = date("Y-m-d H:m:i");
			$contentCats->setAttributes($_POST['cat']);

			if($contentCats->validate()){
				$contentCats->save(false);
				$this->redirect(array('view','id' => $contentCats->id, 'msg' => true));
			}
		}
		$this->title= $contentCats->id == '' ?'Add Category | CMS Sagigonet': 'Edit Category | CMS Sagigonet';;
		$this->render('edit',compact('contentCats'));
	}

	public function actionView(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id==null){
			$this->redirect(array('index'));
		}

		$contentCat = ContentCategories::model()->findByPk($id);

		if($contentCat == null) $this->redirect(array('index'));

		$this->title='View Category | CMS Saigonet';

		$msg = isset($_GET['msg']) ? true : false;
		$this->render('view',compact('msg','contentCat'));
	}

	public function actionSearch(){
		$this->title='Manager Categories | CMS Saigonet';
		$search['name'] = $search['abbr_cd'] = '';

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
					case 'abbr_cd':
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
		$count = ContentCategories::model()->count($c);

		$nodata = ($count)?false:true;
		$c->limit = 10;
		$c->offset = $c->limit * ($page-1);
		$items = ContentCategories::model()->findAll($c);
		$pages = new CPagination($count);
		$pages->pageSize = $c->limit;
		$pages->applyLimit($c);
		$this->render('index',compact('items','count','pages','search','nodata'));
	}

	public function actionDelete(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id!=null){
			ContentCategories::model()->deleteByPk($id);
		}
		$this->redirect(array('index'));
	}
}