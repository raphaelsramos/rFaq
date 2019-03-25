<?php

	/***
	 *	Updated: 2018-06-13
	 */

	$_CPT = get_post_type();
	$_ID = get_the_ID();
	
?>					<div class="faq-item" id="<?php echo sanitize_title( get_the_title() ) ?>">
						<h4 class="faq-question"><?php the_title() ?></h4>
						<div class="faq-answer">
<?php the_content() ?>
						</div>
					</div>
					<!-- /.faq-item -->
