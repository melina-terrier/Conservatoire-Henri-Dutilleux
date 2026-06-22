<div class="mainColumn">

	<h2 class="pageTitle">Rien n'a été trouvé</h2>

	<?php if ( is_search() ) : ?>
		<p>Désolé, mais rien ne correspond à vos termes de recherche. Veuillez réessayer avec d'autres mots-clés.</p>
	<?php else : ?>
		<p>Il semble que nous ne pouvons pas trouver ce que vous recherchez. Peut-être une recherche peut vous aider.</p>
	<?php endif; ?>

	<div class="searchFormPage">
		<form class="searchFormPage__form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
			<label class="sr-only" for="pageSearchForm">Rechercher</label>
			<input class="searchFormPage__input" id="pageSearchForm" type="search" name="s" placeholder="Rechercher…" value="<?php echo esc_attr( get_search_query() ); ?>">
			<button class="searchFormPage__submit btn -dark" type="submit">
				Rechercher
			</button>
		</form>
	</div>
</div>
