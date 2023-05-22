<?php

namespace floor12\pages\components;


use DOMDocument;
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
                $this->owner->$attribute = $this->formatCode($this->owner->$attribute);
            }
    }

    public function formatCode($html)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->encoding = 'UTF-8';
        $dom->substituteEntities = true;
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        $dom->formatOutput = true;
        return str_replace('<!--?xml encoding="utf-8" ?-->', '', $dom->saveHTML());

    }
}