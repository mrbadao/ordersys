<?php

class CategoryWidget extends CLinkPager {
    public $activeCategoryId;
    public function init() {

    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $items = ContentCategories::model()->findAll();
        $activeCategoryId = isset($this->activeCategoryId) ? $this->activeCategoryId : '-1';
        $this->render('category-widget', compact('items', 'activeCategoryId'));
    }

}
