<?php

class DefaultController extends Controller
{

	const SESS_KEY = '_PRODUCT';

	public function actionIndex()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : null;
        var_dump($id);
	}
}