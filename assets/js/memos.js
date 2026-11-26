/**
 * Memos 动态页面渲染脚本
 * 完整参照 veryjack.com 的实现
 */
(function() {
    'use strict';

    // 加载 CSS 样式
    function loadCssCode(code) {
        var style = document.createElement('style');
        style.type = 'text/css';
        style.rel = 'stylesheet';
        style.appendChild(document.createTextNode(code));
        var head = document.getElementsByTagName('head')[0];
        head.appendChild(style);
    }

    // 完整的 CSS 样式（直接复制 veryjack 的样式）
    var allCSS = `
    #bber {
        margin-top: 80px;
        width: 90% !important;
        max-width: 758px;
        margin-left: auto;
        margin-right: auto;
    }
    @media screen and (max-width: 1000px) {
        #bber {
            margin-top: 80px;
            width: 100% !important;
        }
    }
    .bb-timeline pre {
        color: #aaa;
    }
    .bb-timeline ul {
        margin: 0;
        padding: 0;
    }
    .bb-timeline ul li {
        list-style-type: none;
        margin-bottom: 1rem;
    }
    .bb-timeline ul li .datacont ul li {
        margin-bottom: 0;
    }
    .bb-timeline ul li .bb-div {
        padding: 0.3rem 0 1.5rem 0;
        border-bottom: 1px solid #E7E9EF;
    }
    .bb-load {
        text-align: center;
        padding: 30px 0;
    }
    .bb-load button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 14px;
        background-color: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.2s ease;
        letter-spacing: 0;
        width: auto !important;
        box-shadow: none !important;
    }
    .bb-load button:hover {
        color: #374151 !important;
        text-decoration: underline;
        background-color: transparent !important;
    }
    .bb-load button:active {
        color: #1f2937 !important;
    }
    .bb-timeline ul li .datatime {
        display: flex;
        overflow: hidden;
        max-height: 100%;
    }
    .bb-timeline ul li .datacont {
        padding-left: 45px;
        margin-top: 5px;
        min-height: 50px;
    }
    .bb-timeline ul li .datacont img[src*='emotion'] {
        display: inline-block;
        width: auto;
    }
    .bb-timeline ul li .datafrom {
        color: #aaa;
        font-size: 0.75em !important;
        font-style: italic;
    }
    .bb-timeline ul li p {
        margin: 0;
        font-size: 14px;
        letter-spacing: 1px;
        line-height: 28px;
        min-height: 18px;
    }
    .bb-timeline pre p {
        display: inline-block;
    }
    .bb-timeline pre p:empty {
        display: none;
    }
    .datacont p {
        margin: 0;
    }
    .datacont blockquote {
        font-family: KaiTi,STKaiti,STFangsong !important;
        margin: 0 0 0 1rem;
        padding: .25rem 2rem;
        position: relative;
        border-left: 0 none;
    }
    .datacont blockquote::before {
        line-height: 2rem;
        content: '"';
        font-family: Georgia,serif;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        left: 10px;
        top: 5px;
    }
    .datacont .tag-span {
        color: #98c1d9;
    }
    .datasource a {
        color: #fafafa;
        background: #3b3d42;
        padding: 2px 8px;
        margin: 0 6px 0 0;
        border-radius: 5px;
        font-size: .9rem;
        font-weight: 400;
    }
    .datacont .img {
        cursor: pointer;
        max-width: 250px;
        max-height: 400px;
        border-radius: 4px;
    }
    .datacont .img.square {
        height: 180px;
        width: 180px;
        object-fit: cover;
    }
    .resimg.grid,
    .resimg.grid-2,
    .resimg.grid-4 {
        display: grid;
        box-sizing: border-box;
        margin: 8px 0 0;
        max-width: 300px;
    }
    .resimg.single {
        margin: 8px 0 0;
        max-width: 300px;
        display: inline-block;
    }
    .resimg.single figure.gallery-thumbnail {
        position: relative;
        width: 100%;
        margin: 0 !important;
        overflow: hidden;
        border-radius: 4px;
        cursor: pointer;
    }
    .resimg.single figure.gallery-thumbnail > img.thumbnail-image {
        display: block;
        width: 100%;
        height: auto;
        object-fit: contain;
    }
    .resimg.grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    .resimg.grid-2 {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .resimg.grid-4 {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .resimg.grid figure.gallery-thumbnail,
    .resimg.grid-2 figure.gallery-thumbnail,
    .resimg.grid-4 figure.gallery-thumbnail {
        position: relative;
        width: 100%;
        height: 0;
        padding-top: 100%;
        margin: 0 !important;
        overflow: hidden;
        border-radius: 4px;
        cursor: pointer;
    }
    .resimg.grid figure.gallery-thumbnail > img.thumbnail-image,
    .resimg.grid-2 figure.gallery-thumbnail > img.thumbnail-image,
    .resimg.grid-4 figure.gallery-thumbnail > img.thumbnail-image {
        position: absolute;
        left: 0;
        top: 0;
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    .hy-avatar {
        max-height: 35px;
        max-width: 35px;
        border-radius: 50%;
        margin-right: 10px;
        flex-shrink: 0;
    }
    .hy-intro {
        flex: 1;
        min-width: 0;
    }
    .hy-name {
        font-size: 0.8em;
        color: #44507b !important;
        font-weight: 600;
    }
    .hy-tag .hy-text-muted {
        color: #9b9b9b;
        text-align: right;
        font-size: 0.65em;
    }
    .hy-time .hy-text-muted {
        color: #9b9b9b;
        text-align: right;
        font-size: 0.65em;
    }
    .hy-time {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        color: #9b9b9b;
        text-align: right;
        font-size: 0.65em;
        margin-top: 0px;
    }
    .hy-tag {
        color: #9b9b9b;
        text-align: right;
        font-size: 0.65em;
        margin-top: 20px;
    }
    .hy-location {
        color: #576b95;
        margin-right: 10px;
    }
    .hy-astyle {
        text-decoration: none;
        cursor: pointer;
    }
    .hy-tags-item {
        margin-right: 10px;
    }
    .hy-comment-link {
        margin-top: 10px;
        text-align: right;
    }
    .commentsLink {
        text-decoration: none;
        color: #475671;
        text-align: right;
        font-size: 12px;
        border-radius: 3px;
        padding: 4px 10px;
        font-weight: 400;
    }
    .d-none {
        display: none;
    }
    .comment {
        margin-top: 19px;
    }
    .datatime {
        display: flex;
        overflow: hidden;
        max-height: 100%;
        align-items: center;
    }
    .datacont {
        padding-left: 45px;
        margin-top: 5px;
    }`;

    // 初始化
    window.addEventListener('load', function() {
        loadCssCode(allCSS);
        
        var bbDom = document.querySelector(bbMemo.domId);
        if (!bbDom) {
            console.error('找不到 Memos 容器');
            return;
        }

        var pageToken = '';  // Memos 0.28.0 使用 pageToken 分页
        var nextLength = 0;
        var nextDom = null;
        var hasMore = true;  // 是否有更多数据

        // 首次加载
        getFirstList();

        // 绑定加载更多按钮事件（使用事件委托，因为按钮是动态创建的）
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('load-btn')) {
                e.preventDefault();
                
                if (!hasMore) {
                    return;
                }
                
                if (!nextDom || nextDom.length === 0) {
                    return;
                }
                
                updateHTML(nextDom);
                nextDom = null;
                
                if (nextLength < bbMemo.limit || !pageToken) {
                    var btn = document.querySelector('.load-btn');
                    if (btn) btn.remove();
                    hasMore = false;
                    return;
                }
                
                getNextList();
            }
        });

        // 获取首次数据
        function getFirstList() {
            bbDom.insertAdjacentHTML('afterend', '<div class="bb-load"><button class="load-btn">加载更多</button></div>');
            
            var bbUrl = bbMemo.memos + 'creatorId=' + bbMemo.creatorId + '&rowStatus=NORMAL&limit=' + bbMemo.limit;
            
            fetch(bbUrl)
                .then(function(res) {
                    return res.json();
                })
                .then(function(resdata) {
                    // Memos 0.28.0 返回格式：{"memos": [...], "nextPageToken": ""}
                    var memosList = resdata.memos || resdata;
                    pageToken = resdata.nextPageToken || '';  // 保存下一页 token
                    updateHTML(memosList);
                    var nowLength = memosList.length;
                    
                    if (nowLength < bbMemo.limit || !pageToken) {
                        var btn = document.querySelector('.load-btn');
                        if (btn) btn.remove();
                        return;
                    }
                    
                    getNextList();  // 预加载下一页
                })
                .catch(function(error) {
                    console.error('Memos 加载失败:', error);
                    bbDom.innerHTML = '<div class="bb-error">加载失败，请检查 Memos 地址是否正确</div>';
                });
        }

        // 获取下一页数据
        function getNextList() {
            var bbUrl = bbMemo.memos + 'creatorId=' + bbMemo.creatorId + '&rowStatus=NORMAL&limit=' + bbMemo.limit;
            if (pageToken) {
                bbUrl += '&pageToken=' + pageToken;
            }
            
            fetch(bbUrl)
                .then(function(res) {
                    return res.json();
                })
                .then(function(resdata) {
                    var memosList = resdata.memos || resdata;
                    nextDom = memosList;
                    nextLength = nextDom ? nextDom.length : 0;
                    pageToken = resdata.nextPageToken || '';  // 更新下一页 token
                    
                    if (!nextDom || nextLength < 1) {
                        hasMore = false;
                        return;
                    }
                })
                .catch(function(error) {
                    console.error('加载更多失败:', error);
                });
        }

        // 渲染 HTML
        function updateHTML(data) {
            if (!data || data.length === 0) return;

            var result = '';
            
            // Markdown 配置
            if (typeof marked !== 'undefined') {
                marked.setOptions({
                    breaks: true,
                    smartypants: true,
                    langPrefix: 'language-'
                });
            }

            for (var i = 0; i < data.length; i++) {
                var memo = data[i];
                var content = memo.content || '';
                
                // Memos 0.28.0 时间格式处理
                var created;
                if (memo.createTime) {
                    created = new Date(memo.createTime).getTime() / 1000;
                } else {
                    created = memo.createdTs || memo.created;
                }
                
                // 提取标签
                var tags = extractTags(content);
                
                // 替换标签为链接样式
                content = content.replace(/#([^\s#]+?) /g, '<a class="tag-span" href="javascript:;">#$1</a> ');
                
                // Markdown 渲染
                if (typeof marked !== 'undefined') {
                    content = marked.parse(content);
                }
                
                // 处理图片：从 Markdown 内容中提取
                var images = [];
                var imgReg = /!\[.*?\]\((.*?)\)/g;
                var match;
                while ((match = imgReg.exec(content)) !== null) {
                    images.push(match[1]);
                }
                
                // Memos 0.28.0 资源字段处理（attachments）
                if (memo.attachments && memo.attachments.length > 0) {
                    for (var j = 0; j < memo.attachments.length; j++) {
                        var res = memo.attachments[j];
                        
                        if (res.type && res.type.indexOf('image') !== -1) {
                            var imgUrl = '';
                            
                            // 方式 1：直接用外部链接
                            if (res.externalLink) {
                                imgUrl = res.externalLink;
                            }
                            // 方式 2：Memos 0.28.0 使用 name 和 filename 字段
                            else if (res.name && res.filename) {
                                // Memos 0.28.0 文件访问路径：/file/{name}/{filename}
                                imgUrl = bbMemo.fileProxy + 'file/' + res.name + '/' + res.filename;
                            }
                            // 方式 3：只有 name 字段
                            else if (res.name) {
                                imgUrl = bbMemo.fileProxy + 'file/' + res.name;
                            }
                            // 方式 4：只有 filename 字段
                            else if (res.filename) {
                                imgUrl = bbMemo.fileProxy + 'file/' + res.filename;
                            }
                            // 方式 5：完整 URL
                            else if (res.url) {
                                imgUrl = res.url;
                            }
                            
                            if (imgUrl && images.indexOf(imgUrl) === -1) {
                                images.push(imgUrl);
                            }
                        }
                    }
                }
                // 兼容 resources 字段
                if (memo.resources && memo.resources.length > 0) {
                    for (var j = 0; j < memo.resources.length; j++) {
                        var res = memo.resources[j];
                        if (res.type && res.type.indexOf('image') !== -1) {
                            var imgUrl = bbMemo.fileProxy + 'file/' + res.id + '/' + res.filename;
                            if (images.indexOf(imgUrl) === -1) {
                                images.push(imgUrl);
                            }
                        }
                    }
                }
                
                // 生成图片 HTML
                var imageHTML = '';
                if (images.length > 0) {
                    if (images.length === 1) {
                        // 单张图片:使用独立样式,限制最大宽度和高度
                        imageHTML = '<div class="resimg single" view-image>';
                        imageHTML += '<figure class="gallery-thumbnail"><img class="thumbnail-image" src="' + images[0] + '" alt="图片"></figure>';
                        imageHTML += '</div>';
                    } else {
                        // 多张图片:使用网格布局
                        var gridClass = 'grid';
                        if (images.length === 2) gridClass = 'grid-2';
                        else if (images.length === 4) gridClass = 'grid-4';
                        
                        imageHTML = '<div class="resimg ' + gridClass + '" view-image>';
                        for (var j = 0; j < images.length; j++) {
                            imageHTML += '<figure class="gallery-thumbnail"><img class="thumbnail-image" src="' + images[j] + '" alt="图片"></figure>';
                        }
                        imageHTML += '</div>';
                    }
                }
                
                // 格式化时间
                var timeStr = '';
                if (created) {
                    var date = new Date(created * 1000);
                    var year = date.getFullYear();
                    var month = String(date.getMonth() + 1).padStart(2, '0');
                    var day = String(date.getDate()).padStart(2, '0');
                    var hour = String(date.getHours()).padStart(2, '0');
                    var minute = String(date.getMinutes()).padStart(2, '0');
                    timeStr = year + '/' + month + '/' + day + ' ' + hour + ':' + minute;
                    console.log('时间:', timeStr, 'created:', created);
                }
                
                // 生成标签 HTML
                var tagsHTML = '';
                if (tags.length > 0) {
                    tagsHTML = '<div class="hy-tag">';
                    for (var j = 0; j < tags.length; j++) {
                        tagsHTML += '<span class="hy-tags-item">#' + tags[j] + '</span>';
                    }
                    tagsHTML += '</div>';
                }
                
                // 拼接 HTML（参照 veryjack 结构）
                result += '<li>';
                result += '<div class="bb-div">';
                result += '<div class="datatime">';
                result += '<img class="hy-avatar" src="' + bbMemo.useravatar + '" alt="' + bbMemo.username + '">';
                result += '<div class="hy-intro">';
                result += '<div class="hy-name">' + bbMemo.username + '</div>';
                result += '<div class="hy-time"><span class="hy-text-muted">' + timeStr + '</span></div>';
                result += '</div>';
                result += '</div>';
                result += '<div class="datacont">' + content + imageHTML + tagsHTML + '</div>';
                result += '</div>';
                result += '</li>';
            }

            // 追加到容器
            var ul = bbDom.querySelector('ul');
            if (!ul) {
                ul = document.createElement('ul');
                bbDom.appendChild(ul);
            }
            ul.insertAdjacentHTML('beforeend', result);

            // 初始化时间格式化（需要 jQuery）
            if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.lately !== 'undefined') {
                window.jQuery.lately({
                    'target': '.hy-text-muted'
                });
            }
        }

        // 提取标签
        function extractTags(content) {
            var tags = [];
            var tagReg = /#([^\s#]+?) /g;
            var match;
            while ((match = tagReg.exec(content)) !== null) {
                tags.push(match[1]);
            }
            return tags;
        }
    });
})();
