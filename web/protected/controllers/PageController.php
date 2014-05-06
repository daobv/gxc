<?php

class PageController extends FeController {

    //public $defaultAction='render';

    public function allowedActions() {
        return 'index,view';
    }

    public function actionIndex() {
        $slug = isset($_GET['slug']) ? plaintext($_GET['slug']) : false;
        if ($slug) {
            parent::renderPageSlug($slug);
        } else {
            throw new CHttpException('404', t('cms', 'Oops! Page not found!'));
        }
    }

    public function actionView() {
        $id = Yii::app()->request->getParam("id");
        $connection = Yii::app()->db;
        $command = $connection->createCommand('SELECT * FROM {{page}} WHERE slug=:slug limit 1');
        $command->bindValue(':slug', "post", PDO::PARAM_STR);
        $page = $command->queryRow();
        $this->layout = 'main';
        $this->pageTitle = $page['title'];
        $this->description = $page['description'];
        $this->keywords = $page['keywords'];
       
        //depend on the layout of the page, use the corresponding file to render                  
        $this->renderPage('common.layouts.' . $page['layout'] . '.' . $page['display_type'], array('page' => $page));
    }

}
