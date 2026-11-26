<?php
/**
 * 票根
 * 
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<main class="movie-tickets-page">
    <!-- 页面标题 -->
    <div class="tickets-header">
        <div class="tickets-intro">
            <p class="tickets-intro-text">记录每一段光影时光</p>
            <div class="tickets-intro-ornament">
                <span class="ornament-line"></span>
                <span class="ornament-dot"></span>
                <span class="ornament-line"></span>
            </div>
        </div>
    </div>

    <!-- 票根列表 -->
    <div class="tickets-content">
        <?php 
        if (class_exists('MovieTickets_Plugin')) {
            $page = $this->request->page ? intval($this->request->page) : 1;
            $pageSize = 8;
            $total = MovieTickets_Plugin::getTotal();
            $totalPage = ceil($total / $pageSize);
                
            $pattern = '<div class="ticket-card">
    <div class="ticket-top">
        <div class="ticket-title-wrapper">
            <div class="ticket-title-cn">{title_cn}</div>
            <div class="ticket-title-en">{title_en}</div>
        </div>
        <div class="ticket-cinema">{cinema}</div>
    </div>
    <div class="ticket-info">
        <div class="ticket-info-row ticket-info-hall">
            <span class="ticket-info-group">
                <span class="ticket-info-label">观影厅：</span>
                <span class="ticket-info-value">{hall}</span>
            </span>
            <span class="ticket-info-divider">座位</span>
            <span class="ticket-info-value">{seat}</span>
        </div>
        <div class="ticket-info-row">
            <span class="ticket-info-item">观影时间：{view_date}</span>
        </div>
        <div class="ticket-info-row">
            <div class="ticket-info-highlight">
                <div class="ticket-info-content">
                    <div class="ticket-info-col">
                        <span class="ticket-info-item">上映时间：{release_date}</span>
                    </div>
                    <div class="ticket-info-col">
                        <span class="ticket-info-item">类型：{genre}</span>
                    </div>
                </div>
            </div>
            <div class="ticket-qr">
                <div class="qr-placeholder"></div>
            </div>
        </div>
    </div>
    <div class="ticket-bottom">
        <span class="ticket-disclaimer">本电影票仅供个人电子收藏，无实际有效凭证</span>
    </div>
</div>';
            
            MovieTickets_Plugin::output($pattern, $pageSize, $page, 'created', 'DESC');
        } else {
            echo '<div class="no-tickets"><p>电影票根插件未启用</p></div>';
        }
        ?>
    </div>

    <!-- 分页 -->
    <?php if ($totalPage > 1): 
        $prevPage = $page > 1 ? $page - 1 : 1;
        $nextPage = $page < $totalPage ? $page + 1 : $totalPage;
    ?>
    <div class="pagination">
        <div class="pagination-wrapper">
            <a href="?page=<?php echo $prevPage; ?>" class="pagination-btn prev-btn" <?php if($page <= 1) echo 'style="pointer-events: none; opacity: 0.3;"'; ?>>
                上一页
            </a>
            
            <span class="pagination-info">
                <?php echo $page; ?> / <?php echo $totalPage; ?>
            </span>
            
            <a href="?page=<?php echo $nextPage; ?>" class="pagination-btn next-btn" <?php if($page >= $totalPage) echo 'style="pointer-events: none; opacity: 0.3;"'; ?>>
                下一页
            </a>
        </div>
    </div>
    <?php endif; ?>
</main>

<!-- ... existing code ... -->

<!-- 引入本地二维码库 -->
<!-- ... existing code ... -->
<script src="<?php $this->options->themeUrl('assets/js/qrcode.min.js'); ?>"></script>
<script>
(function() {
    var qrPlaceholders = document.querySelectorAll('.qr-placeholder');
    
    qrPlaceholders.forEach(function(placeholder, index) {
        var randomData = 'T-' + Date.now() + '-' + index + '-' + Math.random().toString(36).substr(2, 9);
        
        var container = document.createElement('div');
        container.style.width = '48px';
        container.style.height = '48px';
        
        new QRCode(container, {
            text: randomData,
            width: 48,
            height: 48,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.L
        });
        
        placeholder.innerHTML = '';
        placeholder.appendChild(container);
    });
})();
</script>
<!-- ... existing code ... -->
<!-- ... existing code ... -->

<?php $this->need('footer.php'); ?>
