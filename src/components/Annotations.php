<?php

namespace floor12\pages\components;

use floor12\pages\models\PageParam;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionMethod;

class Annotations
{
    /**
     * @param string $className
     * @param string $methodName
     * @return PageParam[]
     */
    public static function read(string $className, string $methodName): array
    {
        $factory = DocBlockFactory::createInstance();
        try {
            $reflectionMethod = new ReflectionMethod($className, $methodName);
        } catch (\ReflectionException $e) {
            return [];
        }
        $docComment = $reflectionMethod->getDocComment();
        if (!$docComment)
            return [];
        $docBlock = $factory->create($docComment);
        $paramTags = $docBlock->getTagsByName('param');
        $params = [];
        foreach ($paramTags as $paramTag) {
            $param = new PageParam();
            $param->name = $paramTag->getVariableName();
            try {
                $param->type = $paramTag->getType()->getActualType();
            } catch (\Throwable $e) {
                $param->type = $paramTag->getType();
            }
            $param->description = (string)$paramTag->getDescription();
            if (str_contains($param->description, '#')) {
                list($param->description, $param->modelClassName) = explode('#', $param->description);
                $param->description = trim($param->description);
                $param->modelClassName = trim($param->modelClassName);
            }
            $params[] = $param;
        }
        return $params;
    }
}