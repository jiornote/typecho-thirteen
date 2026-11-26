<?php
/**
 * Thirteen - 文章页面模板
 * 
 * @package Thirteen
 * @author Your Name
 * @version 1.0.0
 * @link http://example.com
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 确保立即输出
if (ob_get_level()) {
    ob_end_flush();
}
?>
<?php $this->need('header.php'); ?>

<div class="article-container">
    <!-- 文章内容 -->
    <main class="article-main">
        <article class="single-post">
            <!-- 文章头部（标题和元信息） -->
            <header class="post-header">
                <h1 class="post-title"><?php $this->title() ?></h1>
                
                <div class="post-meta-info">
                    <?php 
                    // 统计字数
                    $content = $this->content;
                    $wordCount = mb_strlen(strip_tags($content), 'UTF-8');
                    
                    // 计算阅读时间（约 300 字/分钟）
                    $readTime = ceil($wordCount / 300);
                    ?>
                    <span>作者：<?php $this->author(); ?></span>
                    <span>浏览：<?php get_post_view($this); ?></span>
                    <span>字数：<?php echo $wordCount; ?></span>
                    <span>阅读：约 <?php echo $readTime; ?> 分钟</span>
                </div>
            </header>
            
            <!-- 正文区域 -->
            <div class="post-body">
                <!-- 文章内容 -->
                <div class="post-content">
                    <?php 
                    // 解析文章引用卡片的辅助函数
                    function parse_ref_card($postId) {
                        $db = Typecho_Db::get();
                        $post = $db->fetchRow($db->select()->from('table.contents')->where('cid = ? AND status = ? AND type = ?', $postId, 'publish', 'post'));
                        
                        if (!$post) {
                            return '<div class="post-ref-error">文章不存在</div>';
                        }
                        
                        // 获取纯文本摘要（去掉所有 Markdown 语法和 HTML 标签）
                        $cleanText = $post['text'];
                        $cleanText = preg_replace('/^#{1,6}\s*/m', '', $cleanText);
                        $cleanText = preg_replace('/!\[.*?\]\(.*?\)/s', '', $cleanText);
                        $cleanText = preg_replace('/^>\s*/m', '', $cleanText);
                        $cleanText = preg_replace('/^[-*]\s*/m', '', $cleanText);
                        $cleanText = preg_replace('/^\d+\.\s*/m', '', $cleanText);
                        $cleanText = preg_replace('/^```[\s\S]*?```/m', '', $cleanText);
                        $cleanText = preg_replace('/\|.*?\|/m', '', $cleanText);
                        $cleanText = preg_replace('/\*\*(.+?)\*\*/', '$1', $cleanText);
                        $cleanText = preg_replace('/\*(.+?)\*/', '$1', $cleanText);
                        $cleanText = preg_replace('/~~(.+?)~~/', '$1', $cleanText);
                        $cleanText = preg_replace('/`(.+?)`/', '$1', $cleanText);
                        $cleanText = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $cleanText);
                        $cleanText = strip_tags($cleanText);
                        $cleanText = trim(preg_replace('/\s+/', ' ', $cleanText));
                        $excerpt = Typecho_Common::subStr($cleanText, 0, 100, '...');
                                            
                        // 获取第一张图片 URL（支持 Markdown 和 HTML 格式）
                        $thumb = '';
                        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $post['text'], $imgMatches)) {
                            $thumb = $imgMatches[1];
                        } elseif (preg_match('/!\[[^\]]*\]\(([^\)]+)\)/i', $post['text'], $imgMatches)) {
                            $thumb = $imgMatches[1];
                        }
                                            
                        // 构建文章链接
                        $permalink = Typecho_Router::url('post', array('cid' => $postId), Helper::options()->siteUrl);
                                            
                        $html = '<div class="post-ref-card">';
                        $html .= '<a href="' . htmlspecialchars($permalink) . '" class="post-ref-link" target="_blank" rel="noopener noreferrer">';
                        if ($thumb) {
                            $html .= '<div class="post-ref-thumb" style="background-image:url(' . htmlspecialchars($thumb) . ')"></div>';
                        } else {
                            $html .= '<div class="post-ref-thumb post-ref-no-thumb"><span>Read</span></div>';
                        }
                        $html .= '<div class="post-ref-info">';
                        $html .= '<h3 class="post-ref-title">' . htmlspecialchars($post['title']) . '</h3>';
                        $html .= '<p class="post-ref-excerpt">' . htmlspecialchars($excerpt) . '</p>';
                        $html .= '</div>';
                        $html .= '</a>';
                        $html .= '</div>';
                        
                        return $html;
                    }
                    
                    // 解析下载短代码 [DOWNLOAD:文本|URL|图标]
                    $content = $this->content;
                    
                    // 解析文章引用短代码 [REF:文章ID]
                    // 先匹配被 <p> 包裹的情况
                    $content = preg_replace_callback(
                        '/<p>\\s*\\[REF:(\\d+)\\]\\s*<\/p>/i',
                        function($matches) {
                            return parse_ref_card($matches[1]);
                        },
                        $content
                    );
                    
                    // 再匹配没有被 <p> 包裹的情况
                    $content = preg_replace_callback(
                        '/\\[REF:(\\d+)\\]/i',
                        function($matches) {
                            return parse_ref_card($matches[1]);
                        },
                        $content
                    );
                    
                    // 最后解析下载短代码
                    $content = preg_replace(
                        '/\\[DOWNLOAD:(.*?)\\|(.*?)\\|(.*?)\\]/i',
                        '<a href="$2" class="download-btn" target="_blank" rel="noopener noreferrer"><i class="fa $3"></i> $1</a>',
                        $content
                    );
                    
                    echo $content;
                    ?>
                </div>
                
                <!-- 文章标签 -->
                <?php if ($this->tags): ?>
                <div class="post-tags">
                    <span class="tags-label">标签：</span>
                    <?php $this->tags(' ', true, '暂无标签'); ?>
                </div>
                <?php endif; ?>
            </div>
        </article>
        
        <!-- 评论区 -->
        <?php $this->need('comments.php'); ?>
    </main>
