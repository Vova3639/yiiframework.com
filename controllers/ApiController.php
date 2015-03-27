<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ApiController extends Controller
{
    public function actionIndex($version)
    {
        return $this->actionView($version, 'index');
    }

    public function actionView($version, $section)
    {
        $versions = Yii::$app->params['api.versions'];
        if (!in_array($version, $versions)) {
            // TODO make nicer error page
            throw new NotFoundHttpException('The requested version was not found.');
        }

        $title = '';
        if ($version[0] === '1') {
            $file = Yii::getAlias("@app/data/api-$version/api/$section.html");
            $view = 'view11';
        } else {
            $file = Yii::getAlias("@app/data/api-$version/$section.html");
            $view = 'view2x';
            $titles = require(Yii::getAlias("@app/data/api-$version/titles.php"));
            if (isset($titles[$section . '.html'])) {
                $title = $titles[$section . '.html'];
            }
        }
        if (!preg_match('/^[\w\-]+$/', $section) || !is_file($file)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }

        return $this->render($view, [
            'content' => file_get_contents($file),
            'section' => $section,
            'versions' => array_keys(Yii::$app->params['guide.versions']),
            'version' => $version,
            'title' => $title,
        ]);
    }
}
