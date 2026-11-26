<?php
/**
 * Thirteen - 侧边栏模板
 * 
 * @package Thirteen
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>

<div class="sidebar">
    <div class="widget">
        <h3><?php _e('最新文章'); ?></h3>
        <?php $this->widget('Widget_Contents_Post_Recent')->to($posts); ?>
        <?php if ($posts->have()): ?>
            <ul>
                <?php while ($posts->next()): ?>
                    <li><a href="<?php $posts->permalink(); ?>"><?php $posts->title(); ?></a></li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>
    
    <div class="widget">
        <h3><?php _e('分类'); ?></h3>
        <?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
        <?php if ($categories->have()): ?>
            <ul>
                <?php while ($categories->next()): ?>
                    <li><a href="<?php $categories->permalink(); ?>"><?php $categories->name(); ?></a></li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>
    
    <div class="widget">
        <h3><?php _e('标签'); ?></h3>
        <?php $this->widget('Widget_Metas_Tag_Cloud')->to($tags); ?>
        <?php if ($tags->have()): ?>
            <ul>
                <?php while ($tags->next()): ?>
                    <li><a href="<?php $tags->permalink(); ?>"><?php $tags->name(); ?></a></li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
