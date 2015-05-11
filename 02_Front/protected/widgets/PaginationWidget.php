<?php

class PaginationWidget extends CLinkPager {

    const CSS_PREVIOUS_PAGE = 'backward';
    const CSS_NEXT_PAGE = 'forward';
    const CSS_INTERNAL_PAGE = 'page';
    const CSS_HIDDEN_PAGE = 'hidden';
    const CSS_SELECTED_PAGE = 'stay';

    /**
     * @var integer maximum number of page buttons that can be displayed. Defaults to 10.
     */
    public $maxButtonCount = 3;

    /**
     * @var string the text label for the next page button. Defaults to 'Next &gt;'.
     */
    public $nextPageLabel;

    /**
     * @var string the text label for the previous page button. Defaults to '&lt; Previous'.
     */
    public $prevPageLabel;

    /**
     * @var string the text label for the first page button. Defaults to '&lt;&lt; First'.
     */
    public $header;

    /**
     * @var string the text shown after page buttons.
     */
    public $footer = '';

    /**
     * @var mixed the CSS file used for the widget. Defaults to null, meaning
     * using the default CSS file included together with the widget.
     * If false, no CSS file will be used. Otherwise, the specified CSS file
     * will be included when using this widget.
     */
    public $cssFile;

    /**
     * @var array HTML attributes for the pager container tag.
     */
    public $htmlOptions = array();

    /**
     * Initializes the pager by setting some default property values.
     */
    public function init() {
        if ($this->nextPageLabel === null)
            //$this->nextPageLabel = '/common/img/icon_arrow05_next_n.png';
												//$this->nextPageLabel = '&gt;&gt;';
            $this->nextPageLabel = '≫';
        if ($this->prevPageLabel === null)
            //$this->prevPageLabel = '/common/img/icon_arrow05_prev_n.png';
												//$this->prevPageLabel = '&lt;&lt;';
            $this->prevPageLabel = '≪';
        if ($this->header === null)
            //$this->header = "<div class='pagenation-A01'><nav>";
            //$this->header = '<div class="pageList_link">';
        if ($this->footer === null)
            //$this->footer = "</nav></div>";
            //$this->footer = "</div>";
        if ($this->cssFile === null) {
            $this->cssFile = Yii::app()->request->baseUrl . '/css/pagination.css';
        }
    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $this->registerClientScript();
        $buttons = $this->createPageButtons();
        if (empty($buttons))
            return;
        echo $this->header;
								//echo CHtml::tag('ul', array(), implode("\n", $buttons));
        echo CHtml::tag('ul', array('class' => 'pageList_link'), implode("\n", $buttons));
        echo $this->footer;
    }

    /**
     * Creates the page buttons.
     * @return array a list of page buttons (in HTML code).
     */
    protected function createPageButtons() {
        $firstPage = 1;
        $lastPage = $this->getPageCount();

        if (($pageCount = $this->getPageCount()) <= 1)
            return array();

        list($beginPage, $endPage) = $this->getPageRange();
        $currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons = array();

        // prev page
        if (($page = $currentPage - 1) < 0)
            $page = 0;
        //$buttons[] = $this->createPageButton($this->prevPageLabel, $page, self::CSS_PREVIOUS_PAGE, true, false) . '<li class="direct"><ul>';
        if($currentPage > 0)
        {
            //$buttons[] = $this->createPageButton($this->prevPageLabel, $page, self::CSS_PREVIOUS_PAGE, 0, false) . '<li class="direct"><ul>';
												$buttons[] = $this->createPageButton($this->prevPageLabel, $page, self::CSS_PREVIOUS_PAGE, 0, false);
        }


        // internal pages 
        if ($this->getPageCount() <= 7) {            
            for($i=0;$i<$this->getPageCount();++$i){
                $buttons[]=$this->createPageButton($i+1,$i,self::CSS_SELECTED_PAGE,false,$i==$currentPage);
            }
        } else {
            for ($i = $firstPage - 1; $i <= 1; ++$i) {
                $buttons[] = $this->createPageButton($i + 1, $i, self::CSS_SELECTED_PAGE, false, $i == $currentPage);
            }
            if ($beginPage >= 2 && $endPage < $lastPage - 2) {
                for ($i = $beginPage; $i <= $endPage; ++$i) {
                    $buttons[] = $this->createPageButton($i + 1, $i, self::CSS_SELECTED_PAGE, false, $i == $currentPage);
                }
            } else if ($beginPage < 2) {
                for ($i = 2; $i <= 4; ++$i) {
                    $buttons[] = $this->createPageButton($i + 1, $i, self::CSS_SELECTED_PAGE, false, $i == $currentPage);
                }
            } else if ($endPage >= $lastPage - 2) {
                for ($i = $lastPage - 5; $i <= $lastPage - 3; ++$i) {
                    $buttons[] = $this->createPageButton($i + 1, $i, self::CSS_SELECTED_PAGE, false, $i == $currentPage);
                }
            }


            for ($i = $lastPage - 2; $i <= $lastPage - 1; ++$i) {
                $buttons[] = $this->createPageButton($i + 1, $i, self::CSS_SELECTED_PAGE, false, $i == $currentPage);
            }
        }

        // next page
        if (($page = $currentPage + 1) >= $pageCount - 1)
            $page = $pageCount - 1;
        //$buttons[] = "</ul></li>" . $this->createPageButton($this->nextPageLabel, $page, self::CSS_NEXT_PAGE, true, false);
        if($currentPage < $lastPage-1)
        {
            //$buttons[] = "</ul></li>" . $this->createPageButton($this->nextPageLabel, $page, self::CSS_NEXT_PAGE, 0, false);
												$buttons[] = $this->createPageButton($this->nextPageLabel, $page, self::CSS_NEXT_PAGE, 0, false);
        }


        return $buttons;
    }

