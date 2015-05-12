<?php

class DefaultController extends Controller
{
	const SESS_KEY = '_CATEGORIES';
	const LIMIT = 10;

	public function actionIndex()
    {
        $category = null;
        $this->layout = "main";

        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $category = ContentCategories::model()->findByPk($id);

        if($category == null) throw new CHttpException(404,'Url Site not exits');

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

        $this->render('index', compact('items', 'pages', 'category'));
    }
}