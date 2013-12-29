<h1 class="cu-section-title"><?php _e('Comments Manager','rmcommon'); ?></h1>

<div class="row comm-pagination">
    <div class="col-md-6 col-lg-6">
        <?php $nav->display(false, true); ?>
    </div>
    <div class="col-md-6 col-lg-6">
        <form name="search_form" class="pull-right" method="get" action="comments.php">
            <div class="comm-search-control">
                <div class="input-group">
                    <input type="search" name="w" id="wsearch" value="<?php echo isset($keyw) ? $keyw : '' ?>" size="20" class="form-control input-sm" />
                <span class="input-group-btn">
                    <button type="button" title="<?php _e('Search','rmcommon'); ?>" onclick="$('#wsearch').val()==''?alert('<?php _e('You need something to search!','rmcommon'); ?>'):submit();" class="btn btn-info btn-sm">
                        <i class="fa-search"></i>
                    </button>
                </span>
                </div>
            </div>

            <input type="hidden" name="action" value="">
        </form>
    </div>
</div>

<form name="list_comments" method="post" action="comments.php" id="list-comments" class="form-inline">
<div class="row-fluid cu-bulk-actions">

	<select name="action" id="action-select" onchange="$('#action-select2').val($(this).val());" class="form-control">
	    <option value="" selected="selected"><?php _e('Bulk Actions...','rmcommon'); ?></option>
	    <option value="unapprove"><?php _e('Set unapproved','rmcommon'); ?></option>
	    <option value="approve"><?php _e('Set approved','rmcommon'); ?></option>
	    <option value="spam"><?php _e('Mark as SPAM','rmcommon'); ?></option>
	    <option value="delete"><?php _e('Delete comments','rmcommon'); ?></option>
	</select>
	<button type="submit" class="btn btn-default" onclick="if($('#action-select').val()=='delete') return confirm('Do you really want to delete selected comments?');"><?php _e('Apply','rmcommon'); ?></button>

	<ul class="nav nav-pills pull-right">
		<li<?php if($filter==''): ?> class="active"<?php endif; ?>>
			<a href="comments.php"><?php _e('View all','rmcommon'); ?></a>
		</li>
		<li<?php if($filter=='waiting'): ?> class="active"<?php endif; ?>>
			<a href="comments.php?filter=waiting"><?php _e('Unapproved','rmcommon'); ?></a>
		</li>
		<li<?php if($filter=='approved'): ?> class="active"<?php endif; ?>>
			<a href="comments.php?filter=approved"><?php _e('Approved','rmcommon'); ?></a>
		</li>
		<li<?php if($filter=='spam'): ?> class="active"<?php endif; ?>>
			<a href="comments.php?filter=spam"><?php _e('SPAM','rmcommon'); ?></a>
		</li>
	</ul>
