<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

    public  function actionEdit(){
        $this->widget('DatePickerWidget');
        $ContentSaleoff = new ContentSaleoff();
        $contentCats = ContentCategories::model()->findAll();

        return $this->render('edit', compact('ContentSaleoff','contentCats'));
    }
}