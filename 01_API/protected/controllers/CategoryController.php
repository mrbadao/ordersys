<?php

class CategoryController extends Controller
{
	/**
	 * Declares class-based actions.
	 */

	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

    public function actionSearch(){
        $search = isset($this->_post_data['search']) ? $this->_post_data['search'] : null;

        $_result = array();
        $_result['total'] =0;
        $_result['categories'] = array();

        $c = new CDbCriteria();
        $c->order = 'id DESC';

        if(isset($this->_post_data['offset']) && is_numeric($this->_post_data['offset'])){
            $c->offset =$this->_post_data['offset'];
        }

        if(isset($this->_post_data['limit']) && is_numeric($this->_post_data['limit'])){
            $c->limit =$this->_post_data['limit'];
        }

        if ($search != null) {
            foreach ($search as $k => $v) {
                if (!isset($v) && $v === '') {
                    continue;
                }
                switch ($k) {
                    case 'name':
                        $c->compare($k, $v, false, 'AND');
                        break;
                    case 'abbr_cd':
                        $c->compare($k, $v, false, 'AND');
                        break;
                }
            }
        }

        $_data_count = ContentCategories::model()->count($c);
        $_data = $_data_count > 0 ? ContentCategories::model()->findAll($c) : null;

        if ($_data == null) {
            Helpers::_sendResponse(200, json_encode(array(
                'error' => array(
                    "error_code" => "1003",
                    "error_message" => "No data.",
                ))));
        }

        $_result['count'] = $_data_count;
        $_result['categories'] = Helpers::_db_fetchDataArray($_data, 'categories');

        Helpers::_sendResponse(200, json_encode($_result));
    }
}