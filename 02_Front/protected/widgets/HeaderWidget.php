<?php

class HeaderWidget extends CLinkPager {
    public $menu = array();

    public function init() {
        $this->menu['items'] = array(
            array('label'=>'Trang chủ', 'url'=>'/site/', 'id' =>'/content/index'),
            array('label'=>'Giới thiệu', 'url'=>'/site/gioi-thieu.html', 'id' =>'/content/about'),
            array('label'=>'Thông tin giao hàng', 'url'=> '/site/thong-tin-giao-hang.html', 'id' => '/content/about/deliveryinfo'),
            array('label'=>'Liên hệ', 'url'=> '/site/lien-he.html', 'id' => '/content/contact'),
        );

        $this->menu['activeCssClass'] = 'current';
        $this->menu['defaultActiveID'] = '/content/index';
        $this->menu['htmlOptions'] = array('id' => 'main-menu', 'class' => 'main-menu clearfix rr');
    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run() {
        $menu = $this->menu;
        $this->render('header-widget', compact('menu'));
    }

}
