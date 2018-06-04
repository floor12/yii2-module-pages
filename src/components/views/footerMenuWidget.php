<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 26.11.2016
 * Time: 18:11
 *
 * @var floor12\pages\Page $model
 * @var \floor12\pages\Page[] $subs
 *
 */


?>

<div class="footer-menu-block">

    <div class="footer-menu-header"><?= $model->title_menu ?></div>
    <ul class="footer-menu">
        <?= $subs ?>
    </ul>
</div>