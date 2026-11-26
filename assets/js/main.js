/**
 * Thirteen Theme - 主JavaScript文件
 */

(function() {
    'use strict';
    
    // DOM加载完成后执行
    document.addEventListener('DOMContentLoaded', function() {
        
        // 初始化导航菜单切换
        initNavToggle();
        
        // 初始化搜索快捷键
        initSearchShortcut();
        
        // 初始化订阅面板
        initSubscribePanel();
        
        // 初始化首页轮播
        initHeroCarousel();
        
        // 初始化移动端触摸滑动
        initMobileTouch();
        
        // 示例：平滑滚动
        initSmoothScroll();
        
        // 初始化回到顶部按钮
        initBackToTop();
    });
    
    /**
     * 初始化导航菜单切换
     */
    function initNavToggle() {
        var navToggle = document.getElementById('navToggle');
        var navPanel = document.getElementById('navPanel');
        var dividerWrapper = document.querySelector('.divider-wrapper');
        var hideTimer;
        
        // 检测是否为移动设备
        var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) 
            || window.innerWidth <= 768;
        
        if (navToggle && navPanel && dividerWrapper) {
            // 动态设置导航面板的右边距，对齐divider-wrapper
            function updatePanelPosition() {
                var headerRect = document.querySelector('.header-container').getBoundingClientRect();
                var dividerRect = dividerWrapper.getBoundingClientRect();
                var rightOffset = headerRect.right - dividerRect.right;
                navPanel.style.right = rightOffset + 'px';
            }
            
            // 初始定位
            updatePanelPosition();
            window.addEventListener('resize', updatePanelPosition);
            
            // 移动端：点击切换
            if (isMobile) {
                // 初始化面板状态为隐藏
                navPanel.style.opacity = '0';
                navPanel.style.visibility = 'hidden';
                navPanel.style.transform = 'translateY(-10px)';
                navPanel.style.pointerEvents = 'none';
                
                // 点击导航按钮切换面板
                navToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var isVisible = navPanel.style.opacity === '1';
                    
                    if (isVisible) {
                        navPanel.style.opacity = '0';
                        navPanel.style.visibility = 'hidden';
                        navPanel.style.transform = 'translateY(-10px)';
                        navPanel.style.pointerEvents = 'none';
                    } else {
                        navPanel.style.opacity = '1';
                        navPanel.style.visibility = 'visible';
                        navPanel.style.transform = 'translateY(0)';
                        navPanel.style.pointerEvents = 'auto';
                    }
                });
                
                // 点击页面其他地方关闭导航面板
                document.addEventListener('click', function(e) {
                    if (!navToggle.contains(e.target) && !navPanel.contains(e.target)) {
                        navPanel.style.opacity = '0';
                        navPanel.style.visibility = 'hidden';
                        navPanel.style.transform = 'translateY(-10px)';
                        navPanel.style.pointerEvents = 'none';
                    }
                });
            } else {
                // 桌面端：hover 显示
                // 鼠标移入导航图标时显示
                navToggle.addEventListener('mouseenter', function() {
                    clearTimeout(hideTimer);
                    navPanel.style.opacity = '1';
                    navPanel.style.visibility = 'visible';
                    navPanel.style.transform = 'translateY(0)';
                    navPanel.style.pointerEvents = 'auto';
                });
                
                // 鼠标离开导航图标时延迟隐藏
                navToggle.addEventListener('mouseleave', function() {
                    hideTimer = setTimeout(function() {
                        navPanel.style.opacity = '0';
                        navPanel.style.visibility = 'hidden';
                        navPanel.style.transform = 'translateY(-10px)';
                        navPanel.style.pointerEvents = 'none';
                    }, 300);
                });
                
                // 鼠标移到面板上时取消隐藏
                navPanel.addEventListener('mouseenter', function() {
                    clearTimeout(hideTimer);
                });
                
                // 鼠标离开面板时隐藏
                navPanel.addEventListener('mouseleave', function() {
                    navPanel.style.opacity = '0';
                    navPanel.style.visibility = 'hidden';
                    navPanel.style.transform = 'translateY(-10px)';
                    navPanel.style.pointerEvents = 'none';
                });
            }
        }
    }
    
    /**
     * 初始化搜索快捷键
     */
    function initSearchShortcut() {
        var searchBtn = document.getElementById('searchBtn');
        var searchOverlay = document.getElementById('searchOverlay');
        var searchInput = document.getElementById('searchInput');
        var searchClose = document.getElementById('searchClose');
        
        // 打开搜索面板
        function openSearch() {
            if (searchOverlay && searchInput) {
                searchOverlay.classList.add('active');
                searchInput.focus();
            }
        }
        
        // 关闭搜索面板
        function closeSearch() {
            if (searchOverlay && searchInput) {
                searchOverlay.classList.remove('active');
                searchInput.value = '';
            }
        }
        
        if (searchBtn) {
            searchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                openSearch();
            });
        }
        
        if (searchClose) {
            searchClose.addEventListener('click', function() {
                closeSearch();
            });
        }
        
        // 点击遮罩层关闭
        if (searchOverlay) {
            searchOverlay.addEventListener('click', function(e) {
                if (e.target === searchOverlay) {
                    closeSearch();
                }
            });
        }
        
        // Ctrl+K 快捷键打开
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                openSearch();
            }
            
            // Esc 关闭
            if (e.key === 'Escape') {
                closeSearch();
            }
        });
        
        // 搜索输入框回车事件
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    var query = searchInput.value.trim();
                    if (query) {
                        // 跳转到搜索结果页面
                        var siteUrl = window.location.origin + window.location.pathname;
                        // 移除末尾的斜杠
                        if (siteUrl.endsWith('/')) {
                            siteUrl = siteUrl.slice(0, -1);
                        }
                        window.location.href = siteUrl + '?s=' + encodeURIComponent(query);
                    }
                }
            });
        }
    }
    
    /**
     * 初始化订阅面板
     */
    function initSubscribePanel() {
        var openSubscribeBtn = document.getElementById('openSubscribe');
        var subscribeOverlay = document.getElementById('subscribeOverlay');
        var subscribeClose = document.getElementById('subscribeClose');
        var subscribeForm = document.getElementById('subscribeForm');
        var subscribeEmail = document.getElementById('subscribeEmail');
        var subscribeMessage = document.getElementById('subscribeMessage');
        
        if (!openSubscribeBtn || !subscribeOverlay) return;
        
        // 打开订阅面板
        function openSubscribe() {
            subscribeOverlay.classList.add('active');
            if (subscribeEmail) {
                setTimeout(function() {
                    subscribeEmail.focus();
                }, 100);
            }
        }
        
        // 关闭订阅面板
        function closeSubscribe() {
            subscribeOverlay.classList.remove('active');
            if (subscribeEmail) {
                subscribeEmail.value = '';
            }
            if (subscribeMessage) {
                subscribeMessage.className = 'subscribe-message';
                subscribeMessage.textContent = '';
            }
        }
        
        // 显示消息
        function showMessage(text, type) {
            if (subscribeMessage) {
                subscribeMessage.textContent = text;
                subscribeMessage.className = 'subscribe-message ' + type;
            }
        }
        
        // 点击打开按钮
        openSubscribeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openSubscribe();
        });
        
        // 点击关闭按钮
        if (subscribeClose) {
            subscribeClose.addEventListener('click', function() {
                closeSubscribe();
            });
        }
        
        // 点击遮罩层关闭
        subscribeOverlay.addEventListener('click', function(e) {
            if (e.target === subscribeOverlay) {
                closeSubscribe();
            }
        });
        
        // Esc 键关闭
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && subscribeOverlay.classList.contains('active')) {
                closeSubscribe();
            }
        });
        
        // 表单提交处理
        if (subscribeForm) {
            subscribeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                var email = subscribeEmail ? subscribeEmail.value.trim() : '';
                
                if (!email) {
                    showMessage('请输入邮箱地址', 'error');
                    return;
                }
                
                // 验证邮箱格式
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showMessage('请输入有效的邮箱地址', 'error');
                    return;
                }
                
                // 禁用按钮，防止重复提交
                var submitBtn = subscribeForm.querySelector('.subscribe-submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = '提交中...';
                }
                
                // 发送AJAX请求到插件Action
                var xhr = new XMLHttpRequest();
                // 从表单的data-action-url属性获取URL
                var actionUrl = subscribeForm.getAttribute('data-action-url') || '/action/subscribe';
                xhr.open('POST', actionUrl, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            try {
                                var response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    showMessage(response.message || '订阅成功！请查收确认邮件。', 'success');
                                    if (subscribeEmail) {
                                        subscribeEmail.value = '';
                                    }
                                } else {
                                    showMessage(response.message || '订阅失败，请稍后重试。', 'error');
                                }
                            } catch (e) {
                                showMessage('订阅失败，请稍后重试。', 'error');
                            }
                        } else {
                            showMessage('网络错误，请稍后重试。', 'error');
                        }
                        
                        // 恢复按钮状态
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = '订阅';
                        }
                    }
                };
                
                xhr.send('email=' + encodeURIComponent(email));
            });
        }
    }
    
    /**
     * 初始化首页轮播
     */
    function initHeroCarousel() {
        var slides = document.querySelectorAll('.carousel-item');
        var dots = document.querySelectorAll('.carousel-dot');
        var currentIndex = 0;
        var autoPlayTimer = null;
        
        if (slides.length === 0) return;
        
        // 切换到指定幻灯片
        function goToSlide(index) {
            // 移除所有active类
            slides.forEach(function(slide) {
                slide.classList.remove('active');
            });
            dots.forEach(function(dot) {
                dot.classList.remove('active');
            });
            
            // 添加active类到当前幻灯片
            slides[index].classList.add('active');
            if (dots[index]) {
                dots[index].classList.add('active');
            }
            
            currentIndex = index;
        }
        
        // 下一张
        function nextSlide() {
            var nextIndex = (currentIndex + 1) % slides.length;
            goToSlide(nextIndex);
        }
        
        // 自动播放
        function startAutoPlay() {
            autoPlayTimer = setInterval(function() {
                nextSlide();
            }, 5000);
        }
        
        // 停止自动播放
        function stopAutoPlay() {
            clearInterval(autoPlayTimer);
        }
        
        // 点击指示器
        dots.forEach(function(dot, index) {
            dot.addEventListener('click', function() {
                stopAutoPlay();
                goToSlide(index);
                startAutoPlay();
            });
        });
        
        // 启动自动播放
        startAutoPlay();
    }
    
    /**
     * 初始化平滑滚动
     */
    function initSmoothScroll() {
        var links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                var targetId = this.getAttribute('href');
                
                if (targetId !== '#' && document.querySelector(targetId)) {
                    e.preventDefault();
                    
                    var targetElement = document.querySelector(targetId);
                    var offsetTop = targetElement.offsetTop - 100;
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
    
    /**
     * 初始化回到顶部按钮
     */
    function initBackToTop() {
        var backToTopBtn = document.getElementById('backToTop');
        if (!backToTopBtn) return;
        
        // 滚动监听
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        
        // 点击事件
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    /**
     * 初始化移动端触摸滑动
     */
    function initMobileTouch() {
        // 检测是否为移动设备
        var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) 
            || window.innerWidth <= 768;
        
        if (!isMobile) return;
        
        // 轮播图触摸滑动
        var carousel = document.getElementById('heroCarousel');
        if (carousel) {
            var slides = carousel.querySelectorAll('.carousel-item');
            if (slides.length > 0) {
                var touchStartX = 0;
                var touchEndX = 0;
                var currentIndex = 0;
                
                carousel.addEventListener('touchstart', function(e) {
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });
                
                carousel.addEventListener('touchend', function(e) {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                }, { passive: true });
                
                function handleSwipe() {
                    var diff = touchStartX - touchEndX;
                    var threshold = 50; // 滑动阈值
                    
                    if (Math.abs(diff) > threshold) {
                        if (diff > 0) {
                            // 向左滑动，下一张
                            currentIndex = (currentIndex + 1) % slides.length;
                        } else {
                            // 向右滑动，上一张
                            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                        }
                        
                        // 切换到指定幻灯片
                        slides.forEach(function(slide) {
                            slide.classList.remove('active');
                        });
                        slides[currentIndex].classList.add('active');
                        
                        // 更新指示器
                        var dots = carousel.querySelectorAll('.carousel-dot');
                        dots.forEach(function(dot) {
                            dot.classList.remove('active');
                        });
                        if (dots[currentIndex]) {
                            dots[currentIndex].classList.add('active');
                        }
                    }
                }
            }
        }
    }
    
})();
