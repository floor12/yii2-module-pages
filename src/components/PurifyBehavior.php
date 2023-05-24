<?php

namespace floor12\pages\components;


use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\HtmlPurifier;

class PurifyBehavior extends Behavior
{
    public $attributes = [];

    public $config = null;

    public function init()
    {
        parent::init();
        $this->config = function ($conf) {
            $conf->set('HTML.AllowedElements', ['p', 'div', 'a', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'table', 'thead', 'tbody', 'tr', 'th', 'td', 'ul', 'ol', 'li', 'b', 'i', 'strike', 'img', 'hr']);
            $conf->set('HTML.AllowedAttributes', 'src, height, width, alt, align, class, target, href, target');
            $conf->set('Filter.ExtractStyleBlocks.Escaping', false);
        };
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    public function beforeValidate()
    {
        if (!isset($this->owner->use_purifier) || $this->owner->use_purifier == true)
            foreach ($this->attributes as $attribute) {
                $this->owner->$attribute = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $this->owner->$attribute);
                $this->owner->$attribute = str_replace(['class="MsoNormal"', "\n", "\r", '&nbsp;'], " ", $this->owner->$attribute);
                $pattern = '/<[^\/>]*>(\s*| )*<\/[^>]*>/';
                $this->owner->$attribute = preg_replace($pattern, '', $this->owner->$attribute);
                $this->owner->$attribute = HtmlPurifier::process($this->owner->$attribute, $this->config);
                try {
                    $this->owner->$attribute = $this->formatCode($this->owner->$attribute);
                } catch (\Throwable $e) {
                    \Yii::$app->errorHandler->logException($e);
                }
            }
    }

    public function formatCode($html)
    {
        $config = array(
            'indent' => true,
            'output-xhtml' => true,
            'wrap' => 200
        );
        $tidy = new \tidy;
        $tidy->parseString($html, $config, 'utf8');
        $tidy->cleanRepair();
        preg_match('/<body[^>]*>(.*?)<\/body>/is', (string)$tidy->html(), $matches);
        return trim(preg_replace('/^\s{4}/m', '', $matches[1]));
    }
}