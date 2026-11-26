<?php
/**
 * 友链
 * 
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<main class="links-page">
    <!-- 友链列表 -->
    <div class="links-content">
            <?php 
            // 使用Links插件输出友链
            if (class_exists('Links_Plugin')) {
                // 自定义HTML模板，匹配主题风格
                $pattern = '<a href="{url}" title="{description}" target="_blank" rel="noopener noreferrer" class="link-card">
                    <div class="link-avatar">
                        <img src="{image}" alt="{name}" onerror="this.src=\'/usr/themes/Thirteen/assets/images/default-avatar.png\'">
                    </div>
                    <div class="link-info">
                        <h3 class="link-name">{name}</h3>
                        <p class="link-desc">{description}</p>
                    </div>
                </a>';
                
                // 输出所有友链（0表示不限制数量），mode='HTML' 表示返回HTML字符串
                echo Links_Plugin::output($pattern, 0, null, 80, 'HTML');
            } else {
                echo '<div class="no-links"><p>友情链接插件未启用</p></div>';
            }
            ?>
        </div>

    <!-- 友链互换 -->
    <div class="links-info">
            
            <div class="links-info-content">
                <h3 class="links-site-info-title">友链申请</h3>
                <p class="links-info-text">
                    如果你也是 <strong>博客爱好者</strong>，<br>
                    并且还在 <strong>认真</strong> 运营博客，<br>
                    可在 <a href="#comments">留言板</a> 或者通过邮件 <code>959565159@qq.com</code> 告知你的博客 <strong>网址</strong>、<strong>站点名</strong> 以及 <strong>站点简介</strong>，<br>
                    内容合适 <strong>我会将其添加在友情链接的页面中</strong>。
                </p>
            </div>

            <div class="links-site-info">
                <h3 class="links-site-info-title">站点信息</h3>
                <div class="links-site-info-item">
                    <span class="links-site-info-label">网站名称：</span>
                    <span class="links-site-info-value">达达兔Note</span>
                </div>
                <div class="links-site-info-item">
                    <span class="links-site-info-label">网站链接：</span>
                    <span class="links-site-info-value">https://www.jinote.cn</span>
                </div>
                <div class="links-site-info-item">
                    <span class="links-site-info-label">网站描述：</span>
                    <span class="links-site-info-value">Satisfaction is the beginning of failure.</span>
                </div>
                <div class="links-site-info-item">
                    <span class="links-site-info-label">头像链接：</span>
                    <span class="links-site-info-value">https://www.jinote.cn/images/tx.webp</span>
                </div>
            </div>
        </div>

    <!-- 评论区 -->
    <?php $this->need('comments.php'); ?>
</main>

<?php $this->need('footer.php'); ?>
