<?php

namespace floor12\pages\components;

use OpenAI;
use yii\web\BadRequestHttpException;

class GptHelper
{
    /**
     * @throws Exception
     */
    public static function MakeMetaTags($content)
    {
        $client = self::getClient();
        $request = 'You are SEO especialist. Make data for H1 (h1), Page title (title) and short page meta description (description, 160 letters max) using page language. Give answer in clean json format. Page content:' . strip_tags($content);
        $result = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $request],
            ],
        ]);

        return str_replace(['```json', '```'], '', $result['choices']['0']['message']['content']) ?? '';
    }

    public static function MakeContent($query, $lang = 'ru')
    {
        $client = self::getClient();
        $request = 'You are SEO especialist and copywriter. ' . $query . '. Give answer in clean and formatted html. Use only formatting tags (h1, h2, h3, p, ul, li, strong, em etc) and start from h1, dont make html, body, head, title tags. Use current language:' . $lang;

        $result = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $request],
            ],
        ]);
        if (!$result['choices']['0']['message']['content'] ?? null) {
            throw new BadRequestHttpException('Gpt response is empty. ' . print_r($result, true));
        }
        return $result['choices']['0']['message']['content'] ?? '';
    }

    /**
     * @return OpenAI\Client
     * @throws \Exception
     */
    public static function getClient(): OpenAI\Client
    {
        $yourApiKey = getenv('OPENAI_API_KEY');
        if (!$yourApiKey) {
            throw new \Exception('OpenAI API key not found');
        }
        $client = OpenAI::client($yourApiKey);
        return $client;
    }
}