<?php
/**
 * 旅行
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');

$rabbitActive = class_exists('RabbitTravel_Plugin');
$rabbitUrl = rtrim($this->options->siteUrl, '/') . '/rabbittravel';
?>

<div class="container">
    <main class="content travel-page content-area">

        <!-- 小喇叭通知 -->
        <div class="notice-section">
            <div class="notice-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                </svg>
            </div>
            <div class="notice-content">
                <p class="notice-text">欢迎来到我的旅行足迹页面！欢迎留言分享推荐，也许下一次出发，就能去到你推荐的宝藏地点！</p>
            </div>
        </div>

        <!-- 足迹地图 -->
        <div class="travel-content">
            <div class="travel-map">
                <?php if ($rabbitActive): ?>
                    <iframe src="<?php echo htmlspecialchars($rabbitUrl . '?embed=1'); ?>" title="旅行足迹地图" allowfullscreen></iframe>
                    <a class="travel-fullscreen-btn" href="<?php echo htmlspecialchars($rabbitUrl); ?>" target="_blank" rel="noopener" title="全屏查看" aria-label="全屏查看">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 3H5a2 2 0 0 0-2 2v3"></path>
                            <path d="M21 8V5a2 2 0 0 0-2-2h-3"></path>
                            <path d="M3 16v3a2 2 0 0 0 2 2h3"></path>
                            <path d="M16 21h3a2 2 0 0 0 2-2v-3"></path>
                        </svg>
                    </a>
                <?php else: ?>
                    <div class="travel-map-fallback">足迹地图插件未启用</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 评论区 -->
        <?php $this->need('comments.php'); ?>
    </main>
</div>

<script>
(function () {
    var mapBox = document.querySelector('.travel-map');
    if (!mapBox) return;
    var frame = mapBox.querySelector('iframe');
    // 在地图以外的地方点击时，关闭地图里的游记停留卡片
    document.addEventListener('click', function (e) {
        if (mapBox.contains(e.target)) return;
        try {
            if (frame && frame.contentWindow && frame.contentWindow.__rabbitHidePinned) {
                frame.contentWindow.__rabbitHidePinned();
            }
        } catch (err) {}
    });
})();
</script>

<?php $this->need('footer.php'); ?>
