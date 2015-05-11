<?php

class CategoryWidget extends CLinkPager {

    public function init() {

    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $items = ContentCategories::model()->findAll();
        $this->render('category-widget', compact('items'));
    }

}
