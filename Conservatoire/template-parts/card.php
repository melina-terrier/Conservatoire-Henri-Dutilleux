<?php
  $date = get_field_object('event_date');
  $date_formatted = '';
  if ( $date && ! empty( $date['value'] ) ) {
    $timestamp = DateTime::createFromFormat( 'Y-m-d H:i:s', $date['value'] )
              ?: DateTime::createFromFormat( 'd/m/Y G:i', $date['value'] );
    if ( $timestamp ) {
      $date_format = get_option( 'date_format' ) . ' à ' . get_option( 'time_format' );
      $date_formatted = wp_date( $date_format, $timestamp->getTimestamp() );
    }
  }

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
    <a class="card__link morelink" href="<?php the_permalink(); ?>">
      Plus d'info
    </a>
  </div>
</article>