<?php
/**
 * 归档
 * 
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div class="container">
    <main class="content archive-new-page content-area">
        
        <!-- 顶部导航 -->
        <div class="archive-header">
            <!-- 分类筛选 -->
            <div class="archive-categories">
                <a href="<?php $this->permalink(); ?>" class="archive-cat-item <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">全部</a>
                <?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
                <?php while($categories->next()): ?>
                    <a href="<?php $this->permalink(); ?>?category=<?php echo $categories->mid; ?>" class="archive-cat-item <?php echo (isset($_GET['category']) && $_GET['category'] == $categories->mid) ? 'active' : ''; ?>">
                        <?php echo $categories->name; ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
        
        <!-- 归档内容 -->
        <div class="archive-body">
            <?php
            // 获取分类筛选参数
            $selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;
            
            // 获取所有文章
            $allPosts = $this->widget('Widget_Contents_Post_Recent', 'pageSize=10000');
            $posts = array();
            
            while($allPosts->next()){
                // 如果有分类筛选，只获取该分类的文章
                if ($selectedCategory > 0) {
                    $categories = $allPosts->categories;
                    $hasCategory = false;
                    foreach ($categories as $cat) {
                        if ($cat['mid'] == $selectedCategory) {
                            $hasCategory = true;
                            break;
                        }
                    }
                    if (!$hasCategory) {
                        continue;
                    }
                }
                
                $posts[] = array(
                    'cid' => $allPosts->cid,
                    'title' => $allPosts->title,
                    'created' => $allPosts->created,
                    'permalink' => $allPosts->permalink
                );
            }
            
            // 按年份和月份分组
            $archives = array();
            foreach ($posts as $post) {
                $year = date('Y', $post['created']);
                $month = date('m', $post['created']);
                $monthDay = date('m/d', $post['created']);
                $monthName = date('m月', $post['created']);
                
                if (!isset($archives[$year])) {
                    $archives[$year] = array();
                }
                
                if (!isset($archives[$year][$month])) {
                    $archives[$year][$month] = array(
                        'name' => $monthName,
                        'posts' => array()
                    );
                }
                
                $archives[$year][$month]['posts'][] = array(
                    'cid' => $post['cid'],
                    'title' => $post['title'],
                    'date' => $monthDay,
                    'permalink' => $post['permalink']
                );
            }
            
            // 输出归档
            $firstYear = true;
            foreach ($archives as $year => $yearMonths): 
            ?>
            <div class="archive-year-section">
                <h2 class="archive-year-title"><?php echo $year; ?></h2>
                
                <?php foreach ($yearMonths as $month => $monthData): ?>
                <div class="archive-month-section">
                    <h3 class="archive-month-title"><?php echo $monthData['name']; ?></h3>
                    
                    <ul class="archive-post-list">
                        <?php foreach ($monthData['posts'] as $post): ?>
                        <li class="archive-post-item">
                            <span class="archive-post-date"><?php echo $post['date']; ?></span>
                            <a href="<?php echo $post['permalink']; ?>" class="archive-post-link">
                                <?php echo $post['title']; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
        
    </main>
</div>

<?php $this->need('footer.php'); ?>
