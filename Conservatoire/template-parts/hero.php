<?php 
$hero = function_exists('get_field') ? get_field('header_default', 'header_archives') : [];
$hero_agenda = function_exists('get_field') ? get_field('header_archive_agenda', 'header_archives') : [];

global $post;
$post_id = $post->ID ?? 0;

// Taxonomies liées au CPT agenda
$agenda_taxonomies = array( 'location', 'cat_agenda' );
$is_agenda_tax = is_tax( $agenda_taxonomies );

// Déterminer l'image et le chapo
if ( ( is_post_type_archive( 'agenda' ) || $is_agenda_tax ) && is_array($hero_agenda) ) {
    $img_archive   = $hero_agenda['img_header_archive'] ?? null;
    $chapo_archive = $hero_agenda['desc_header_archive'] ?? '';
} else {
    $img_archive   = $hero['img_header_default'] ?? null;
    $chapo_archive = $hero['chapo_header_default'] ?? '';
}

// Déterminer l'image, le titre et le chapo
$img      = '';
$title    = '';
$subtitle = '';
$chapo    = '';

$default_img = ( is_array($hero) && !empty($hero['img_header_default']) )
    ? wp_get_attachment_image( $hero['img_header_default'], 'full' )
    : '';

if ( is_post_type_archive() || is_tax() || is_search() ) {
    $img = $img_archive
        ? wp_get_attachment_image( $img_archive, 'full' )
        : $default_img;
} elseif ( is_front_page() || is_singular() || is_page() ) {
    $img = get_the_post_thumbnail( $post_id, 'full' ) ?: $default_img;
} else {
    $img = $default_img;
}

if ( is_post_type_archive() ) {
    $title = post_type_archive_title( '', false );
    $chapo = $chapo_archive;
} elseif ( is_tax() ) {
    $term = get_queried_object();
    if ( $term && isset($term->taxonomy) ) {
        $tax   = get_taxonomy( $term->taxonomy );
        $title = single_term_title( '', false );
    }
    $chapo = term_description();
} elseif ( is_search() ) {
    $title = __( 'Recherche' );
    $chapo  = sprintf( __( 'Résultats de la recherche pour : %s' ), esc_html( get_query_var( 's' ) ) );
} elseif ( is_front_page() ) {
    $title    = get_bloginfo( 'name' );
    $subtitle = html_entity_decode( get_bloginfo( 'description' ) );
} else {
    $title = get_the_title() ?? '';
    $chapo = get_the_excerpt() ?? '';
}

?>

<div class="grid -fullHeight -withoutMargin hero">

  <header class="hero__header">
    <h1 class="hero__title"><?php echo wp_kses( $title, array( 'br' => array() ) ); ?></h1>

    <?php if ( is_front_page() && $subtitle ) : ?>
      <h3 class="hero__subTitle"><?php echo esc_html( $subtitle ); ?></h3>
    <?php endif; ?>
  </header>

  <div class="hero__chapo chapo <?php echo ( is_front_page() || is_page( 'Enseignements' ) ) ? '-col3' : ''; ?>">
    <?php if ( is_front_page() || is_page( 'Enseignements' ) ) :
        if( function_exists('have_rows') && have_rows( 'menu_items', 'header_enseignements' ) ):
            while ( have_rows( 'menu_items', 'header_enseignements' ) ) : the_row();
                $itemMenuSvg  = get_sub_field( 'menu_item_icon' );
                $itemMenuTxt  = get_sub_field( 'menu_item_label' );
                $itemMenuLink = get_sub_field( 'menu_item_url' );
                ?>
                <h3 class="chapo__title">
                  <a class="chapo__link" href="<?php echo esc_url($itemMenuLink); ?>">
                    <?php if ( $itemMenuSvg && isset($itemMenuSvg['url']) ): ?>
                      <img class="style-svg" src="<?php echo esc_url($itemMenuSvg['url']); ?>" alt="Icone <?php echo esc_attr($itemMenuTxt); ?>">
                    <?php endif; ?>
                    <?php echo esc_html($itemMenuTxt); ?>
                  </a>
                </h3>
            <?php endwhile;
        endif;
    else :
        if ( $chapo ) echo '<p class="chapo__text">' . esc_html($chapo) . '</p>';
    endif; ?>
  </div>

  <div class="duotone hero__img"><?php echo $img; ?></div>

  <?php if ( is_front_page() ) : ?>
    <div class="hero__patterns rellax" data-rellax-speed="-5">
      <svg viewBox="0 0 1260 700" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g class="curveLines">
            <path d="M714.82,146.75c-45.82,0-83-40.88-83-91.32A97.76,97.76,0,0,1,644.1,7.66" />
            <path d="M983.41,153.39a32.38,32.38,0,1,0,0-64.75h-37" />
            <path d="M826.91,15.58H976.29c56.85,0,102.94,46.28,102.94,103.36S1033.14,222.3,976.29,222.3H931.34" />
            <path d="M928.58,334H1016c50.33,0,92.48,39.34,103.49,92.16" />
            <path d="M847.66,174.15a26.57,26.57,0,1,0-26.56-26.57A26.56,26.56,0,0,0,847.66,174.15Z" />
            <path d="M847.66,222.3c42.17,0,76.36-33.64,76.36-75.13S889.83,72,847.66,72s-76.36,33.64-76.36,75.14" />
            <path d="M165.4,185.77a74.7,74.7,0,1,1-149.4,0" />
            <path d="M798.69,223.13H725.58A83.8,83.8,0,0,0,641.82,307" />
            <path d="M871.31,384.19a32.38,32.38,0,1,0,32.79,32.38V384.19" />
            <path d="M1071.76,537.57c0,46.43,38.08,84.06,85.07,84.06s85.08-37.63,85.08-84.06V447.29" />
            <path d="M1027.45,576.8h-4.22a43.59,43.59,0,1,1,0-87.17H1156a43.59,43.59,0,1,1,0,87.17h-1.3" />
            <path d="M946.43,384.19H1000A64.93,64.93,0,0,1,1065.11,449" />
            <path d="M418.62,338.11a34.07,34.07,0,1,1-34.07-34.45H494.08" />
            <path d="M505.7,226.45H469.75a47.19,47.19,0,0,0-47.05,47.32" />
            <path d="M537.65,190.75V303.66" />
            <path d="M743.91,375.18v10.95a28.12,28.12,0,0,1-56.24,0v-9.07a27.91,27.91,0,0,0-55.81,0v11.16" />
            <path d="M820.68,685.55a34,34,0,1,0-34.44-34A34.25,34.25,0,0,0,820.68,685.55Z" />
            <path d="M536.8,96.94q0,12.45.05,24.91" />
            <path d="M536.8,49.62q0,12.45.05,24.91" />
            <path d="M809.87,493q0,12.45,0,24.9" />
            <path d="M868.8,493q0,12.45,0,24.9" />
            <path d="M927.73,493q0,12.45,0,24.9" />
          </g>
        </svg>
    </div>
  <?php endif; ?>

</div>