</div>

<!-- 图片灯箱：原生 <dialog> 实现，打开/关闭不锁滚动条，页面无跳动 -->
<dialog id="img-lightbox" aria-label="图片预览">
    <img id="img-lightbox-image" src="" alt="">
    <button type="button" class="lb-btn lb-close" aria-label="关闭">×</button>
    <button type="button" class="lb-btn lb-prev" aria-label="上一张">‹</button>
    <button type="button" class="lb-btn lb-next" aria-label="下一张">›</button>
</dialog>

<style>
#img-lightbox {
    border: 0;
    padding: 0;
    background: transparent;
    width: 100%;
    height: 100%;
    max-width: 100vw;
    max-height: 100vh;
    overflow: hidden;
}
#img-lightbox::backdrop {
    background: rgba(0, 0, 0, 0.9);
}
#img-lightbox img {
    position: absolute;
    inset: 0;
    margin: auto;
    max-width: 92vw;
    max-height: 88vh;
    object-fit: contain;
}
#img-lightbox .lb-btn {
    position: absolute;
    z-index: 1;
    border: 0;
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
}
#img-lightbox .lb-btn:hover {
    background: rgba(255, 255, 255, 0.28);
}
#img-lightbox .lb-close { top: 16px; right: 16px; }
#img-lightbox .lb-prev  { left: 16px; top: 50%; transform: translateY(-50%); }
#img-lightbox .lb-next  { right: 16px; top: 50%; transform: translateY(-50%); }
</style>