</div>
<table class="outer" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th width="20"><input type="checkbox" id="checkall" value="" onclick="$('#list-comments').toggleCheckboxes(':not(#checkall)');" /></th>
        <th align="left"><?php _e('Author','rmcommon'); ?></th>
        <th align="left"><?php _e('Comment','rmcommon'); ?></th>
        <th><?php _e('Status','rmcommon'); ?></th>
        <th><?php _e('Module','docs'); ?></th>
        <th nowrap="nowrap"><?php _e('In reply to','rmcommon'); ?></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th width="20"><input type="checkbox" id="checkall2" value="" onclick="$('#list-comments').toggleCheckboxes(':not(#checkall2)');" /></th>
        <th align="left"><?php _e('Author','rmcommon'); ?></th>
        <th align="left"><?php _e('Comment','rmcommon'); ?></th>
        <th><?php _e('Status','rmcommon'); ?></th>
        <th><?php _e('Module','docs'); ?></th>
        <th nowrap="nowrap"><?php _e('In reply to','rmcommon'); ?></th>
    </tr>
    </tfoot>
    <tbody>
    <?php if(count($comments)<=0): ?>
    <tr class="head">
        <td colspan="6" align="center"><span class="text-error"><?php _e('There are not comments yet!','rmcommon'); ?></span></td>
    </tr>
    <?php else: ?>
    <?php foreach ($comments as $com): ?>
    <tr class="<?php echo tpl_cycle("even,odd"); ?>" valign="top"<?php if($com['status']=='spam'): ?> style="color: #F00;"<?php endif; ?>>
        <td align="center"><input type="checkbox" name="coms[]" id="com-<?php echo $com['id']; ?>" value="<?php echo $com['id']; ?>" /></td>
        <td class="poster_cell"><img class="poster_avatar" src="<?php echo $com['poster']['avatar']; ?>" />
        <strong><?php echo $com['poster']['name']; ?></strong>
        <span class="poster_data"><a href="mailto:<?php echo $com['poster']['email']; ?>"><?php echo $com['poster']['email']; ?></a><br />
        <?php echo $com['ip']; ?></span></td>
        <td><span class="comment_date"><?php echo $com['posted']; ?></span>
        <?php echo $com['text']; ?>
        <span class="rmc_options">
        	<a href="comments.php?id=<?php echo $com['id']; ?>&amp;action=edit&amp;page=<?php echo $page; ?>&amp;filter=<?php echo $filter; ?>&amp;w=<?php echo $keyw; ?>"><?php _e('Edit','rmcommon'); ?></a> | 
        	<a href="javascript:;" onclick="confirm_delete(<?php echo $com['id']; ?>);"><?php _e('Delete','rmcommon'); ?></a> | 
        	<?php if($com['status']=='approved'): ?>
        	<a href="javascript:;" onclick="approve_action(<?php echo $com['id']; ?>,'unapprove');"><?php _e('Unnaprove','rmcommon'); ?></a>
        	<?php else: ?>
        	<a href="javascript:;" onclick="approve_action(<?php echo $com['id']; ?>,'approve');"><?php _e('Approve','rmcommon'); ?></a>
        	<?php endif; ?>
        	<?php if($com['status']!='spam'): ?>
        	| <a href="javascript:;" onclick="approve_action(<?php echo $com['id']; ?>,'spam');"><?php _e('Spam','rmcommon'); ?></a>
        	<?php endif; ?>
        </span>
        </td>
        <td align="center">
        	<?php 
        		switch($com['status']){
					case 'approved':
						_e('Approved', 'rmcommon');
						break;
					case 'waiting':
						_e('Unapproved','rmcommon');
						break;
					case 'spam':
						echo "<span style='color: #F00;'>";
						_e('SPAM', 'rmcommon');
						echo "</span>";
						break;
        		}
        	?>
        </td>
        <td align="center"><?php echo $com['module']; ?></td>
        <td align="center">
        	<?php if(isset($com['item'])): ?><a href="<?php echo $com['item_url']; ?>"><?php echo $com['item']; ?></a><?php else: echo "&nbsp;"; endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<div class="cu-bulk-actions">
<?php $nav->display(false, true); ?>
<select name="actionb" id="action-select2" onchange="$('#action-select').val($(this).val());" class="form-control">
    <option value="" selected="selected"><?php _e('Bulk Actions...','rmcommon'); ?></option>
    <option value="unapprove"><?php _e('Set unapproved','rmcommon'); ?></option>
    <option value="approve"><?php _e('Set approved','rmcommon'); ?></option>
    <option value="delete"><?php _e('Delete comments','rmcommon'); ?></option>
</select>
<button type="submit" onclick="if($('#action-select').val()=='delete') return confirm('Do you really want to delete selected comments?');" class="btn btn-default"><?php _e('Apply','rmcommon'); ?></button>
</div>
<input type="hidden" name="filter" value="<?php echo $filter; ?>" />
<input type="hidden" name="w" value="<?php echo $keyw; ?>" />
<input type="hidden" name="page" value="<?php echo $page; ?>" />
<?php echo $xoopsSecurity->getTokenHTML(); ?>
</form>