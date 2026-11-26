<?php
/**
 * 说说
 * 
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div class="memos-page">
    <!-- 动态列表容器 -->
    <div id="bber" class="bb-timeline"></div>
</div>

<!-- 本地引用 JS/CSS -->
<script src="<?php $this->options->themeUrl('assets/js/jquery.min.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/marked.min.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/lately.min.js'); ?>"></script>

<!-- Memos 配置 -->
<script>
var bbMemo = {
    memos: '<?php echo Helper::options()->index; ?>?action=memos-proxy&',
    fileProxy: '<?php echo Helper::options()->index; ?>?action=memos-file-proxy&path=',
    limit: '10',                            // 每次加载数量
    creatorId: '1',                         // 用户 ID(通常为 1)
    domId: '#bber',
    username: "小帅",                       // 修改为你自己的昵称
    useravatar: "https://www.jinote.cn/images/tx.webp",  // 修改为你的头像链接
    userlink: "<?php $this->options->siteUrl(); ?>",
    tags: ""
}
</script>

<!-- Memos 渲染脚本 -->
<script src="<?php $this->options->themeUrl('assets/js/memos.js'); ?>"></script>

<!-- 图片灯箱：原生 <dialog> 实现 -->
<script src="<?php $this->options->themeUrl('assets/js/lightbox.js'); ?>"></script>

<?php $this->need('footer.php'); ?>