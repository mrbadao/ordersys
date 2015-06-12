<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

    public  function actionEdit(){
        $this->widget('DatePickerWidget');
        $this->setTitle("CMS Order Sys | add saleoff");
        $ContentSaleoff = null;
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $proMsg = false;
        $itemList = null;
        $contentCats = ContentCategories::model()->findAll();

        if($id){
            $ContentSaleoff = ContentSaleoff::model()->findByPk($id);
            $this->setTitle("CMS Order Sys | edit saleoff");
        }

        $ContentSaleoff = $ContentSaleoff ? $ContentSaleoff : new ContentSaleoff();

        if(isset($_POST['saleoff'])){
            $ContentSaleoff->attributes = $_POST['saleoff'];

            if($id) SaleoffRelation::model()->deleteAllByAttributes(array('saleoff_id' => $id));

            if($ContentSaleoff->validate()){
                if(isset($_POST['productid'])){
                    $ContentSaleoff->save(false);
                    $items=$_POST['productid'];

                    foreach($items as $item){
                        $relation = new SaleoffRelation();
                        $relation->saleoff_id = $ContentSaleoff->id;
                        $relation->product_id = $item;
                        $relation->created = date("Y-m-d H:m:i");
                        $relation->modified = date("Y-m-d H:m:i");
                        $relation->save();
                        $itemList[] = ContentProduct::model()->findByPk($item);
                    }
                }else{
                    $proMsg = true;
                }
            }
        }

        return $this->render('edit', compact('ContentSaleoff','contentCats', 'itemList', 'proMsg'));
    }

    public function actionGetPros(){
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