<?php
/**
 * Created by PhpStorm.
 * User: mrBadao
 */
class CkEditor extends CWidget
{
    public function init()
    {
    }

    public function run()
    {
        $baseUrl = Yii::app()->request->getBaseUrl(true);
        Yii::app()->clientScript
            ->registerScriptFile($baseUrl . '/ckplugin/ckeditor/ckeditor.js', CClientScript::POS_END)
            ->registerScriptFile($baseUrl . '/ckplugin/ckfinder/ckfinder.js', CClientScript::POS_END);
    }
}