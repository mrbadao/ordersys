<?php

class DefaultController extends Controller
{
	const SESS_KEY = '_FRONT_END_PRODUCT_SEARCH';
	const LIMIT = 1;

	public function actionIndex()
    {
        $category = null;

        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $category = ContentCategories::model()->findByPk($id);

        if($category == null) throw new CHttpException(404,'Url Site not exits');

        $this->setTitle($category->name.' | '.Yii::app()->params['appName']);

        $c = new CDbCriteria();
        $c->order = "id DESC";
        $c->addCondition('del_flg = 0', 'AND');
        $c->addCondition('category_id = '.$id, 'AND');

        $count = ContentProduct::model()->count($c);

        $c->limit = self::LIMIT;
        $c->offset = ($page - 1) * self::LIMIT;

        $items = ContentProduct::model()->findAll($c);

        $pages = new CPagination($count);
        $pages->pageSize = $c->limit;
        $pages->applyLimit($c);

        $title = $category->name;

        $this->render('index', compact('items', 'pages', 'category','title'));
    }

    public function actionSearch(){
        $this->setTitle('Tìm kiếm | '.Yii::app()->params['appName']);

        $search['name'] = '';

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

        $c->limit = self::LIMIT;
        $c->offset = $c->limit * ($page-1);

        $items = ContentProduct::model()->findAll($c);

        $pages = new CPagination($count);
        $pages->pageSize = $c->limit;
        $pages->applyLimit($c);

        $title = 'Tìm kiếm "'.$search['name'].'"';
        $this->render('index',compact('items','pages','nodata', 'title'));
    }
}