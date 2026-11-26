<?php
/**
 * Thirteen - 独立页面模板
 * 
 * @package Thirteen
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div class="container">
    <article class="page">
        <h1 class="page-title"><?php $this->title(); ?></h1>
        <div class="page-content">
            <?php $this->content(); ?>
        </div>
    </article>
</div>

<?php $this->need('footer.php'); ?>
