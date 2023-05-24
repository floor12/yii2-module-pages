<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 2019-03-10
 * Time: 10:27
 */

namespace floor12\pages\components;


class PageControlWidget extends \yii\base\Widget
{
    /**
     * @var \floor12\pages\models\Page
     */
    public $model;

    public function run()
    {
        return $this->render('pageControlWidget', ['model' => $this->model]);
    }
}