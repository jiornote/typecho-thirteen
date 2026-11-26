<?php
/**
 * Thirteen - 一个简洁的Typecho主题
 * 
 * @package Thirteen
 * @author Your Name
 * @version 1.0.0
 * @link http://example.com
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php if ($this->is('index')): ?><?php $this->options->title(); ?> - <?php $this->options->description(); ?><?php else: ?><?php $this->archiveTitle(array(
        'category' => _t('分类 %s 下的文章'),
        'search' => _t('包含关键字 %s 的文章'),
        'tag' => _t('标签 %s 下的文章'),
        'author' => _t('%s 发布的文章')
    ), '', ' - '); ?><?php $this->options->title(); ?><?php endif; ?></title>

    <?php
    // SEO 元信息：任何异常都不允许影响页面渲染
    ob_start();
    try {
        thirteen_seo_meta($this);
    } catch (Throwable $seoError) {
        ob_get_clean();
    }
    ?>
    
    <script async src="https://zz.bdstatic.com/linksubmit/push.js"></script>

    <!-- 网站图标 -->
    <link rel="icon" href="<?php $this->options->themeUrl('favicon.ico'); ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php $this->options->themeUrl('favicon.ico'); ?>" type="image/x-icon">
    
    <!-- 引入CSS -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/style.css'); ?>">
    
    <!-- FontAwesome 图标库 - 又拍云 CDN -->
    <link rel="stylesheet" href="https://lib.baomitu.com/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Highlight.js 代码高亮 -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/highlight-dark.min.css'); ?>">
    
    <?php $this->header(); ?>
</head>
<body>
    <header class="site-header">
        <div class="header-container">
            <div class="header-left">
                <a href="<?php $this->options->siteUrl(); ?>" class="site-logo">
                    <?php if ($this->options->logoUrl): ?>
                        <img src="<?php $this->options->logoUrl(); ?>" alt="<?php $this->options->title(); ?>">
                    <?php else: ?>
                        <span class="logo-text"><?php $this->options->title(); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            
            <div class="header-right">
                <button class="header-btn" id="navToggle" title="导航菜单">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                </button>
                <div class="divider-wrapper">
                    <div class="divider"></div>
                </div>
                <button class="header-btn search-btn" id="searchBtn" title="搜索 (Ctrl+K)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <span class="search-shortcut">Ctrl+K</span>
                </button>
            </div>
        </div>
        
        <!-- 导航菜单面板 -->
        <div class="nav-panel" id="navPanel">
            <div class="nav-panel-container">
                <nav class="nav-menu">
                    <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                    <?php while($pages->next()): ?>
                        <a href="<?php $pages->permalink(); ?>" class="nav-item">
                            <?php 
                            $slug = $pages->slug;
                            $permalink = $pages->permalink;
                            $parsedUrl = parse_url($permalink);
                            $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
                            $path = str_replace('.html', '', basename($path));
                            $englishTitle = $path ? ucfirst($path) : '';
                            
                            if ($englishTitle): 
                            ?>
                                <span class="nav-item-title"><?php $pages->title(); ?></span>
                                <span class="nav-item-divider">|</span>
                                <span class="nav-item-english"><?php echo $englishTitle; ?></span>
                            <?php else: ?>
                                <span class="nav-item-title"><?php $pages->title(); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endwhile; ?>
                </nav>
            </div>
        </div>
        
        <!-- 搜索面板 -->
        <div class="search-overlay" id="searchOverlay">
            <div class="search-container">
                <div class="search-input-wrapper">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" class="search-input" id="searchInput" placeholder="Type to Search" autocomplete="off">
                    <button class="search-close" id="searchClose" title="关闭 (Esc)">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- 订阅面板 -->
        <div class="subscribe-overlay" id="subscribeOverlay">
            <div class="subscribe-container">
                <div class="subscribe-box">
                    <button class="subscribe-close" id="subscribeClose" title="关闭 (Esc)">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    
                    <h2 class="subscribe-title">订阅 <?php $this->options->title(); ?> 的新内容</h2>
                    
                    <div class="subscribe-description">
                        <p>若有新文章发布，将其发送到你的收件箱📪。</p>
                        <p>没有广告，可随时自主退订。</p>
                    </div>
                    
                    <form id="subscribeForm" class="subscribe-form" data-action-url="<?php echo Typecho_Common::url('action/subscribe', Helper::options()->index); ?>">
                        <input type="email" 
                               id="subscribeEmail" 
                               name="email" 
                               class="subscribe-input" 
                               placeholder="你的邮箱地址" 
                               required>
                        <button type="submit" class="subscribe-submit-btn">订阅</button>
                    </form>
                    
                    <div id="subscribeMessage" class="subscribe-message"></div>
                    
                    <div class="subscribe-note">
                        <small>确认订阅后会立即给您发送一封确认邮件，若未收到，请检查您的垃圾邮件收件箱并将发件地址加入白名单中。</small>
                    </div>
                    
                    <div class="subscribe-rss">
                        <p>若喜欢RSS订阅的方式，也完全支持：<a href="<?php $this->options->siteUrl(); ?>feed" target="_blank">RSS</a></p>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- 公告栏 -->
    <div class="announcement-bar">
        <div class="announcement-content">
            <svg class="announcement-icon" width="18" height="18" viewBox="0 0 20 20" fill="none">
                <path d="M12.4 12.77l-1.81 4.99a.63.63 0 0 1-1.18 0l-1.8-4.99a.63.63 0 0 0-.38-.37l-4.99-1.81a.62.62 0 0 1 0-1.18l4.99-1.8a.63.63 0 0 0 .37-.38l1.81-4.99a.63.63 0 0 1 1.18 0l1.8 4.99a.63.63 0 0 0 .38.37l4.99 1.81a.62.62 0 0 1 0 1.18l-4.99 1.8a.63.63 0 0 0-.38.38z" fill="#FFC017"/>
            </svg>
            <span>获取最新博主动态，<a href="#" id="openSubscribe">邮件订阅</a> 或 <a href="<?php $this->options->siteUrl(); ?>feed" target="_blank">RSS订阅</a> 本博客。欢迎留言~</span>
        </div>
    </div>
    
    <main class="site-main">
