<?php
/**
 * Thirteen - 评论模板
 * 
 * @package Thirteen
 * @author Your Name
 * @version 1.0.0
 * @link http://example.com
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 自定义评论显示函数
function threadedComments($comments, $options) {
    $commentClass = 'comment-item';
    if ($comments->levels > 0) {
        $commentClass .= ' comment-child';
    } else {
        $commentClass .= ' comment-parent';
    }
    $isAuthor = ($comments->authorId == $comments->ownerId && $comments->authorId > 0);
    
    // 获取父评论的作者名称
    $parentAuthor = '';
    if ($comments->levels > 0 && $comments->parent > 0) {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $parentComment = $db->fetchRow($db->select('author')->from($prefix . 'comments')->where('coid = ?', $comments->parent));
        if ($parentComment) {
            $parentAuthor = $parentComment['author'];
        }
    }
    
    // 头像：QQ 邮箱直接取 QQ 头像，其余走 Cravatar；QQ 头像加载失败回落到 Cravatar
    $mail = strtolower(trim((string) $comments->mail));
    $authorUid = intval($comments->authorId);
    if ($authorUid > 0) {
        // 已登录作者的资料可能已更新（换邮箱/换头像），取用户表当前邮箱
        $db = Typecho_Db::get();
        $authorRow = $db->fetchRow($db->select('mail')->from($db->getPrefix() . 'users')->where('uid = ?', $authorUid));
        if ($authorRow && !empty($authorRow['mail'])) {
            $mail = strtolower(trim($authorRow['mail']));
        }
    }
    $avatarFallback = 'https://cravatar.cn/avatar/' . ($mail !== '' ? md5($mail) : '') . '?s=80&d=mp&r=g';
    $avatarUrl = $avatarFallback;
    if (preg_match('/^(\d{5,11})@(qq|vip\.qq|foxmail)\.com$/', $mail, $mailMatch)) {
        $avatarUrl = 'https://q1.qlogo.cn/g?b=qq&nk=' . $mailMatch[1] . '&s=100';
    }
    
    // 时间格式化
    $commentTime = $comments->created;
    $fullDate = date('M d, Y', $commentTime);
?>
    <li id="<?php $comments->theId(); ?>" class="<?php echo $commentClass; ?>">
        <div class="comment-author">
            <img src="<?php echo $avatarUrl; ?>" alt="avatar" class="comment-avatar" width="40" height="40" onerror="this.onerror=null;this.src='<?php echo $avatarFallback; ?>'" />
            <div class="comment-author-info">
                <span class="comment-author-name"><?php $comments->author(); ?></span>
                <?php if ($comments->status == 'waiting'): ?>
                    <span class="comment-status">(待审核)</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="comment-content">
            <?php if ($comments->levels > 0 && $parentAuthor): ?>
                <span class="reply-to-author">@<?php echo $parentAuthor; ?></span>
            <?php endif; ?>
            <?php $comments->content(); ?>
        </div>
        <div class="comment-meta">
            <span class="comment-reply"><?php $comments->reply('REPLY'); ?></span>
            <span class="comment-date"> · 发布于 <?php echo $fullDate; ?></span>
        </div>
        <div style="clear: both; border-bottom: 1px solid #eee;"></div>
        <?php if ($comments->children): ?>
        <div class="comment-children">
            <?php $comments->threadedComments($options); ?>
        </div>
        <?php endif; ?>
    </li>
<?php
}
?>

<?php if ($this->allow('comment')): ?>
<div id="comments" class="comments-area">
    <?php $this->comments()->to($comments); ?>
    
    <!-- 1. Responses 标题 -->
    <h3 class="comments-title">Responses</h3>
    
    <!-- 2. 已经评论的内容 -->
    <?php if ($comments->have()): ?>
        <ol class="comment-list" id="commentList">
            <?php $comments->listComments(); ?>
        </ol>
        
        <div class="load-more-container" id="loadMoreContainer" style="text-align: center; margin-top: 30px; display: none;">
            <button type="button" id="loadMoreBtn" class="load-more-btn" data-total="<?php echo $comments->getTotal(); ?>">
                Load More
            </button>
        </div>
    <?php endif; ?>
    
    <!-- 3. 评论表单 -->
    <div id="<?php $this->respondId(); ?>" class="comment-respond">
        <h3 class="respond-title">发表留言</h3>
        <p class="reply-notice">人生在世，错别字在所难免，无需纠正。</p>
        <form method="post" action="<?php $this->commentUrl() ?>" id="commentForm" role="form">
            <?php if($this->user->hasLogin()): ?>
                <p class="login-notice">已登录: <a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>. <a href="<?php $this->options->logoutUrl(); ?>" title="Logout">退出 &raquo;</a></p>
            <?php else: ?>
                <div class="form-group">
                    <label for="author">昵称</label>
                    <input type="text" name="author" id="author" class="form-input" value="<?php $this->remember('author'); ?>" required />
                </div>
                <div class="form-group">
                    <label for="mail">邮箱</label>
                    <input type="email" name="mail" id="mail" class="form-input" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?> />
                </div>
                <div class="form-group">
                    <label for="url">网址</label>
                    <input type="url" name="url" id="url" class="form-input" value="<?php $this->remember('url'); ?>" />
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="textarea">评论</label>
                <textarea rows="6" name="text" id="textarea" class="form-textarea" required ><?php $this->remember('text'); ?></textarea>
            </div>
            
            <div class="form-submit">
                <button type="submit" class="submit-btn">Post Comment</button>
                <div class="cancel-comment-reply">
                    <?php $comments->cancelReply('Click here to cancel reply.'); ?>
                </div>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="comments-closed">
    <p>评论已关闭</p>
</div>
<?php endif; ?>
