<?php

class ContentController extends Controller
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


	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$this->render('index');
	}

    public function actionAbout(){
		$this->setTitle('Giới thiệu | '.Yii::app()->params['appName']);
        $this->render('about');
    }

    public function actionDeliveryInfo(){
        var_dump('thong tin giao hang');
    }

    public function actionContact(){
		$this->setTitle('Liên hệ | '.Yii::app()->params['appName']);
        var_dump('Lien he');
    }

    public function actionError(){
        $this->layout = "error-layout";
        $this->render('error');
    }
}