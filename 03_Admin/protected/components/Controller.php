<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	public $userName;
	public $viewPath;
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
//	public $layout='main';
	public $title='';

	public function init()
	{
		$this->userName = Yii::app()->user->getName();
		$this->setTitle(Yii::app()->params['appName']);
		$m = $this->getModule();
		$this->viewPath = (isset($m))?$m->getViewPath():Yii::app()->getViewPath();
	}

	public function render($view,$data=null,$return=false)
	{
		if($this->beforeRender($view))
		{
			$output=$this->renderPartial($view,$data,true);
			if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
				if(!is_array($data))
				{
					$output=$this->renderFile($layoutFile,array('content'=>$output),true);
				}
				else
				{
					$output=$this->renderFile($layoutFile,array_merge(array('content'=>$output),$data),true);
				}

			$this->afterRender($view,$output);

			$output=$this->processOutput($output);

			if($return)
				return $output;
			else
				echo $output;
		}
	}

	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
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