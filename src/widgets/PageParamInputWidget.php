<?php

namespace floor12\pages\widgets;

use floor12\pages\models\PageParam;
use yii\helpers\Html;

class PageParamInputWidget extends \yii\base\Widget
{
    public ?PageParam $model = null;

    public function run()
    {
        if ($this->model->modelClassName) {
            switch ($this->model->type) {
                case 'int':
                    return $this->model->description . Html::dropDownList("PageParam[{$this->model->name}]", $this->model->value, $this->model->modelClassName::dropdown(), ['class' => 'form-control', 'type' => 'number', 'prompt' => '']);
                    break;
                default:
                    return $this->model->description . Html::textInput("PageParam[{$this->model->name}]", $this->model->value, ['class' => 'form-control']);
            }
        } else {
            switch ($this->model->type) {
                case 'int':
                    return $this->model->description . Html::textInput("PageParam[{$this->model->name}]", $this->model->value, ['class' => 'form-control', 'type' => 'number']);
                    break;
                case 'string':
                    return $this->model->description . Html::textInput("PageParam[{$this->model->name}]", $this->model->value, ['class' => 'form-control']);
                    break;
                case 'bool':
                    return Html::tag('div', Html::checkbox("PageParam[{$this->model->name}]", $this->model->value) . ' ' . $this->model->description, ['style' => 'padding-top: 25px;']);
                    break;
                default:
                    return $this->model->description . Html::textInput("PageParam[{$this->model->name}]", $this->model->value, ['class' => 'form-control']);
            }
        }
    }
}