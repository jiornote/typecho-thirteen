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
    </main>
    
    <!-- 回到顶部按钮 -->
    <button class="back-to-top" id="backToTop" title="回到顶部">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m18 15-6-6-6 6"/>
        </svg>
    </button>
    
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-content">
                <!-- 第一行：年份 + 版权 -->
                <div class="footer-line footer-line-1">
                    <span class="footer-divider">2026-PRESENT</span>
                    <span>©</span>
                    <span class="footer-site-wrapper">
                        <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>
                        <span class="footer-color-bar"></span>
                    </span>
                </div>
                
                <!-- 第二行：技术栈 + 主题名 -->
                <div class="footer-line footer-line-2">
                    Powered By <a href="http://www.typecho.org" target="_blank" rel="noopener">Typecho</a>. <a href="https://www.jinote.cn" target="_blank" rel="noopener">Thirteen</a>
                </div>
                
                <!-- 第三行：备案信息 -->
                <div class="footer-line footer-line-3">
                    <?php if ($this->options->icp): ?>
                        <?php $this->options->icp(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- 引入JS -->
    <script src="<?php $this->options->themeUrl('assets/js/main.js'); ?>"></script>
    
    <!-- Highlight.js 代码高亮 -->
    <script src="<?php $this->options->themeUrl('assets/js/highlight.min.js'); ?>"></script>
    <script>
    window.addEventListener('DOMContentLoaded', function() {
        if (typeof hljs !== 'undefined') {
            // 查找所有 pre code 元素
            var codeBlocks = document.querySelectorAll('pre code');
            codeBlocks.forEach(function(block) {
                hljs.highlightElement(block);
            });
        }
    });
    </script>
    
    <script>
    // 评论加载更多功能 - 前端控制显示
    window.addEventListener('load', function() {
        var loadMoreBtn = document.getElementById('loadMoreBtn');
        var loadMoreContainer = document.getElementById('loadMoreContainer');
        var commentList = document.getElementById('commentList');
        
        if (!loadMoreBtn || !loadMoreContainer || !commentList) {
            return;
        }
        
        // Typecho 会在外面再包裹一层 ol.comment-list，需要找到内层的评论列表
        var innerCommentList = commentList.querySelector('ol.comment-list') || commentList;
        
        // 获取所有一级评论（comment-parent）
        var allParentComments = [];
        var children = innerCommentList.children;
        
        for (var i = 0; i < children.length; i++) {
            if (children[i].tagName === 'LI' && children[i].classList.contains('comment-parent')) {
                allParentComments.push(children[i]);
            }
        }
        
        var initialShow = 3;
        
        // 如果一级评论数量少于等于3条，隐藏按钮
        if (allParentComments.length <= initialShow) {
            loadMoreContainer.style.display = 'none';
            return;
        }
        
        // 显示按钮
        loadMoreContainer.style.display = 'block';
        
        // 隐藏第4条及以后的一级评论（子评论跟随父评论显示/隐藏）
        var currentShow = initialShow;
        
        for (var i = 0; i < allParentComments.length; i++) {
            if (i >= initialShow) {
                allParentComments[i].style.display = 'none';
            }
        }
        
        // 绑定点击事件
        loadMoreBtn.addEventListener('click', function() {
            var nextShow = currentShow + 3;
            
            // 显示下一批一级评论（子评论跟随显示）
            for (var i = currentShow; i < nextShow && i < allParentComments.length; i++) {
                allParentComments[i].style.display = 'block';
            }
            
            currentShow = nextShow;
            
            if (currentShow >= allParentComments.length) {
                loadMoreContainer.style.display = 'none';
            }
        });
    });
    </script>
    
    <script>
    // 搜索结果加载更多功能
    window.addEventListener('load', function() {
        console.log('搜索 Load More 脚本开始执行');
        
        var loadMoreBtn = document.getElementById('searchLoadMoreBtn');
        var loadMoreContainer = document.getElementById('searchLoadMoreContainer');
        var resultsList = document.getElementById('searchResultsList');
        
        console.log('按钮元素:', loadMoreBtn);
        console.log('容器元素:', loadMoreContainer);
        console.log('结果列表:', resultsList);
        
        if (!loadMoreBtn || !loadMoreContainer || !resultsList) {
            console.log('元素未找到，退出');
            return;
        }
        
        // 获取所有搜索结果
        var allResults = resultsList.querySelectorAll('.post-item');
        var totalResults = allResults.length;
        var initialShow = 5;
        var currentShow = initialShow;
        
        console.log('总结果数:', totalResults);
        console.log('初始显示:', initialShow);
        
        // 如果结果数量少于等于5条，隐藏按钮
        if (totalResults <= initialShow) {
            console.log('结果数量少于5条，隐藏按钮');
            loadMoreContainer.style.display = 'none';
            return;
        }
        
        // 显示按钮
        console.log('显示按钮');
        loadMoreContainer.style.display = 'block';
        
        // 绑定点击事件 - 每次显示5条
        loadMoreBtn.addEventListener('click', function() {
            var nextShow = currentShow + 5;
            
            // 显示下一批结果
            for (var i = currentShow; i < nextShow && i < totalResults; i++) {
                allResults[i].style.display = 'flex';
            }
            
            currentShow = nextShow;
            
            // 如果全部显示完，隐藏按钮
            if (currentShow >= totalResults) {
                loadMoreContainer.style.display = 'none';
            }
        });
        
        console.log('搜索 Load More 脚本执行完成');
    });
    </script>
    
    <?php $this->footer(); ?>
</body>
</html>
