<?php
class PHPTALViewRenderer extends CApplicationComponent implements IViewRenderer
{
    public $PHPTALPathAlias = 'application.vendors.phptal';

    public $fileExtension = '.html';

    private $_basePath;
    private $_basePathLength;

    function init()
    {
        require_once Yii::getPathOfAlias($this->PHPTALPathAlias).'/PHPTAL.php';
        Yii::registerAutoloader(array('PHPTAL','autoloadRegister'),true);

        $app = Yii::app();
        $theme = $app->getTheme();

        if($theme == null)
        {
            $this->_basePath = $app->getBasePath();
        }
        else
        {
            $this->_basePath = $theme->getBasePath();
        }
        $this->_basePathLength = strlen($this->_basePath);
    }

    public function renderFile($context, $sourceFile, $data, $return)
    {
        $sourceFile = 'protected'.substr($sourceFile, $this->_basePathLength);
        $tal = new PHPTAL($sourceFile);
        $tal->setOutputMode(PHPTAL::HTML5);
        $tal->this = $context;

        if(is_array($data) && 0 < count($data))
        {
            foreach($data as $k => $v)
            {
                $tal->$k = $v;
            }
        }
        $tal->set('SERVER',$_SERVER);
        $tal->set('params',Yii::app()->params);

        $res = $tal->execute();
        if($return)
        {
            return $res;
        }
        echo $res;
    }
}
function phptal_tales_dateformat($src,$nothrow = true)
{
    $params = preg_split('/\s/',mb_convert_kana(trim($src),'s'));
    $src = array_shift($params);
    if(!isset($params[0]) || !$params[0])
    {
        //$format = 'Y年m月d日 H時i分';
        $format = 'Y/m/d H:i';
    }
    else
    {
        $format = implode(' ',$params);
    }
    return 'date("'.$format.'",strtotime( '.phptal_tales($src, $nothrow).'))';
}
function phptal_tales_selected($src,$nothrow = false)
{
    list($l,$r) = preg_split('/\s/',mb_convert_kana(trim($src),'s'));
    return '('.phptal_tales($l,$nothrow).' == '.phptal_tales($r,$nothrow).
        ' && '.phptal_tales($l,$nothrow).'!= "" && '.phptal_tales($r,$nothrow).'!= "")?"selected":""';
}
function phptal_tales_checked($src,$nothrow = false)
{
    list($l,$r) = preg_split('/\s/',mb_convert_kana(trim($src),'s'));
    return '('.phptal_tales($l,$nothrow).' == '.phptal_tales($r,$nothrow).
        ' && '.phptal_tales($l,$nothrow).'!= "" && '.phptal_tales($r,$nothrow).'!= "")?"checked":""';
}
