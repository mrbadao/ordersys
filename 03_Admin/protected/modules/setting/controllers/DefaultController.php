<?php

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $this->title = 'Setting | CMS Order Sys';
        $msg = false;
        $siteSettings = null;
        $settingKey = array('Email', 'Facebook', 'Twitter', 'Gplus', 'Phone', 'Mobile');

        foreach ($settingKey as $item) {
            if (isset($_POST['setting'])) {
                $siteSettings[] = self::getSetting($item, $_POST['setting'][$item]);
                $msg = true;
            } else {
                $siteSettings[] = self::getSetting($item, '');
            }
        }

        $this->render('index', compact('siteSettings', 'msg'));
    }

    private function getSetting($key, $val)
    {
        $setting = null;
        $setting = Setting::model()->findByAttributes(array('key' => $key));

        if ($setting == null) {
            $setting = new Setting();
            $setting->key = $key;
        }

        $setting->value = $val;
        $setting->save(false);

        return $setting;
    }
}