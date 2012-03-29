<?php

class DefaultController extends CsbController
{
    /**
     * List user block
     * @return void
     */
   	public function actionIndex()
       {
           $model = new CsbLog('search');
           $model->unsetAttributes(); // clear any default values
           if (isset($_GET['CsbLog']))
               $model->attributes = $_GET['CsbLog'];

           $this->render('index', array(
               'model' => $model,
           ));
       }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    public function actionLive()
    {
        $model = new CsbRequest('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['CsbRequest']))
            $model->attributes = $_GET['CsbRequest'];

        $this->render('live', array(
            'model' => $model,
        ));
    }


    public function loadModel($id)
    {
        $model = CsbLog::model()->findByPk((int)$id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
}