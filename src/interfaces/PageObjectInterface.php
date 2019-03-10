<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 22.04.2018
 * Time: 11:13
 */

namespace floor12\pages\interfaces;

/**
 * Interface PageObjectInterface
 * @package floor12\pages
 */
interface PageObjectInterface
{
    const STATUS_ACTIVE = 0;
    const STATUS_DISABLED = 1;

    /** Метод, возвращающий url для просмотра конкретного объекта.
     * @return string
     */
    public function getUrl(): string;
}