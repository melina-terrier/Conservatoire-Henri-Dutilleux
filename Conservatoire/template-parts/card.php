<?php
  $date_formatted = crdtheme_format_event_date();

  $heading_tag = isset( $args['heading'] ) && in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true )
    ? $args['heading']
    : 'h3';
?>

<article class="card">
  <?php if( has_post_thumbnail() ): ?>
    <div class="duotone card__img">
      <?php the_post_thumbnail( 'large' ); ?>
    </div>
  <?php endif; ?>

  <<?php echo $heading_tag; ?> class="card__title">
    <a class="card__titleLink" href="<?php the_permalink(); ?>">
      <?php the_title(); ?>
    </a>
  </<?php echo $heading_tag; ?>>
  
  <div class="card__caption">
    <?php if( $date_formatted ): ?>
      <p class="card__date"><?php echo esc_html( $date_formatted ); ?></p>
    <?php endif; ?>
    <?php if( has_excerpt() ): ?>
      <p class="card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?></p>
    <?php endif; ?>
    <a class="card__link morelink" href="<?php the_permalink(); ?>" aria-label="Plus d'info sur <?php the_title_attribute(); ?>">
      Plus d'info
    </a>
  </div>
</article>