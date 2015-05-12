<?php
/**
 * CMenu class file.
 *
 * @author Jonah Turnquist <poppitypop@gmail.com>
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CMenu displays a multi-level menu using nested HTML lists.
 *
 * The main property of CMenu is {@link items}, which specifies the possible items in the menu.
 * A menu item has three main properties: visible, active and items. The "visible" property
 * specifies whether the menu item is currently visible. The "active" property specifies whether
 * the menu item is currently selected. And the "items" property specifies the child menu items.
 *
 * The following example shows how to use CMenu:
 * <pre>
 * $this->widget('zii.widgets.CMenu', array(
 *     'items'=>array(
 *         // Important: you need to specify url as 'controller/action',
 *         // not just as 'controller' even if default action is used.
 *         array('label'=>'Home', 'url'=>array('site/index')),
 *         // 'Products' menu item will be selected no matter which tag parameter value is since it's not specified.
 *         array('label'=>'Products', 'url'=>array('product/index'), 'items'=>array(
 *             array('label'=>'New Arrivals', 'url'=>array('product/new', 'tag'=>'new')),
 *             array('label'=>'Most Popular', 'url'=>array('product/index', 'tag'=>'popular')),
 *         )),
 *         array('label'=>'Login', 'url'=>array('site/login'), 'visible'=>Yii::app()->user->isGuest),
 *     ),
 * ));
 * </pre>
 *
 *
 * @author Jonah Turnquist <poppitypop@gmail.com>
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package zii.widgets
 * @since 1.1
 */
class NaviBar extends CWidget
{
    public $items=array();
    public $defaultActiveID;
    /**
     * @var string the template used to render an individual menu item. In this template,
     * the token "{menu}" will be replaced with the corresponding menu link or text.
     * If this property is not set, each menu will be rendered without any decoration.
     * This property will be overridden by the 'template' option set in individual menu items via {@items}.
     * @since 1.1.1
     */
    public $itemTemplate;
    /**
     * @var boolean whether the labels for menu items should be HTML-encoded. Defaults to true.
     */
    public $encodeLabel=true;
    /**
     * @var string the CSS class to be appended to the active menu item. Defaults to 'active'.
     * If empty, the CSS class of menu items will not be changed.
     */
    public $activeCssClass='active';
    /**
     * @var boolean whether to automatically activate items according to whether their route setting
     * matches the currently requested route. Defaults to true.
     * @since 1.1.3
     */
    public $activateItems=true;
    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     * The activated parent menu items will also have its CSS classes appended with {@link activeCssClass}.
     * Defaults to false.
     */
    public $activateParents=false;
    /**
     * @var boolean whether to hide empty menu items. An empty menu item is one whose 'url' option is not
     * set and which doesn't contain visible child menu items. Defaults to true.
     */
    public $hideEmptyItems=true;
    /**
     * @var array HTML attributes for the menu's root container tag
     */
    public $htmlOptions=array();
    /**
     * @var array HTML attributes for the submenu's container tag.
     */
    public $submenuHtmlOptions=array();
    /**
     * @var string the HTML element name that will be used to wrap the label of all menu links.
     * For example, if this property is set as 'span', a menu item may be rendered as
     * &lt;li&gt;&lt;a href="url"&gt;&lt;span&gt;label&lt;/span&gt;&lt;/a&gt;&lt;/li&gt;
     * This is useful when implementing menu items using the sliding window technique.
     * Defaults to null, meaning no wrapper tag will be generated.
     * @since 1.1.4
     */
    public $linkLabelWrapper;
    /**
     * @var array HTML attributes for the links' wrap element specified in
     * {@link linkLabelWrapper}.
     * @since 1.1.13
     */
    public $linkLabelWrapperHtmlOptions=array();
    /**
     * @var string the CSS class that will be assigned to the first item in the main menu or each submenu.
     * Defaults to null, meaning no such CSS class will be assigned.
     * @since 1.1.4
     */
    public $firstItemCssClass;
    /**
     * @var string the CSS class that will be assigned to the last item in the main menu or each submenu.
     * Defaults to null, meaning no such CSS class will be assigned.
     * @since 1.1.4
     */
    public $lastItemCssClass;
    /**
     * @var string the CSS class that will be assigned to every item.
     * Defaults to null, meaning no such CSS class will be assigned.
     * @since 1.1.9
     */
    public $itemCssClass;

    /**
     * Initializes the menu widget.
     * This method mainly normalizes the {@link items} property.
     * If this method is overridden, make sure the parent implementation is invoked.
     */
    public function init()
    {
        if(isset($this->htmlOptions['id']))
            $this->id=$this->htmlOptions['id'];
        else
            $this->htmlOptions['id'] = $this->id;
        $route=$this->getController()->getRoute();
        $this->items=$this->normalizeItems($this->items,$route,$hasActiveChild);
    }