    /**
     * Creates a page button.
     * You may override this method to customize the page buttons.
     * @param string $label the text label for the button
     * @param integer $page the page number
     * @param string $class the CSS class for the page button. This could be 'page', 'first', 'last', 'next' or 'previous'.
     * @param boolean $button whether this page button is visible
     * @param boolean $selected whether this page button is selected
     * @return string the generated button
     */
    protected function createPageButton($label, $page, $class, $button, $selected) {
        $currentPage = $this->getCurrentPage();
        $lastPage = $this->getPageCount();
        $firstPage = 0;
        if ($button) {
            $image = CHtml::image($label, "次へ", array("width" => "26", "height" => "26"));
            return '<li class="' . $class . '">' . CHtml::link($image, $this->createPageUrl($page), array("class" => "rollover")) . '</li>';
        }

        if ($page == ($firstPage + 1) && $currentPage > ($firstPage + 3) && $this->getPageCount() > 7) {
            //return '<li>' . CHtml::link($label, $this->createPageUrl($page)) . '<span class="skip-marker">…</span></li>';
												return '<li>' . CHtml::link($label, $this->createPageUrl($page)) . '</li><li><span class="skip-marker">...</span></li>';
        }

        if ($page == ($lastPage - 2) && ($currentPage < $lastPage - 4) && $this->getPageCount() > 7) {
            //return '<li><span class="skip-marker">…</span>' . CHtml::link($label, $this->createPageUrl($page)) . '</li>';
												return '<li><span class="skip-marker">...</span></li><li>' . CHtml::link($label, $this->createPageUrl($page)) . '</li>';
        }

        if ($selected)
            return '<li class="' . $class . '"><strong>' . $label . '</strong></li>';

        return '<li>' . CHtml::link($label, $this->createPageUrl($page)) . '</li>';
    }

    /**
     * @return array the begin and end pages that need to be displayed.
     */
    protected function getPageRange() {
        $currentPage = $this->getCurrentPage();
        $pageCount = $this->getPageCount();

        $beginPage = max(0, $currentPage - (int) ($this->maxButtonCount / 2));
        if (($endPage = $beginPage + $this->maxButtonCount - 1) >= $pageCount) {
            $endPage = $pageCount - 1;
            $beginPage = max(0, $endPage - $this->maxButtonCount + 1);
        }
        return array($beginPage, $endPage);
    }

    /**
     * Registers the needed client scripts (mainly CSS file).
     */
    public function registerClientScript() {
        if ($this->cssFile !== false)
            self::registerCssFile($this->cssFile);
    }

    /**
     * Registers the needed CSS file.
     * @param string $url the CSS URL. If null, a default CSS URL will be used.
     * @since 1.0.2
     */
    public static function registerCssFile($url = null) {
        if ($url === null)
            $url = CHtml::asset(Yii::getPathOfAlias('system.web.widgets.pagers.pager') . '.css');
        Yii::app()->getClientScript()->registerCssFile($url);
    }

}
