<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	public $viewPath;
	public $title = '';
    public $breadcrumbs = array();
    public $menu = array();

	public function init()
	{
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

	public function setTitle($title)
	{
		$this->title = $title;
	}
}