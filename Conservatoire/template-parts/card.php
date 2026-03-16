<?php
  $date = get_field_object('event_date');
  $date_formatted = '';
  if ( $date && $date['value'] ) {
    $timestamp = DateTime::createFromFormat('d/m/Y G:i', $date['value']);
    if ( $timestamp ) {
      $date_formatted = date_i18n('j F \à G\hi', $timestamp->getTimestamp());
    }
  }

?>

<article class="card">
  <?php if( has_post_thumbnail() ): ?>
    <div class="duotone card__img">
      <?php the_post_thumbnail( 'large' ); ?>
    </div>
  <?php endif; ?>

  <h3 class="card__title">
    <a class="card__titleLink" href="<?php the_permalink(); ?>">
      <?php the_title(); ?>      
    </a>
  </h3>
  
  <div class="card__caption">
    <?php if( $date_formatted ): ?>
      <p class="card__date"><?php echo esc_html( $date_formatted ); ?></p>
    <?php endif; ?>
    <?php if( has_excerpt() ): ?>
      <p class="card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?></p>
    <?php endif; ?>
    <a class="card__link morelink" href="<?php the_permalink(); ?>">
      <?php echo esc_html__( "Plus d'info" )?>
    </a>
  </div>
</article>