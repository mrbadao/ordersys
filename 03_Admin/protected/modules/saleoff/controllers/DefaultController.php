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
            $itemList = SaleoffRelation::model()->findAllByAttributes(array('saleoff_id' => $id));
            $this->setTitle("CMS Order Sys | edit saleoff");
        }

        $ContentSaleoff = $ContentSaleoff ? $ContentSaleoff : new ContentSaleoff();

        if(isset($_POST['saleoff'])){
            $ContentSaleoff->attributes = $_POST['saleoff'];

            if($id) SaleoffRelation::model()->deleteAllByAttributes(array('saleoff_id' => $id));
            $itemList=null;

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
                    $this->redirect(array('view','id' => $ContentSaleoff->id, 'msg' => true));
                }else{
                    $proMsg = true;
                }
            }
        }
        return $this->render('edit', compact('ContentSaleoff','contentCats', 'itemList', 'proMsg'));
    }

    public function actionView(){
        $id=isset($_GET['id']) ? $_GET['id'] : null;
        $msg=isset($_GET['msg']) ? $_GET['msg'] : null;

        if(!$id) $this->redirect('/admin/saleoff');

        $contendSaleoff = ContentSaleoff::model()->findByPk($id);

        if(!$contendSaleoff) $this->redirect('/admin/saleoff');

        $itemList = SaleoffRelation::model()->findAllByAttributes(array('saleoff_id' => $id));
        $this->setTitle("CMS Order Sys | saleoff detail");
        return $this->render('view', compact('contendSaleoff','itemList', 'msg'));

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