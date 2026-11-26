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

// 主题设置
function themeConfig($form) {
    // Logo设置
    $logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', NULL, NULL, _t('站点 LOGO 地址'), _t('在这里填入一个图片 URL 地址, 以在网站标题前加上一个 LOGO'));
    $form->addInput($logoUrl);
    
    // 站点描述
    $siteDescription = new Typecho_Widget_Helper_Form_Element_Text('siteDescription', NULL, NULL, _t('站点描述'), _t('在这里填入站点描述，会显示在首页'));
    $form->addInput($siteDescription);
    
    // 一言
    $authorBio = new Typecho_Widget_Helper_Form_Element_Text('authorBio', NULL, NULL, _t('一言'), _t('在这里填入一言内容，会显示在首页统计面板'));
    $form->addInput($authorBio);
    
    // 置顶文章CID
    $sticky = new Typecho_Widget_Helper_Form_Element_Text('sticky', NULL, NULL, _t('置顶文章的 cid'), _t('填写置顶文章的 cid（文章ID），多个用逗号或空格隔开。例如：1,2,3'));
    $form->addInput($sticky);
    
    // 备案号
    $icp = new Typecho_Widget_Helper_Form_Element_Text('icp', NULL, NULL, _t('备案号'), _t('在这里填入备案号，会显示在页脚'));
    $form->addInput($icp);
}

// 自定义字段
function themeFields($layout) {
    // 置顶文章 Banner 图片
    $stickyBanner = new Typecho_Widget_Helper_Form_Element_Text('stickyBanner', NULL, NULL, _t('置顶文章 Banner 图片'), _t('如果该文章被设置为置顶，将使用此图片作为轮播 Banner 的背景图。填入图片 URL 地址。'));
    $layout->addItem($stickyBanner);
    
    // 文章缩略图
    $thumbnail = new Typecho_Widget_Helper_Form_Element_Text('thumbnail', NULL, NULL, _t('文章缩略图'), _t('填入文章缩略图的 URL 地址，会显示在文章列表中。'));
    $layout->addItem($thumbnail);
}

/**
 * 正文转纯文本摘要（用于 description）
 * $archive->content 取到的是 Markdown 原文，需先清语法再截断
 */