    /**
     * Calls {@link renderMenu} to render the menu.
     */
    public function run()
    {
        $this->renderMenu($this->items);
    }

    /**
     * Renders the menu items.
     * @param array $items menu items. Each menu item will be an array with at least two elements: 'label' and 'active'.
     * It may have three other optional elements: 'items', 'linkOptions' and 'itemOptions'.
     */
    protected function renderMenu($items)
    {
        if(count($items))
        {
            echo CHtml::openTag('ul',$this->htmlOptions)."\n";
            $this->renderMenuRecursive($items);
            echo CHtml::closeTag('ul');
        }
    }

    /**
     * Recursively renders the menu items.
     * @param array $items the menu items to be rendered recursively
     */
    protected function renderMenuRecursive($items)
    {
        $count=0;
        $n=count($items);
        foreach($items as $item)
        {
            $count++;
            $options=isset($item['itemOptions']) ? $item['itemOptions'] : array();
            $class=array();

            if($item['active'] && $this->activeCssClass!='')
                $class[]=$this->activeCssClass;
            if($count===1 && $this->firstItemCssClass!==null)
                $class[]=$this->firstItemCssClass;
            if($count===$n && $this->lastItemCssClass!==null)
                $class[]=$this->lastItemCssClass;
            if($this->itemCssClass!==null)
                $class[]=$this->itemCssClass;
            if($class!==array())
            {
                if(empty($options['class']))
                    $options['class']=implode(' ',$class);
                else
                    $options['class'].=' '.implode(' ',$class);
            }

            echo CHtml::openTag('li');

            $menu=$this->renderMenuItem($item,$options);
            if(isset($this->itemTemplate) || isset($item['template']))
            {
                $template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
                echo strtr($template,array('{menu}'=>$menu));
            }
            else
                echo $menu;

            if(isset($item['items']) && count($item['items']))
            {
                echo "\n".CHtml::openTag('ul',isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions)."\n";
                $this->renderMenuRecursive($item['items']);
                echo CHtml::closeTag('ul')."\n";
            }

            echo CHtml::closeTag('li')."\n";
        }
    }

    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     * @param array $item the menu item to be rendered. Please see {@link items} on what data might be in the item.
     * @return string
     * @since 1.1.6
     */
    protected function renderMenuItem($item, $options)
    {
        if(isset($item['url']))
        {
            $label=$this->linkLabelWrapper===null ? $item['label'] : CHtml::tag($this->linkLabelWrapper, $this->linkLabelWrapperHtmlOptions, $item['label']);
            return CHtml::link($label,$item['url'],isset($options) ? $options : array());
        }
        else
            return CHtml::tag('span',isset($options) ? $options : array(), $item['label']);
    }

    /**
     * Normalizes the {@link items} property so that the 'active' state is properly identified for every menu item.
     * @param array $items the items to be normalized.
     * @param string $route the route of the current request.
     * @param boolean $active whether there is an active child menu item.
     * @return array the normalized menu items
     */
    protected function normalizeItems($items,$route,&$active)
    {
        foreach($items as $i=>$item)
        {
            if(isset($item['visible']) && !$item['visible'])
            {
                unset($items[$i]);
                continue;
            }
            if(!isset($item['label']))
                $item['label']='';
            $encodeLabel = isset($item['encodeLabel']) ? $item['encodeLabel'] : $this->encodeLabel;
            if($encodeLabel)
                $items[$i]['label']=CHtml::encode($item['label']);
            $hasActiveChild=false;
            if(isset($item['items']))
            {
                $items[$i]['items']=$this->normalizeItems($item['items'],$route,$hasActiveChild);
                if(empty($items[$i]['items']) && $this->hideEmptyItems)
                {
                    unset($items[$i]['items']);
                    if(!isset($item['url']))
                    {
                        unset($items[$i]);
                        continue;
                    }
                }
            }
            if(!isset($item['active']))
            {
                if($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item,$route))
                    $active=$items[$i]['active']=true;
                else
                    $items[$i]['active']=false;
            }
            elseif($item['active'])
                $active=true;
        }
        return array_values($items);
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if the currently requested URL is generated by the 'url' option
     * of the menu item. Note that the GET parameters not specified in the 'url' option will be ignored.
     * @param array $item the menu item to be checked
     * @param string $route the route of the current request
     * @return boolean whether the menu item is active
     */
    protected function isItemActive($item,$route)
    {
        if (isset($item['id'])) {
            if(strpos($route, trim($item['id'], '/')) > -1)
                return true;
            elseif(isset($this->defaultActiveID) && $item['id'] == $this->defaultActiveID) return true;
        }
        return false;
    }
}