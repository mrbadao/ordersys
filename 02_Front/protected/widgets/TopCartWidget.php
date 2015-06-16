<?php

class TopCartWidget extends CLinkPager
{
    const SESSION_KEY = '_CART';
    public $session;

    public function init()
    {
        $this->session = Yii::app()->session;
    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run()
    {
        $count = 0;
        $total = 0;
        $cart = array();

        if ($this->session->contains(self::SESSION_KEY)) {
            $cart = $this->session[self::SESSION_KEY];
        }

        $count = count($cart);

        if ($count > 0) {
            for ($i = 0; $i < count($cart); $i++) {
                $product = Helpers::getProduct($cart[$i]['id']);

                if ($product) {
                    $total += $product->saleoff_price !='' ? $product->saleoff_price * $cart[$i]['qty'] : $product->price * $cart[$i]['qty'];
                }
            }
            $total = number_format($total);
        }

        $this->render('top-cart-widget', compact('count', 'total'));
    }

}