function thirteen_seo_excerpt($raw, $limit = 160) {
    $text = (string) $raw;
    $text = preg_replace('/```.*?```/s', ' ', $text);                    // 代码块
    $text = preg_replace('/`([^`]*)`/', '$1', $text);                    // 行内代码
    $text = preg_replace('/<!--.*?-->/s', ' ', $text);                   // HTML 注释
    $text = preg_replace('/!\[[^\]]*\]\([^)]*\)/', '', $text);           // 图片
    $text = preg_replace('/\[([^\]:]*)[^\]]*\]\([^)]*\)/', '$1', $text); // 链接
    $text = preg_replace('/\[[^\]]*\]/', '', $text);                     // 残留短代码
    $text = preg_replace('/<(script|style)[^>]*>.*?<\/\1>/is', ' ', $text);
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/^\s*(?:#{1,6}|>|[-*+]|\d+\.)\s*/m', '', $text); // 行首标记
    $text = str_replace(array('**', '__', '~~'), ' ', $text);
    $text = trim(preg_replace('/\s+/', ' ', $text));
    return mb_substr($text, 0, $limit);
}

/**
 * 当前页的规范地址：分页与查询参数统一回指第一页，避免权重分散
 * 只用 $_SERVER 与 options 属性，不读 Widget 内部对象
 */
function thirteen_seo_canonical() {
    $path = isset($_SERVER['REQUEST_URI'])
        ? (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        : '/';
    $path = preg_replace('#/page/\d+#', '', $path);

    return rtrim((string) Helper::options()->siteUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * 输出 SEO 元信息：description / keywords / canonical / Open Graph
 */
function thirteen_seo_meta($archive) {
    $siteName = (string) Helper::options()->title;
    $description = '';
    $keywords = '';
    $ogType = 'website';

    if ($archive->is('index')) {
        $description = trim((string) (Helper::options()->siteDescription
            ? Helper::options()->siteDescription : Helper::options()->description));
    } elseif ($archive->is('post') || $archive->is('page')) {
        $ogType = 'article';
        $html = (string) $archive->content;
        if ($html === '') {
            $html = (string) $archive->text;
        }
        $description = thirteen_seo_excerpt($html);

        ob_start();
        $archive->category(',', false);
        $cats = (string) ob_get_clean();
        ob_start();
        $archive->tags(',', false);
        $tags = (string) ob_get_clean();
        $keywords = trim($cats . ',' . $tags, ', ');
    } elseif ($archive->is('category') || $archive->is('tag') || $archive->is('search')) {
        ob_start();
        $archive->archiveTitle(array(
            'category' => '%s',
            'tag' => '%s',
            'search' => '%s'
        ), '', '');
        $name = trim((string) ob_get_clean());
        if ($name !== '') {
            $description = $siteName . ' - ' . $name . ' 相关的文章列表';
            $keywords = $name;
        }
    }

    if ($description === '') {
        $description = trim((string) Helper::options()->description);
    }

    $canonical = thirteen_seo_canonical();
    $e = function ($s) {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    };
?>
    <meta name="description" content="<?php echo $e($description); ?>">
<?php if ($keywords !== ''): ?>
    <meta name="keywords" content="<?php echo $e($keywords); ?>">
<?php endif; ?>
    <link rel="canonical" href="<?php echo $e($canonical); ?>">
    <meta property="og:type" content="<?php echo $ogType; ?>">
    <meta property="og:site_name" content="<?php echo $e($siteName); ?>">
    <meta property="og:url" content="<?php echo $e($canonical); ?>">
    <meta property="og:title" content="<?php echo $e($archive->is('single') ? (string) $archive->title : $siteName); ?>">
    <meta property="og:description" content="<?php echo $e($description); ?>">
<?php if ($archive->is('post') && $archive->created): ?>
    <meta property="article:published_time" content="<?php echo date('c', (int) $archive->created); ?>">
<?php endif; ?>
    <meta property="og:image" content="<?php echo $e(Helper::options()->themeUrl('screenshot.png')); ?>">
<?php
}

/**
 * 输出 sitemap.xml（文章 + 独立页面 + 分类），供百度站长平台提交
 */
function thirteen_sitemap($archive) {
    $siteUrl = rtrim(Helper::options()->siteUrl, '/');
    $today = date('Y-m-d');
    $urls = array(
        array('loc' => $siteUrl . '/', 'lastmod' => $today, 'priority' => '1.0')
    );

    $posts = $archive->widget('Widget_Contents_Post_Recent', 'pageSize=10000');
    while ($posts->next()) {
        if (!empty($posts->password)) {
            continue;
        }
        $time = $posts->modified ? $posts->modified : $posts->created;
        $urls[] = array('loc' => $posts->permalink, 'lastmod' => date('Y-m-d', $time), 'priority' => '0.8');
    }

    $pages = $archive->widget('Widget_Contents_Page_List');
    while ($pages->next()) {
        $time = $pages->modified ? $pages->modified : $pages->created;
        $urls[] = array('loc' => $pages->permalink, 'lastmod' => date('Y-m-d', $time), 'priority' => '0.6');
    }

    $cats = $archive->widget('Widget_Metas_Category_List');
    while ($cats->next()) {
        $urls[] = array('loc' => $cats->permalink, 'lastmod' => $today, 'priority' => '0.5');
    }

    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $item) {
        echo '  <url>' . "\n";
        echo '    <loc>' . htmlspecialchars($item['loc'], ENT_QUOTES, 'UTF-8') . '</loc>' . "\n";
        echo '    <lastmod>' . $item['lastmod'] . '</lastmod>' . "\n";
        echo '    <changefreq>weekly</changefreq>' . "\n";
        echo '    <priority>' . $item['priority'] . '</priority>' . "\n";
        echo '  </url>' . "\n";
    }
    echo '</urlset>';
    exit;
}

// 文章浏览量统计
function get_post_view($archive) {
    $cid    = $archive->cid;
    $db     = Typecho_Db::get();
    $prefix = $db->getPrefix();
    
    // 检查数据库中是否有 views 字段
    if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')))) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0;');
        echo 0;
        return;
    }
    
    // 获取当前文章的浏览量
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    
    // 如果是文章页面，增加浏览量
    if ($archive->is('single')) {
        $views = Typecho_Cookie::get('extend_contents_views');
        if (empty($views)) {
            $views = array();
        } else {
            $views = explode(',', $views);
        }
        
        // 如果当前文章不在浏览记录中，则增加浏览量
        if (!in_array($cid, $views)) {
            $db->query($db->update('table.contents')->rows(array('views' => (int) $row['views'] + 1))->where('cid = ?', $cid));
            array_push($views, $cid);
            $views = implode(',', $views);
            Typecho_Cookie::set('extend_contents_views', $views); // 记录浏览 cookie
        }
    }
    
    echo $row['views'];
}

/**
 * Memos API PHP 代理
 * 解决 Nginx 反向代理冲突问题
 * 通过查询参数方式调用：/?action=memos-proxy&creatorId=1&rowStatus=NORMAL&limit=10
 */
function themeInit($archive) {
    // 站点地图入口：/sitemap.xml 与 ?action=sitemap 两种写法
    $requestPath = isset($_SERVER['REQUEST_URI']) ? (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
    if ((isset($_GET['action']) && $_GET['action'] === 'sitemap') || substr($requestPath, -12) === '/sitemap.xml') {
        thirteen_sitemap($archive);
    }

    // 检查是否是 Memos API 代理请求
    if (isset($_GET['action']) && $_GET['action'] === 'memos-proxy') {
        // 获取请求参数
        $creatorId = isset($_GET['creatorId']) ? $_GET['creatorId'] : '1';
        $rowStatus = isset($_GET['rowStatus']) ? $_GET['rowStatus'] : 'NORMAL';
        $limit = isset($_GET['limit']) ? $_GET['limit'] : '10';
        $pageToken = isset($_GET['pageToken']) ? $_GET['pageToken'] : '';
        
        // Memos 服务器地址
        $memosHost = 'http://127.0.0.1:5230';
        
        // 构建 Memos API URL
        $apiUrl = $memosHost . '/api/v1/memos?creatorId=' . urlencode($creatorId) 
                . '&rowStatus=' . urlencode($rowStatus) 
                . '&limit=' . urlencode($limit);
        
        if (!empty($pageToken)) {
            $apiUrl .= '&pageToken=' . urlencode($pageToken);
        }
        
        // 使用 cURL 请求 Memos API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // 设置响应头
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($error) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to connect to Memos API: ' . $error]);
        } else {
            http_response_code($httpCode);
            echo $response;
        }
        exit;
    }
    
    // Memos 文件代理请求
    if (isset($_GET['action']) && $_GET['action'] === 'memos-file-proxy') {
        // 获取文件路径参数
        $filePath = isset($_GET['path']) ? $_GET['path'] : '';
        
        if (empty($filePath)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing file path parameter']);
            exit;
        }
        
        // Memos 服务器地址
        $memosHost = 'http://127.0.0.1:5230';
        
        // 构建 Memos 文件 URL
        $fileUrl = $memosHost . '/' . ltrim($filePath, '/');
        
        // 使用 cURL 请求文件
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fileUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        
        // 设置响应头
        if ($contentType) {
            header('Content-Type: ' . $contentType);
        }
        header('Cache-Control: public, max-age=2592000'); // 30天缓存
        header('Access-Control-Allow-Origin: *');
        
        if ($response === false) {
            http_response_code(404);
            echo 'File not found';
        } else {
            http_response_code($httpCode);
            echo $response;
        }
        exit;
    }
}
