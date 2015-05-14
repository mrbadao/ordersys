<?php

class ContentController extends Controller
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }


    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionAbout()
    {
        $this->setTitle('Giới thiệu | ' . Yii::app()->params['appName']);
        $page = ContentPage::model()->findByAttributes(array('key' => 'about'));
        $this->render('about', compact('page'));
    }

    public function actionDeliveryInfo()
    {
        $hasError = false;
        $order = null;

        $page = ContentPage::model()->findByAttributes(array('key' => 'deliveryinfo'));

        if ($_POST['name']) {
            $order = ContentOrder::model()->findByAttributes(array('name' => $_POST['name']));
            if ($order == null) $hasError = true;
        }

        $this->render('deliveryinfo', compact('order', 'hasError', 'page'));
    }

    public function actionContact()
    {
        $this->setTitle('Liên hệ | ' . Yii::app()->params['appName']);

        $shopLocation = Yii::app()->params['shopLocation'];

        $settings = null;
        $settingKey = array('Email', 'Phone', 'Mobile');

        foreach ($settingKey as $item) {
            $setting = self::getSetting($item);
            if ($setting != null)
                $settings[$setting->key] = $setting->value;
        }

        $msg = false;
        $contact = null;

        if (isset($_POST['contact'])) {
            $contact = new ContentContact();
            $contact->attributes = $_POST['contact'];
            if ($contact->validate()) {
                $contact->save(false);
                $msg = true;
                $contact = null;
            }
        }

        $this->render('contact', compact('settings', 'shopLocation', 'contact', 'msg'));
    }

    public function actionError()
    {
        $this->layout = "error-layout";
        $this->render('error');
    }

    private function getSetting($key)
    {
        $setting = null;
        $setting = Setting::model()->findByAttributes(array('key' => $key));
        return $setting;
    }
}