<?php

class NewProductWidget extends CLinkPager {

    public function init() {

    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $c = new CDbCriteria();
        $c->offset = 0;
        $c->limit = 5;
        $c->order = "id DESC";
        $c->addCondition('del_flg = 0 ', 'AND');

        $items=  ContentProduct::model()->findAll($c);

        $this->render('popular-product-widget', compact('items'));
    }

}
