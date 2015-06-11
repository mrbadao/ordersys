<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

    public  function actionEdit(){
        return $this->render('edit');
    }
}