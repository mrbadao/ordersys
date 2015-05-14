<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
		$this->title='Setting | CMS Order Sys';
		$msg = false;
		$siteSettings = null;
		$settingKey = array('Email', 'Facebook', 'Twitter', 'Gplus', 'Phone', 'Mobile');

		foreach($settingKey as $item){
			if(isset($_POST['setting'])) {
				$siteSettings[] = self::getSetting($item, $_POST['setting'][$item]);
				$msg = true;
			}
			$siteSettings[] = self::getSetting($item, '');
		}

		$this->render('index', compact('$siteSettings', 'msg'));
	}

	private function getSetting($key, $val){
		$setting = Setting::model()->findByAttributes(array('key' => $key));

		if($setting) return $setting;
		else{
			$setting = new Setting();
			$setting->key = $key;
			$setting->value = $val;
			$setting->save(false);
			return $setting;
		}
	}
}