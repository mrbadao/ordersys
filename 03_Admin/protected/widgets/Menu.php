<?php

class Menu extends CWidget
{
    public function init()
    {
        if (Yii::app()->user->IsGuest) {
            $this->owner->redirect('/admin/site/login');
        }
    }

    public function run()
    {
        return $this->render('menu');
    }
}

