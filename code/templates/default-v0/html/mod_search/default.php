<?php /** $Id$ */
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<form action="index.php" method="post">
	<div class="search<?php if ( $button == 1 ) { ?> button-active<?php } ?>">
		<?php
		    $output = '<input name="searchword" id="mod_search_searchword" maxlength="'.$maxlength.'" alt="'.$button_text.'" class="search-input" type="text" size="'.$width.'" value="'.$text.'"  onblur="if(this.value==\'\') this.value=\''.$text.'\';" onfocus="if(this.value==\''.$text.'\') this.value=\'\';" />';

			echo $output;
		?>
		<button id="searchsubmit" class="search-button" type="submit"></button>
	</div>
	<input type="hidden" name="task"   value="search" />
	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
</form>