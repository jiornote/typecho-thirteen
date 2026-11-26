(function () {
    var css = '#img-lightbox{border:0;padding:0;background:transparent;width:100%;height:100%;max-width:100vw;max-height:100vh;overflow:hidden}' +
        '#img-lightbox::backdrop{background:rgba(0,0,0,.9)}' +
        '#img-lightbox img{position:absolute;inset:0;margin:auto;max-width:92vw;max-height:88vh;object-fit:contain}' +
        '#img-lightbox .lb-btn{position:absolute;z-index:1;border:0;background:rgba(255,255,255,.12);color:#fff;width:44px;height:44px;border-radius:50%;font-size:24px;line-height:1;cursor:pointer}' +
        '#img-lightbox .lb-btn:hover{background:rgba(255,255,255,.28)}' +
        '#img-lightbox .lb-close{top:16px;right:16px}' +
        '#img-lightbox .lb-prev{left:16px;top:50%;transform:translateY(-50%)}' +
        '#img-lightbox .lb-next{right:16px;top:50%;transform:translateY(-50%)}';

    var html = '<dialog id="img-lightbox" aria-label="图片预览">' +
        '<img id="img-lightbox-image" src="" alt="">' +
        '<button type="button" class="lb-btn lb-close" aria-label="关闭">×</button>' +
        '<button type="button" class="lb-btn lb-prev" aria-label="上一张">‹</button>' +
        '<button type="button" class="lb-btn lb-next" aria-label="下一张">›</button>' +
        '</dialog>';

    var lb, lbImg, images = [], idx = 0;

    function show(i) {
        idx = (i + images.length) % images.length;
        lbImg.src = images[idx].src;
        lb.querySelector('.lb-prev').hidden =
        lb.querySelector('.lb-next').hidden = images.length < 2;
    }

    function ensure() {
        if (lb) return;
        var style = document.createElement('style');
        style.textContent = css;
        document.head.appendChild(style);
        document.body.insertAdjacentHTML('beforeend', html);
        lb = document.getElementById('img-lightbox');
        lbImg = document.getElementById('img-lightbox-image');
        lb.querySelector('.lb-close').addEventListener('click', function () { lb.close(); });
        lb.querySelector('.lb-prev').addEventListener('click', function () { show(idx - 1); });
        lb.querySelector('.lb-next').addEventListener('click', function () { show(idx + 1); });
        lb.addEventListener('click', function (e) { if (e.target === lb) lb.close(); });
        lb.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') show(idx - 1);
            if (e.key === 'ArrowRight') show(idx + 1);
        });
    }

    // 事件委托：动态加载的说说图片也能点击，无需重新初始化
    document.addEventListener('click', function (e) {
        var img = e.target.closest ? e.target.closest('img') : null;
        if (!img) return;
        var scope = img.closest('[view-image]') || img.closest('.post-content');
        if (!scope) return;
        ensure();
        images = Array.prototype.slice.call(scope.querySelectorAll('img'));
        show(images.indexOf(img));
        if (!lb.open) lb.showModal();
    });
})();