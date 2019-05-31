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

    /**
     * @param int $number
     * @return string
     */
    public static function makeColor(Page $page)
    {
        $colors = [
            'white',
            'white',
            '#ebf1ff',
            '#f4ffeb',
            '#fdebff',
            '#e3f2fb',
        ];

        return $colors[mb_substr_count($page->url, '/')];
    }

    /**@inheritdoc
     * @return array
     */
    public function rules(): array
    {
        return [
            ['filter', 'string', 'max' => 255]
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
            'query' => Page::find()->andFilterWhere(['LIKE', 'title', $this->filter]),
            'pagination' => false
        ]);
    }
}