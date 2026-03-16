<div class="main-column">

	<h2 class="page-title">Rien n'a été trouvé</h2>

	<?php
	if ( is_search() ) {
		echo '<p>Désolé, mais rien ne correspond à vos termes de recherche. Veuillez réessayer avec d\'autres mots-clés.</p>';
	} else {
		echo '<p>Il semble que nous ne pouvons pas trouver ce que vous recherchez. Peut-être une recherche peut vous aider.</p>';
	} 
    ?>

	<div class="searchFormPage">
		<form class="searchFormPage__form" action="<?php echo esc_url( site_url() ); ?>" method="get">
			<label class="sr-only" for="searchForm">Rechercher</label>
			<input class="searchFormPage__input" type="search" name="s" placeholder="Rechercher…" value="<?php the_search_query(); ?>">
			<button class="searchFormPage__submit btn -dark" type="submit">
				Rechercher
			</button>
		</form>
	</div>
</div>