<? /** $Id$ */ ?>
<? defined('_JEXEC') or die('Restricted access'); ?>

<div class="language_select">
	<form action="<?= @$uri->toString(array('path', 'query'))?>" method="post">	
		<? if('flag' == @$display_flag || 'both' == @$display_flag) : ?>
			<?= @helper('nooku.flag.image', @$current_lang); ?>
		<? endif; ?>
		<?= @helper('select.genericlist',  @$languages->toArray(), 'lang', 'onchange="this.form.submit();"', 'iso_code', @$langformat, @$current_lang->iso_code, 'language-select' ); ?>
	</form>
</div>