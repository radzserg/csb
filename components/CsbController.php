<?php

/**
 * You should customize it,
 *
 * for example i use layout from my admin module
 * and allow to check info only for admins
 */
abstract class CsbController extends Controller
{

    public $layout='application.modules.admin.views.layouts.new';

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
    	// enable access only for administrators
        return array(
            array('allow',
                'roles' => array('admin')
            ),
            array('deny',
                'users'=>array('*'),
            )
        );
    }

}