<script>
(function() {
    function initLightbox() {
        var lb = document.getElementById('img-lightbox');
        var lbImg = document.getElementById('img-lightbox-image');
        var images = Array.prototype.slice.call(document.querySelectorAll('.post-content img'));
        if (!lb || images.length === 0) return;

        var idx = 0;
        function show(i) {
            idx = (i + images.length) % images.length;
            lbImg.src = images[idx].src;
            lb.querySelector('.lb-prev').hidden =
            lb.querySelector('.lb-next').hidden = images.length < 2;
        }

        images.forEach(function(img, i) {
            img.style.cursor = 'zoom-in';
            img.addEventListener('click', function() {
                show(i);
                if (!lb.open) lb.showModal();
            });
        });

        lb.querySelector('.lb-close').addEventListener('click', function() { lb.close(); });
        lb.querySelector('.lb-prev').addEventListener('click', function() { show(idx - 1); });
        lb.querySelector('.lb-next').addEventListener('click', function() { show(idx + 1); });
        lb.addEventListener('click', function(e) { if (e.target === lb) lb.close(); });
        lb.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') show(idx - 1);
            if (e.key === 'ArrowRight') show(idx + 1);
        });
    }
    window.addEventListener('load', initLightbox);
})();
</script>

<!-- 一行多图排版：按行识别纯图片组，2张并排/3张并排/4张及以上每行两张 -->
<script>
(function() {
    function imgCountOf(line) {
        var c = 0;
        for (var i = 0; i < line.length; i++) {
            var n = line[i];
            if (n.nodeType === 1 && n.tagName === 'IMG') c++;
            else if (!(n.nodeType === 3 && n.nodeValue.trim() === '')) return 0;
        }
        return c;
    }
    function allPhotos(line) {
        return line.every(function(n) {
            return n.nodeType === 3 || (n.tagName === 'IMG' && n.src.indexOf('/usr/uploads/') !== -1);
        });
    }
    function rowClass(c) {
        if (c < 2) return '';
        return c === 3 ? 'img-row-3' : 'img-row-2';
    }
    function applyImageRows() {
        document.querySelectorAll('.post-content p').forEach(function(p) {
            if (!p.querySelector('img')) return;
            var lines = [[]];
            Array.prototype.forEach.call(p.childNodes, function(n) {
                if (n.nodeType === 1 && n.tagName === 'BR') {
                    lines.push([]);
                } else if (n.nodeType === 3 && n.nodeValue.indexOf('\n') !== -1) {
                    n.nodeValue.split('\n').forEach(function(t, i) {
                        if (i > 0) lines.push([]);
                        if (t !== '') lines[lines.length - 1].push(document.createTextNode(t));
                    });
                } else {
                    lines[lines.length - 1].push(n);
                }
            });
            while (lines.length > 1) {
                var last = lines[lines.length - 1];
                var empty = last.every(function(n) { return n.nodeType === 3 && n.nodeValue.trim() === ''; });
                if (empty) lines.pop(); else break;
            }
            if (lines.length === 1) {
                var c1 = imgCountOf(lines[0]);
                if (c1 >= 2 && allPhotos(lines[0])) p.classList.add(rowClass(c1));
                else if (c1 === 1 && allPhotos(lines[0])) p.classList.add('img-solo');
                return;
            }
            var frag = document.createDocumentFragment();
            lines.forEach(function(line) {
                var block = document.createElement('p');
                var c = imgCountOf(line);
                if (c >= 2 && allPhotos(line)) block.className = rowClass(c);
                else if (c === 1 && allPhotos(line)) block.className = 'img-solo';
                line.forEach(function(n) { block.appendChild(n); });
                frag.appendChild(block);
            });
            p.parentNode.replaceChild(frag, p);
        });
    }
        // 主题的 Markdown 是渲染后才插入 DOM 的，用 MutationObserver 在图片出现的同一帧完成排版，
    // 避免等 window load（图片全部加载完）才执行导致的向上跳动
    var content = document.querySelector('.post-content');
    if (content) {
        applyImageRows();
        new MutationObserver(function() { applyImageRows(); })
            .observe(content, { childList: true, subtree: true });
    }
    window.addEventListener('load', applyImageRows);
})();
</script>
<?php $this->need('footer.php'); ?>