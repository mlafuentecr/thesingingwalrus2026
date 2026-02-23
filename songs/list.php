<?php

$child_terms = get_terms( array(
    'taxonomy' => 'topics',
    'hide_empty' => false,
    'exclude' => 38,
) );
//$child_terms = get_terms( $current_term->taxonomy, $args );
?>

<div class="songs-list-items">
<div class="x-row x-container max width">
<?php foreach ($child_terms as $term) { ?>

<div class="topic-lists">
<div class="topic-list">
	<?php
	$icon = get_field('song_category_image', $term->taxonomy . '_' . $term->term_id);
	?>
    <a href="<?php echo get_term_link( $term->name, $term->taxonomy ); ?>">
    <img src="<?php echo $icon; ?>" class="icona" alt="" />
    <h3><?php echo $term->name; ?></h3></a>
</div>
</div>

<?php } ?>
</div>
</div>


