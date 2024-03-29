<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 19.06.2018
 * Time: 13:16
 */

namespace floor12\pages\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;

class PageFilter extends Model
{
    public $filter;
    public $lang;

    public function init()
    {
        $this->lang = \Yii::$app->language;
        parent::init();
    }

    /**
     * @param int $number
     * @return string
     */
    public static function makeColor(Page $page)
    {
        $colors = [
            'white',
            '#e3f2fb',
            '#ebf1ff',
            '#f4ffeb',
            '#fdebff',
            '#e3f2fb',
        ];
        return $colors[mb_substr_count($page->path, '/')];
    }

    /**@inheritdoc
     * @return array
     */
    public function rules(): array
    {
        return [
            [['filter', 'lang'], 'string', 'max' => 255]
        ];
    }

    /**
     * @return ActiveDataProvider
     * @throws BadRequestHttpException
     */
    public function dataProvider(): ActiveDataProvider
    {
        if (!$this->validate())
            throw new BadRequestHttpException('Model validation error');

        return new ActiveDataProvider([
            'query' => Page::find()
                ->andWhere(['lang' => $this->lang])
                ->andFilterWhere(['LIKE', 'title', $this->filter])
                ->orderBy('norder'),
            'sort' => false,
            'pagination' => false
        ]);
    }

    /**
     * @return array
     */
    public function getLangs()
    {
        return Page::find()
            ->select('lang')
            ->indexBy('lang')
            ->distinct()->column();
    }

    public function getIndexActions()
    {
        return \Yii::$app->getModule('pages')->actionsIndex;
    }

    public function getViewActions()
    {
        return \Yii::$app->getModule('pages')->actionsView;
    }


}