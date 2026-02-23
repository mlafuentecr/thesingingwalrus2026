<?php

$child_terms = get_terms( array(
    'taxonomy' => 'topics',
    'hide_empty' => false,
    'exclude' => 38,
) );
//$child_terms = get_terms( $current_term->taxonomy, $args );
?>
<div class="song-sidebar-bg">
<ul class="song-icons">
<?php foreach ($child_terms as $term) { ?>
<li>
<?php
$icon = get_field('song_category_icon', $term->taxonomy . '_' . $term->term_id);
	if(!empty(wp_get_attachment_url($icon))){ ?>
		<img src="<?php echo wp_get_attachment_url($icon); ?>" class="icona" alt="" />
	<?php }else{ ?>
		<img src="<?php echo $icon; ?>" class="icona" alt="" />
	<?php }
?>


<p><a href="<?php echo get_term_link( $term->name, $term->taxonomy ); ?>"><?php echo $term->name; ?></a></p>
</li>

<?php } ?>
</ul>
</div>