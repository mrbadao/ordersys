<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	public $metaTags = '';
	public $metaDescription = '';
	public $title = '';
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
//	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();

	public function init()
	{
		$this->setTitle('Sài Gòn ET');

//		$setting = Setting::model()->findByPk(1);
//		if($setting){
//			$this->metaDescription = $setting->meta_description;
//			$this->metaTags = $setting->meta_tag;
//		}

		$this->menu['items'] = array(
			array('label'=>'Trang chủ', 'url'=>'/site', 'id' =>'/content/index'),
			array('label'=>'Giới thiệu', 'url'=>'/site/content/about', 'id' =>'/content/about'),
			array('label'=>'Giải pháp & Tài liệu', 'url'=> '/site/giai-phap-va-tai-lieu', 'id' => 'solution'),
			array('label'=>'Dịch vụ', 'url'=> '/site/dich-vu', 'id' => 'services'),
			array('label'=>'Công trình & Dự án', 'url'=> '/site/cong-trinh-du-an', 'id' => 'project'),
			array('label'=>'Tin tức & Sự kiện', 'url'=> '/site/tin-tuc-su-kien', 'id' =>'news'),
			array('label'=>'Liên hệ', 'url'=> '/site/content/contact', 'id' => '/content/contact'),
		);
	}

	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function setTitle($title)
	{
		$this->title = $title;
	}
}