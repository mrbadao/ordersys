<?php

class SnsFooterWidget extends CLinkPager {
    public $settings;

    public function init() {

    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $settings = $this->settings;
        $this->render('sns-footer-widget', compact('settings'));
    }

}
