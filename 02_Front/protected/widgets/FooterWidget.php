<?php

class FooterWidget extends CLinkPager {

    public function init() {

    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $settingKey = array('Email', 'Facebook', 'Twitter', 'Gplus', 'Phone', 'Mobile');
        $settings = null;

        foreach ($settingKey as $item) {
            $setting =  self::getSetting($item);
            if($setting != null)
            $settings[$setting->key] = $setting->value;
        }

        $this->render('footer-widget', compact('settings'));
    }

    private function getSetting($key)
    {
        $setting = null;
        $setting = Setting::model()->findByAttributes(array('key' => $key));
        return $setting;
    }

}
