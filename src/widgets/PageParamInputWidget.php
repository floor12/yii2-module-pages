<?php

namespace floor12\pages\widgets;

use floor12\pages\models\PageParam;
use kartik\select2\Select2;
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
                    return Html::tag('div', "<label>{$this->model->description}</label>" . Html::dropDownList("Page[page_params][{$this->model->name}]", $this->model->value, $this->model->modelClassName::dropdown(), ['class' => 'form-control', 'type' => 'number', 'prompt' => '']), ['class' => 'col-md-6 form-group']);
                case 'array':
                    return Html::tag('div', "<label>{$this->model->description}</label>" . Select2::widget([
                            'id' => 'page-page_params-' . $this->model->name,
                            'name' => "Page[page_params][{$this->model->name}]",
                            'value' => $this->model->value,
                            'data' => $this->model->modelClassName::dropdown(),
                            'options' => [
                                'multiple' => true,
                                'class' => 'form-control'
                            ],
                        ]), ['class' => 'col-md-12 form-group']);
                default:
                    return "<label>{$this->model->description}</label>" . Html::textInput("Page[page_params][{$this->model->name}]", $this->model->value, ['class' => 'form-control form-group']);
            }
        } else {
            switch ($this->model->type) {
                case 'int':
                    return Html::tag('div', "<label>{$this->model->description}</label>" . Html::textInput("Page[page_params][{$this->model->name}]", $this->model->value, ['class' => 'form-control', 'type' => 'number']), ['class' => 'col-md-6 form-group']);
                case 'bool':
                    return Html::tag('div', Html::tag('div', Html::checkbox("Page[page_params][{$this->model->name}]", $this->model->value) . ' ' . $this->model->description, ['style' => 'padding-top: 25px;']), ['class' => 'col-md-6 form-group']);
                default:
                    return Html::tag('div', "<label>{$this->model->description}</label>" . Html::textInput("Page[page_params][{$this->model->name}]", $this->model->value, ['class' => 'form-control']), ['class' => 'col-md-6 form-group']);
            }
        }
    }
}