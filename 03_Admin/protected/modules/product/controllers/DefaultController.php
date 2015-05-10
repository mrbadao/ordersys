<?php

class DefaultController extends Controller
{
	const UPLOAD_PATH = 'upload/images/';
	const SESS_KEY = '_PRODUCT';

	public function actionIndex()
	{
		$this->forward('search');
	}

	public function actionEdit(){
		$this->widget('CkEditor');
		$contentProduct= null;
		$contentCats = ContentCategories::model()->findAll();
		$id = isset($_GET['id']) ? $_GET['id'] : null;

		if($id != null){
			$contentProduct = ContentProduct::model()->findByPk($id);
		}

		if($contentProduct == null) $contentProduct = new ContentProduct();

		if(isset($_POST['product'])){
			if($contentProduct->getIsNewRecord()){
				$contentProduct->created = date("Y-m-d H:m:i");
			}

			$contentProduct->modified = date("Y-m-d H:m:i");
			$contentProduct->setAttributes($_POST['product']);

			$uploadExt =array(
				'jpg' => 'image/jpeg',
				'png' => 'image/png',
				'gif' => 'image/gif',
			);

			$uploadPath = substr(Yii::app()->request->getBaseUrl(true),0,-5).self::UPLOAD_PATH;

			if(isset($_FILES['silde1']) && $_FILES['silde1']['name'] != null){
				$silde1_name = time().'_1.'.array_search($_FILES['silde1']['type'],$uploadExt);
				if(move_uploaded_file($_FILES['silde1']['tmp_name'],'../upload/images/'.$silde1_name)){
					$contentProduct->thumbnail = $uploadPath.$silde1_name;
				}
			}


			if($contentProduct->validate()){

				$contentProduct->save(false);

				$this->redirect(array('view','id' => $contentProduct->id, 'msg' => true));
			}
		}
		$this->title= $contentProduct->id == '' ?'Add Product | CMS Order Sys': 'Edit Product | CMS Order Sys';;
		$this->render('edit',compact('contentCats', 'contentProduct', 'tags'));
	}

	public function actionView(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id==null){
			$this->redirect(array('index'));
		}

		$contentProduct = ContentProduct::model()->findByPk($id);

		if($contentProduct == null) $this->redirect(array('index'));

		$this->title='View Product | CMS Saigonet';

		$msg = isset($_GET['msg']) ? true : false;
		$this->render('view',compact('msg','contentProduct'));
	}

	public function actionSearch(){
		$this->title='Manage Product | CMS Saigonet';
		$search['name'] = $search['category_id'] = $search['del_flg'] = '';

		$catItems = ContentCategories::model()->findAll();

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
					case 'category_id':
						$c->compare($k, $v, false,'AND');
						break;
					case 'del_flg':
						if($v != '')
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
		$count = ContentProduct::model()->count($c);

		$nodata = ($count)?false:true;
		$c->limit = 10;
		$c->offset = $c->limit * ($page-1);
		$items = ContentProduct::model()->findAll($c);
		$pages = new CPagination($count);
		$pages->pageSize = $c->limit;
		$pages->applyLimit($c);

		$this->render('index',compact('items', 'catItems', 'count','pages','search','nodata'));
	}

	public function actionDelete(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id!=null){
			ContentProduct::model()->deleteByPk($id);
		}
		$this->redirect(array('index'));
	}
}