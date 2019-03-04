<{if $enable_comments_form}>
<div class="comments-form">
<form name="rmc_comment_form" id="rmc-comment-form" method="post" action="<{$cf.actionurl}>">
    <h4><{$cf.lang_title}></h4>
    <{if $cf.show_name}>
    <div class="form-group">
        <label for="comment-name"><{$cf.lang_name}></label>
        <input type="text" name="comment_name" id="comment-name" class="form-control" value="<{$cf.name}>" required>
    </div>
    <{/if}>
    <{if $cf.show_email}>
    <div class="form-group">
        <label for="comment-email"><{$cf.lang_email}></label>
        <input type="text" name="comment_email" id="comment-email" class="form-control email" value="<{$cf.email}>" required>
    </div>
    <{/if}>
    <{if $cf.show_url}>
    <div class="form-group">
        <label for="comment-url"><{$cf.lang_url}></label>
        <input type="text" name="comment_url" id="comment-url" class="form-control url" value="<{$cf.url}>">
    </div>
    <{/if}>

    <div class="form-group">
        <label for="comment-text"><{$cf.lang_text}></label>
        <textarea name="comment_text" id="comment-text" class="form-control" rows="4" required><{$cf.text}></textarea>
    </div>

    <{foreach item=field from=$cf.fields}>
    <{$field}>
    <{/foreach}>
    <br>
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg"><{$cf.lang_submit}></button>
    </div>
    <input type="hidden" name="uri" value="<{$cf.uri}>">
    <input type="hidden" name="params" value="<{$cf.params}>">
    <input type="hidden" name="type" value="<{$cf.type}>">
    <input type="hidden" name="object" value="<{$cf.object}>">
    <input type="hidden" name="action" value="<{$cf.action}>">
    <input type="hidden" name="update" value="<{$cf.update}>">

</form>
</div>
<{/if}>