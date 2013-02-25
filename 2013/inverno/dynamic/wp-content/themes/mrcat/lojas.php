<?php 
/*
Template name: Lojas
*/
$data = get_lojas();
?>
<?php get_header(); ?>
	<div id="body">
<?php 	while( have_posts() ) : the_post(); ?>
		<article id="content" <?php post_class(); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<form action="<?php the_permalink(); ?>" method="get" id="filter">
				<fieldset>
					<legend>Filtro</legend>
					<p class="field field-estado">
						<label for="estado">Estado</label><br>
						<select name="estado" id="estado">
							<option value="">Selecione o estado</option>
<?php 						foreach( $data as $estado => $cidades ) : ?>
							<option value="<?php echo trim( $estado ); ?>"><?php echo trim( $estado ); ?></option>
<?php 						endforeach; ?>
						</select>
					</p>
					<p class="field field-cidade">
						<label for="cidade">Cidade</label><br>
						<select name="cidade" id="cidade">
							<option value="">Selecione a cidade</option>
<?php 						foreach( $data as $estado => $cidades ) : ?>
							<optgroup label="<?php echo trim( $estado ); ?>">
<?php 							foreach( $cidades as $cidade => $bairros ) : ?>
								<option value="<?php 
								echo trim( $cidade ); ?>" data-val="<?php 
								echo sanitize_title( "{$estado}-{$cidade}" ); ?>"><?php 
								echo trim( $cidade ); ?></option>
<?php 							endforeach; ?>
							</optgroup>
<?php 						endforeach; ?>
						</select>
					</p>
					<p class="field field-bairro">
						<label for="bairro">Bairro</label><br>
						<select name="bairro" id="bairro">
							<option value="">Selecione o bairro</option>
<?php 						foreach( $data as $estado => $cidades ) :
								foreach( $cidades as $cidade => $bairros ) : ?>
								<optgroup label="<?php echo trim( $cidade ); ?>" data-label="<?php 
								echo sanitize_title( "{$estado}-{$cidade}" ); ?>">
<?php 								foreach( $bairros as $bairro => $lojas ) : ?>
									<option value="<?php 
									echo trim( $bairro ); ?>" data-val="<?php 
									echo sanitize_title( "{$estado}-{$cidade}-{$bairro}" ); ?>"><?php 
									echo trim( $bairro ); ?></option>
<?php 								endforeach; ?>
								</optgroup>
<?php 							endforeach;
							endforeach; ?>
						</select>
					</p>
					<p class="field field-loja">
						<label for="loja">Loja</label><br>
						<select name="loja" id="loja">
							<option value="">Selecione a loja</option>
<?php 						foreach( $data as $estado => $cidades ) :
								foreach( $cidades as $cidade => $bairros ) :
									foreach( $bairros as $bairro => $lojas ) : ?>
									<optgroup label="<?php echo trim( $bairro ); ?>" data-label="<?php 
									echo sanitize_title( "{$estado}-{$cidade}-{$bairro}" ); ?>">
<?php 									foreach( $lojas as $loja ) : ?>
										<option value="<?php echo $loja['slug']; ?>"><?php echo trim( $loja['nome'] ); ?></option>
<?php 									endforeach; ?>
									</optgroup>
<?php 								endforeach;
								endforeach;
							endforeach; ?>
						</select>
					</p>
					<p class="field field-submit">
						<button type="submit">Ok</button>
					</p>
				</fieldset>
			</form>
			<div class="entry-content">
<?php 			foreach( $data as $estado => $cidades ) : ?>
				<h2 class="estado-name"><?php echo $estado; ?></h2>
				<ul class="cidades-list">
<?php 				foreach( $cidades as $cidade => $bairros ) : ?>
					<li class="cidade-item">
						<h3 class="cidade-name"><?php echo $cidade; ?></h3>
						<ul class="bairros-list">
<?php 						foreach( $bairros as $bairro => $lojas ) : ?>
							<li class="bairro-item">
								<h4 class="bairro-name"><?php echo $bairro; ?></h4>
								<ul class="lojas-list">
<?php 								foreach( $lojas as $loja ) : ?>
									<li id="<?php echo $loja['slug']; ?>" class="loja-item">
										<h5 class="loja-name"><?php echo $loja['nome']; ?></h5>
										<p class="loja-address"><?php 
										echo str_replace( '[', '<span>', str_replace( ']', '</span>', $loja['endereco'] ) );
										echo "<br>{$bairro}, {$cidade} - " . $loja['uf'];
										echo "<br>Tel: " . $loja['telefone']; ?></p>
									</li>
<?php 								endforeach; ?>
								</ul>
							</li>
<?php 						endforeach; ?>
						</ul>
					</li>
<?php 				endforeach; ?>
				</ul>
<?php 			endforeach; ?>
				<div id="map_canvas"></div>
			</div>
		</article>
<?php 	endwhile; ?>
	</div>
<?php get_footer(); ?>