<?php

class DefaultController extends Controller
{
	const SESS_KEY = '_CATEGORIES';

	public function actionIndex()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        var_dump($id);
        die;
    }
}