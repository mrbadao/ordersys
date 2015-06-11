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

    public function actionGetCats(){
        if(Yii::app()->request->isAjaxRequest){
            $id=$_POST['id'];

            if($id!=null){
                $products= ContentProduct::model()->findAllByAttributes(array('category_id' => $id));
                $result = array();
                foreach($products as $product){
                    $result[] = $product->attributes;
                }

                echo json_encode($result);
            }
        }
    }
}