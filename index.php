<?php
/**
 * Thirteen
 * 主题采用干净利落的视觉设计，注重内容呈现与阅读体验，让每一次访问都成为愉悦的浏览之旅。
 * 
 * @package Thirteen
 * @author Jinote
 * @version 1.0.0
 * @link https://www.jinote.cn
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<!-- 首页Hero板块 -->
<section class="hero-section">
    <div class="hero-container">
        <!-- 左侧统计面板 -->
        <div class="hero-stats">
            
            <!-- 统计卡片 -->
            <div class="stats-grid">
                <?php
                // 获取数据库对象
                $db = Typecho_Db::get();
                $prefix = $db->getPrefix();
                
                // 1. 创站时间（年）
                $firstPost = $db->fetchRow($db->select('created')
                    ->from('table.contents')
                    ->where('type = ?', 'post')
                    ->where('status = ?', 'publish')
                    ->order('created', Typecho_Db::SORT_ASC)
                    ->limit(1));
                
                $years = 0;
                if ($firstPost) {
                    $firstTime = $firstPost['created'];
                    $currentTime = time();
                    $years = round(($currentTime - $firstTime) / (365 * 24 * 3600), 1);
                }
                
                // 2. 文章数量
                $posts = $db->fetchRow($db->select('COUNT(*) AS count')
                    ->from('table.contents')
                    ->where('type = ?', 'post')
                    ->where('status = ?', 'publish'));
                $postCount = isset($posts['count']) ? intval($posts['count']) : 0;
                
                // 3. 浏览数量（使用 SimpleStats 插件）
                $views = 0;
                try {
                    if (class_exists('SimpleStats_Plugin')) {
                        $views = SimpleStats_Plugin::getTotalViews();
                    } else {
                        // 如果没有安装插件，尝试从数据库获取
                        if ($db->fetchRow($db->select('TABLE_NAME')
                            ->from('information_schema.TABLES')
                            ->where('TABLE_NAME = ?', $prefix . 'simple_stats'))) {
                            $viewsResult = $db->fetchRow($db->select('SUM(VIEWS) AS total')
                                ->from('table.simple_stats'));
                            $views = isset($viewsResult['total']) ? intval($viewsResult['total']) : 0;
                        }
                    }
                } catch (Exception $e) {
                    $views = 0;
                }
                
                // 4. 文字总数
                $words = $db->fetchRow($db->select('text')
                    ->from('table.contents')
                    ->where('type = ?', 'post')
                    ->where('status = ?', 'publish'));
                
                $totalWords = 0;
                if ($words) {
                    // 获取所有文章内容并计算字数
                    $allPosts = $db->fetchAll($db->select('text')
                        ->from('table.contents')
                        ->where('type = ?', 'post')
                        ->where('status = ?', 'publish'));
                    
                    foreach ($allPosts as $post) {
                        $textContent = strip_tags($post['text']);
                        $totalWords += mb_strlen(preg_replace('/\s+/', '', $textContent), 'UTF-8');
                    }
                }
                
                // 5. 分类数量
                $categories = $db->fetchRow($db->select('COUNT(*) AS count')
                    ->from('table.metas')
                    ->where('type = ?', 'category'));
                $categoryCount = isset($categories['count']) ? intval($categories['count']) : 0;
                
                // 6. 访客数量（使用 SimpleStats 插件）
                $visitors = 0;
                try {
                    if (class_exists('SimpleStats_Plugin')) {
                        $visitors = SimpleStats_Plugin::getTotalVisitors();
                    } else {
                        // 如果没有安装插件，尝试从数据库获取
                        if ($db->fetchRow($db->select('TABLE_NAME')
                            ->from('information_schema.TABLES')
                            ->where('TABLE_NAME = ?', $prefix . 'simple_stats'))) {
                            $visitorsResult = $db->fetchRow($db->select('SUM(visitors) AS total')
                                ->from('table.simple_stats'));
                            $visitors = isset($visitorsResult['total']) ? intval($visitorsResult['total']) : 0;
                        }
                    }
                } catch (Exception $e) {
                    $visitors = 0;
                }
                ?>
                <div class="stats-item">
                    <div class="stats-number"><?php echo $years; ?></div>
                    <div class="stats-label">YEARS</div>
                </div>
                <div class="stats-item">
                    <div class="stats-number"><?php echo $postCount; ?></div>
                    <div class="stats-label">POSTS</div>
                </div>
                <div class="stats-item">
                    <div class="stats-number"><?php echo $views; ?></div>
                    <div class="stats-label">VIEWS</div>
                </div>
                <div class="stats-item">
                    <div class="stats-number"><?php echo $totalWords >= 10000 ? round($totalWords / 10000, 1) . '万' : $totalWords; ?></div>
                    <div class="stats-label">WORDS</div>
                </div>
                <div class="stats-item">
                    <div class="stats-number"><?php echo $categoryCount; ?></div>
                    <div class="stats-label">CATEGORIES</div>
                </div>
                <div class="stats-item">
                    <div class="stats-number"><?php echo $visitors; ?></div>
                    <div class="stats-label">VISITORS</div>
                </div>
            </div>
            
            <!-- GitHub热力图样式 -->
            <div class="heatmap-wrapper">
                <?php
                // 热力图数据：3行 x 15列 = 45个月
                $currentYear = 2026;
                $currentMonth = 12;
                $totalMonths = 45;
                $monthsPerRow = 15;
                $rows = 3;
                
                // 查询每个月的文章数量
                $db = Typecho_Db::get();
                $articleCounts = array();
                
                for ($i = 0; $i < $totalMonths; $i++) {
                    // 计算当前月份（从后往前推）
                    $monthOffset = $totalMonths - 1 - $i;
                    $year = $currentYear;
                    $month = $currentMonth - $monthOffset;
                    
                    while ($month <= 0) {
                        $year--;
                        $month += 12;
                    }
                    
                    $monthKey = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
                    
                    // 查询该月的文章数量
                    $count = $db->fetchRow($db->select('COUNT(*) AS count')
                        ->from('table.contents')
                        ->where('type = ?', 'post')
                        ->where('status = ?', 'publish')
                        ->where('created >= ?', mktime(0, 0, 0, $month, 1, $year))
                        ->where('created < ?', mktime(0, 0, 0, $month + 1, 1, $year)));
                    
                    $articleCounts[$monthKey] = isset($count['count']) ? intval($count['count']) : 0;
                }
                
                // 找到最大文章数用于计算颜色深度
                $maxCount = max($articleCounts);
                if ($maxCount == 0) $maxCount = 1;
                
                // 生成热力图
                for ($row = 0; $row < $rows; $row++):
                ?>
                <div class="heatmap-row">
                    <?php for ($col = 0; $col < $monthsPerRow; $col++):
                        $index = $row * $monthsPerRow + $col;
                        $monthOffset = $totalMonths - 1 - $index;
                        $year = $currentYear;
                        $month = $currentMonth - $monthOffset;
                        
                        while ($month <= 0) {
                            $year--;
                            $month += 12;
                        }
                        
                        $monthKey = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
                        $count = $articleCounts[$monthKey];
                        
                        // 计算颜色等级 (0-4)
                        $level = 0;
                        if ($count > 0) {
                            $level = min(4, ceil(($count / $maxCount) * 4));
                        }
                    ?>
                        <div class="heatmap-cell level-<?php echo $level; ?>" title="<?php echo $monthKey; ?> 文章 <?php echo $count; ?>"></div>
                    <?php endfor; ?>
                </div>
                <?php endfor; ?>
            </div>
        </div>
        
        <!-- 右侧轮播Banner -->
        <div class="hero-carousel" id="heroCarousel">
            <?php
            // 获取置顶文章，最多3篇
            $sticky = $this->options->sticky;
            if($sticky && ($this->is('index') || $this->is('front'))){
                // 处理CID，支持逗号、空格、中文逗号分隔
                $sticky = strtr($sticky, ' ', ',');
                $sticky = strtr($sticky, '，', ',');
                $sticky_cids = explode(',', $sticky);
                $sticky_cids = array_filter(array_map('trim', $sticky_cids));
                        
                // 查询所有置顶文章
                $sticky_posts = array();
                foreach($sticky_cids as $cid){
                    if(empty($cid)) continue;
                            
                    // 获取单篇文章
                    $tempWidget = $this->widget('Widget_Archive@sticky-' . $cid, 'type=post', 'cid=' . $cid);
                    if($tempWidget->have()){
                        // 获取分类名称
                        $category = '';
                        $categories = $tempWidget->categories;
                        if(is_array($categories) && count($categories) > 0){
                            $category = $categories[0]['name'];
                        }
                                
                        // 获取作者名称
                        $authorName = '';
                        if(isset($tempWidget->author)){
                            $authorObj = $tempWidget->author;
                            if(is_object($authorObj) && isset($authorObj->screenName)){
                                $authorName = $authorObj->screenName;
                            } else {
                                $authorName = strval($authorObj);
                            }
                        }
                                
                        // 获取 Banner 图片 - 优先使用自定义字段
                        $bannerImage = '';
                        $customBanner = isset($tempWidget->fields->stickyBanner) ? $tempWidget->fields->stickyBanner : '';
                                
                        if(is_array($customBanner) && !empty($customBanner)){
                            $bannerImage = trim($customBanner[0] ?? '');
                        } elseif(is_string($customBanner) && !empty($customBanner)){
                            $bannerImage = trim($customBanner);
                        }
                                
                        $sticky_posts[] = array(
                            'title' => $tempWidget->title,
                            'permalink' => $tempWidget->permalink,
                            'category' => $category,
                            'author' => $authorName,
                            'image' => $bannerImage
                        );
                    }
                            
                    if(count($sticky_posts) >= 3) break;
                }
                        
                // 生成轮播HTML
                if(count($sticky_posts) > 0):
            ?>
            <div class="carousel-wrapper">
                <?php foreach($sticky_posts as $index => $post): ?>
                <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
    <a href="<?php echo $post['permalink']; ?>" class="carousel-item-link"></a>
    <?php if(!empty($post['image'])): ?>
    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo $post['title']; ?>">
    <?php endif; ?>
    <div class="carousel-gradient"></div>
    <div class="carousel-info">
        <h3 class="carousel-title"><?php echo $post['title']; ?></h3>
    </div>
</div>
                </div>
                <?php endforeach; ?>
            </div>
                    
            <!-- 指示器 -->
            <?php if (count($sticky_posts) > 1): ?>
            <div class="carousel-dots">
                <?php foreach($sticky_posts as $index => $post): ?>
                <span class="carousel-dot <?php echo ($index === 0) ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php endif; 
            }
            ?>
        </div>
    </div>
</section>




<!-- 文章列表 -->
<div class="container" id="content">
    <div class="article-list">
        <?php while($this->next()): ?>
            <?php 
            $thumbnail = $this->fields->thumbnail;
            if ($thumbnail && preg_match('/!\[.*?\]\((.*?)\)/', $thumbnail, $matches)) {
                $thumbnail = $matches[1];
            }
            $hasThumbnail = !empty($thumbnail);
            ?>
            <article class="article-item <?php echo $hasThumbnail ? 'has-thumbnail' : ''; ?>">
                <div class="article-content">
                    <h2 class="article-title">
                        <a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                    </h2>
                    <div class="article-excerpt">
                        <?php 
                        $content = $this->content;
                        $content = strip_tags($content);
                        $content = preg_replace('/^#{1,6}\s*/m', '', $content);
                        $content = preg_replace('/\*\*(.+?)\*\*/', '$1', $content);
                        $content = preg_replace('/\*(.+?)\*/', '$1', $content);
                        $content = preg_replace('/~~(.+?)~~/', '$1', $content);
                        $content = preg_replace('/`(.+?)`/', '$1', $content);
                        $content = preg_replace('/!\[.*?\]\(.*?\)/s', '', $content);
                        $content = preg_replace('/^>\s*/m', '', $content);
                        $content = preg_replace('/^[-*]\s+/m', '', $content);
                        $content = preg_replace('/^\d+\.\s+/m', '', $content);
                        $content = preg_replace('/^```[\s\S]*?```/m', '', $content);
                        $content = preg_replace('/\|.*?\|/m', '', $content);
                        $content = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $content);
                        $content = preg_replace('/^(\s*[-*]){3,}\s*$/m', '', $content);
                        $content = trim(preg_replace('/\s+/', ' ', $content));
                        if ($hasThumbnail) {
                            // 有缩略图显示1行
                            if (mb_strlen($content, 'UTF-8') > 60) {
                                $content = mb_substr($content, 0, 60, 'UTF-8') . '...';
                            }
                        } else {
                            // 没有缩略图显示2行
                            if (mb_strlen($content, 'UTF-8') > 120) {
                                $content = mb_substr($content, 0, 120, 'UTF-8') . '...';
                            }
                        }
                        echo $content;
                        ?>
                    </div>
                    <div class="article-meta">
                        <span class="article-time"><?php $this->date('M j, Y'); ?></span>
                        <span class="article-meta-divider">/</span>
                        <span class="article-category"><?php $this->category(', ', false); ?></span>
                    </div>
                </div>
                <?php if ($hasThumbnail): ?>
                <a href="<?php $this->permalink() ?>" class="article-thumbnail">
                    <img src="<?php echo htmlspecialchars($thumbnail); ?>" alt="<?php $this->title() ?>">
                </a>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    </div>
    
    <!-- 分页 -->
    <div class="pagination">
        <?php 
        $total = isset($this->countSql) ? $this->getTotal() : 0;
        $currentPage = $this->_currentPage;
        $totalPage = ceil($total / $this->parameter->pageSize);
        
        if ($totalPage > 1):
            $prevPage = $currentPage > 1 ? $currentPage - 1 : 1;
            $nextPage = $currentPage < $totalPage ? $currentPage + 1 : $totalPage;
            $baseUrl = $this->archiveUrl;
        ?>
        <div class="pagination-wrapper">
            <a href="<?php echo $baseUrl . ($prevPage > 1 ? '/page/' . $prevPage : ''); ?>" class="pagination-btn prev-btn" <?php if($currentPage <= 1) echo 'style="pointer-events: none; opacity: 0.3;"'; ?>>
                上一页
            </a>
            
            <span class="pagination-info">
                <?php echo $currentPage; ?> / <?php echo $totalPage; ?>
            </span>
            
            <a href="<?php echo $baseUrl . '/page/' . $nextPage; ?>" class="pagination-btn next-btn" <?php if($currentPage >= $totalPage) echo 'style="pointer-events: none; opacity: 0.3;"'; ?>>
                下一页
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- 预加载 Memos 头像 -->
<link rel="prefetch" href="https://pix.veryjack.com/i/2023/04/04/fsxnkv.webp" as="image">

<?php $this->need('footer.php'); ?>
