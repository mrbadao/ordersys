<?php

class HeaderWidget extends CLinkPager {
    public $menu = array();

    public function init() {

    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $this->render('header-widget');
    }

}
