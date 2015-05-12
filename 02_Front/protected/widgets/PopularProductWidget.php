<?php

class PopularProductWidget extends CLinkPager {

    public function init() {

    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $query =' SELECT `order_relation`.`product_id` AS `id` , `content_product`.`name` , `content_product`.`thumbnail` , `content_product`.`description` , `content_product`.`price` , `content_product`.`category_id` , `content_product`.`created` , `content_product`.`modified`'.
            ' FROM `order_relation`'.
            ' JOIN `content_product` ON `content_product`.`id` = `order_relation`.`product_id`'.
            ' WHERE del_flg = 0 '.
            ' GROUP BY `product_id`'.
            ' ORDER BY Count( * ) DESC'.
            ' LIMIT 0, 5;';

        $items=  Yii::app()->db->createCommand($query)->queryAll();

        for($i = 0; $i < count($items); $i++){
            $items[$i]['frendlyUrl'] = '';

            $category = ContentCategories::model()->findByPk($items[$i]['category_id']);

            if($category){
                $items[$i]['frendlyUrl'] = $category->abbr_cd.'/'.$items[$i]['id'].'/'.Helpers::getDomainFromName($items[$i]['name']).'.html';
            }
        }

        $this->render('popular-product-widget', compact('items'));
    }

}
