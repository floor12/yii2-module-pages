<?php

namespace floor12\pages\widgets;

use floor12\pages\models\PageParam;
use yii\base\Widget;
use yii\helpers\Html;

class PageParamInputWidget extends Widget
{
    public ?PageParam $model = null;

    public function run()
    {
        if ($this->model->modelClassName) {
            switch ($this->model->type) {
                case 'int':
                    return $this->model->description . Html::dropDownList("Page[page_params][{$this->model->name}]", $this->model->value, $this->model->modelClassName::dropdown(), ['class' => 'form-control', 'type' => 'number', 'prompt' => '']);
                case 'array':
                    return $this->model->description . Html::dropDownList("Page[page_params][{$this->model->name}]", $this->model->value, $this->model->modelClassName::dropdown(), ['class' => 'form-control', 'type' => 'number', 'prompt' => '', 'multiple' => true]);
                default:
                    return $this->model->description . Html::textInput("Page[page_params][{$this->model->name}]", $this->model->value, ['class' => 'form-control']);
            }
        } else {
            switch ($this->model->type) {
                case 'int':
                    return $this->model->description . Html::textInput("Page[page_params][{$this->model->name}]", $this->model->value, ['class' => 'form-control', 'type' => 'number']);
                case 'bool':
                    return Html::tag('div', Html::checkbox("Page[page_params][{$this->model->name}]", $this->model->value) . ' ' . $this->model->description, ['style' => 'padding-top: 25px;']);
                default:
                    return $this->model->description . Html::textInput("Page[page_params][{$this->model->name}]", $this->model->value, ['class' => 'form-control']);
            }
        }
    }
}