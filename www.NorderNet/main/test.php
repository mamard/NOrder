<!--Element Liste produit norder-->
<div class="stock_grid_top"></div>
<div class="stock_grid">

	<!--  Image   -->
	<div title="img" class="stock_img">
		<img alt="img art" class="stock_img"  height="40" src="picture/no_picture.png"/>
	</div>

	<!--  Produit   -->
	<div class="stock_product">
	&lsaquo; inconnu &rsaquo;</div>

	<!-- Change -->
	<div class="stock_change"><a href="#change_product_method">
		Changer Produit</a>
	</div>

	<!-- Niveau -->
	<div title="meter" class="stock_meter">
		<div class="styled">
			<meter value="1500"	min="0" low="1000" high="1200" optimum="3000" max="3000"></meter>
		</div>
		<input class="stock_range" type=range value="1000" min=0 max=3000>
	</div>

	<!-- Actuel -->
	<div class="stock_quantity">&lsaquo; ?  &rsaquo;</div>

	<!-- Config -->
	<div class="stock_cfg"><a href="#stock_cfg">cfg</a></div>


</div>
<!-- Add Product -->
	<div class="change_product">
	<!-- Select methode -->
		<form action="" id="change_product_method" class="href_change_product_method">
			<a href="#" class="change_product_close"><em>fermer</em></a>
			<div class="change_product_method_grid">
				<!-- Scan produit -->
				<div class="change_product_method_scan change_product_style_button"><img id="scan_picture" alt="scan"  class="change_product_method_scan" height="40" src="picture/scan2.png"/></div>
				<div class="change_product_method_scan_text">Scanner avec smartphone</div>
				<!-- Ou -->
				<div class="change_product_method_or">ou</div>
				<!-- Ajout manuel -->	
				<a href="#change_product_manual" class="change_product_method_manual change_product_style_button">saisie<br>manuelle</a>
				<div class="change_product_method_manual_text">texte</div>
			</div>
		</form>
	<!--Saisie manuelle-->
		<form action="" id="change_product_manual" class="href_change_product_manual">
			<a href="#" class="change_product_close"><em>fermer</em></a>
			<div class="change_product_manual_grid">
				<!--INPUT-->
				<input list="products" type="text" placeholder="Riz, Café..." autocomplete="off" id="search_products" class="change_product_manual_value" required/>
				<input class="change_product_manual_submit" type="submit" value="ok"/>
				<div class="change_product_manual_text">texte</div>
			</div>
		</form>
	<!--CFG-->
		<form action="" id="stock_cfg" class="href_stock_cfg">
		<a href="#" class="change_product_close"><em>fermer</em></a>
		<div class="stock_cfg_grid">
			<!--  Unit Selectbox -->	
				<label for="stock_unit" class="stock_unit_text">
					Unité
				</label>
				<div class="stock_unit_selectbox">
					<select id="stock_unit" class="stock_unit_selectbox">
						<option value="0">Conditionnement</option>
						<option value="1">kg - Kilo</option>
						<option value="2">g&nbsp; - Gramme</option>
						<!-- si unité=volume
						<option value="1">l&nbsp; - Litre</option>
						<option value="2">dl - Décilitre</option>
						<option value="3">cl - Centilitre</option>
						<option value="4">ml - Mililitre</option>
						-->
					</select>
				</div>
			<!--  Min Selectbox -->	
				<label for="stock_min" class="stock_min_text">
					Minimum
				</label>
				<div class="stock_min_selectbox">
					<input type="number" id="stock_min" value="1000" step="50" min="0" max="3000" class="stock_min_selectbox">
				</div>
			<!--  Max Selectbox -->	
				<label for="stock_max" class="stock_max_text">
					Maximum
				</label>
				<div class="stock_max_selectbox">
					<input type="number" id="stock_max" value="2000" step="50" min="0" max="3000" class="stock_max_selectbox">
				</div>
		</div>
		</form>


<?php /* TEST DEMO DEBUT */ ?>
<!--Element Liste produit norder-->
<div class="stock_grid_top"></div>
<div class="stock_grid">

	<!--  Image   -->
	<div title="img" class="stock_img">
		<img alt="img art" class="stock_img"  height="40" src="picture/no_picture.png"/>
	</div>

	<!--  Produit   -->
	<div class="stock_product">
	&lsaquo; inconnu &rsaquo;</div>

	<!-- Change -->
	<div class="stock_change"><a href="#change_product_method2">
		Changer Produit</a>
	</div>

	<!-- Niveau -->
	<div title="meter" class="stock_meter">
		<div class="styled">
			<meter value="1500"	min="0" low="1000" high="1200" optimum="3000" max="3000"></meter>
		</div>
		<input class="stock_range" type=range value="1000" min=0 max=3000>
	</div>

	<!-- Actuel -->
	<div class="stock_quantity">&lsaquo; ?  &rsaquo;</div>

	<!-- Config -->
	<div class="stock_cfg"><a href="#stock_cfg2">cfg</a></div>


</div>
<!-- Add Product -->
	<div class="change_product">
	<!-- Select methode -->
		<form action="" id="change_product_method2" class="href_change_product_method">
			<a href="#" class="change_product_close"><em>fermer</em></a>
			<div class="change_product_method_grid">
				<!-- Scan produit -->
				<div class="change_product_method_scan change_product_style_button"><img id="scan_picture" alt="scan"  class="change_product_method_scan" height="40" src="picture/scan2.png"/></div>
				<div class="change_product_method_scan_text">Scanner avec smartphone</div>
				<!-- Ou -->
				<div class="change_product_method_or">ou</div>
				<!-- Ajout manuel -->	
				<a href="#change_product_manual2" class="change_product_method_manual change_product_style_button">saisie<br>manuelle</a>
				<div class="change_product_method_manual_text">texte</div>
			</div>
		</form>
	<!--Saisie manuelle-->
		<form action="" id="change_product_manual2" class="href_change_product_manual">
			<a href="#" class="change_product_close"><em>fermer</em></a>
			<div class="change_product_manual_grid">
				<!--INPUT-->
				<input list="products" type="text" placeholder="Riz, Café..." autocomplete="off" id="search_products" class="change_product_manual_value" required/>
				<input class="change_product_manual_submit" type="submit" value="ok"/>
				<div class="change_product_manual_text">texte</div>
			</div>
		</form>
	<!--CFG-->
		<form action="" id="stock_cfg2" class="href_stock_cfg">
		<a href="#" class="change_product_close"><em>fermer</em></a>
		<div class="stock_cfg_grid">
			<!--  Unit Selectbox -->	
				<label for="stock_unit" class="stock_unit_text">
					Unité
				</label>
				<div class="stock_unit_selectbox">
					<select id="stock_unit" class="stock_unit_selectbox">
						<option value="0">Conditionnement</option>
						<option value="1">kg - Kilo</option>
						<option value="2">g&nbsp; - Gramme</option>
						<!-- si unité=volume
						<option value="1">l&nbsp; - Litre</option>
						<option value="2">dl - Décilitre</option>
						<option value="3">cl - Centilitre</option>
						<option value="4">ml - Mililitre</option>
						-->
					</select>
				</div>
			<!--  Min Selectbox -->	
				<label for="stock_min" class="stock_min_text">
					Minimum
				</label>
				<div class="stock_min_selectbox">
					<input type="number" id="stock_min" value="1000" step="50" min="0" max="3000" class="stock_min_selectbox">
				</div>
			<!--  Max Selectbox -->	
				<label for="stock_max" class="stock_max_text">
					Maximum
				</label>
				<div class="stock_max_selectbox">
					<input type="number" id="stock_max" value="2000" step="50" min="0" max="3000" class="stock_max_selectbox">
				</div>
		</div>
		</form>



<!--Element Liste produit norder-->
<div class="stock_grid_top"></div>
<div class="stock_grid">

	<!--  Image   -->
	<div title="img" class="stock_img">
		<img alt="img art" class="stock_img"  height="40" src="picture/no_picture.png"/>
	</div>

	<!--  Produit   -->
	<div class="stock_product">
	&lsaquo; inconnu &rsaquo;</div>

	<!-- Change -->
	<div class="stock_change"><a href="#change_product_method3">
		Changer Produit</a>
	</div>

	<!-- Niveau -->
	<div title="meter" class="stock_meter">
		<div class="styled">
			<meter value="1500"	min="0" low="1000" high="1200" optimum="3000" max="3000"></meter>
		</div>
		<input class="stock_range" type=range value="1000" min=0 max=3000>
	</div>

	<!-- Actuel -->
	<div class="stock_quantity">&lsaquo; ?  &rsaquo;</div>

	<!-- Config -->
	<div class="stock_cfg"><a href="#stock_cfg3">cfg</a></div>


</div>
<!-- Add Product -->
	<div class="change_product">
	<!-- Select methode -->
		<form action="" id="change_product_method3" class="href_change_product_method">
			<a href="#" class="change_product_close"><em>fermer</em></a>
			<div class="change_product_method_grid">
				<!-- Scan produit -->
				<div class="change_product_method_scan change_product_style_button"><img id="scan_picture" alt="scan"  class="change_product_method_scan" height="40" src="picture/scan2.png"/></div>
				<div class="change_product_method_scan_text">Scanner avec smartphone</div>
				<!-- Ou -->
				<div class="change_product_method_or">ou</div>
				<!-- Ajout manuel -->	
				<a href="#change_product_manual3" class="change_product_method_manual change_product_style_button">saisie<br>manuelle</a>
				<div class="change_product_method_manual_text">texte</div>
			</div>
		</form>
	<!--Saisie manuelle-->
		<form action="" id="change_product_manual3" class="href_change_product_manual">
			<a href="#" class="change_product_close"><em>fermer</em></a>
			<div class="change_product_manual_grid">
				<!--INPUT-->
				<input list="products" type="text" placeholder="Riz, Café..." autocomplete="off" id="search_products" class="change_product_manual_value" required/>
				<input class="change_product_manual_submit" type="submit" value="ok"/>
				<div class="change_product_manual_text">texte</div>
			</div>
		</form>
	<!--CFG-->
		<form action="" id="stock_cfg3" class="href_stock_cfg">
		<a href="#" class="change_product_close"><em>fermer</em></a>
		<div class="stock_cfg_grid">
			<!--  Unit Selectbox -->	
				<label for="stock_unit" class="stock_unit_text">
					Unité
				</label>
				<div class="stock_unit_selectbox">
					<select id="stock_unit" class="stock_unit_selectbox">
						<option value="0">Conditionnement</option>
						<option value="1">kg - Kilo</option>
						<option value="2">g&nbsp; - Gramme</option>
						<!-- si unité=volume
						<option value="1">l&nbsp; - Litre</option>
						<option value="2">dl - Décilitre</option>
						<option value="3">cl - Centilitre</option>
						<option value="4">ml - Mililitre</option>
						-->
					</select>
				</div>
			<!--  Min Selectbox -->	
				<label for="stock_min" class="stock_min_text">
					Minimum
				</label>
				<div class="stock_min_selectbox">
					<input type="number" id="stock_min" value="1000" step="50" min="0" max="3000" class="stock_min_selectbox">
				</div>
			<!--  Max Selectbox -->	
				<label for="stock_max" class="stock_max_text">
					Maximum
				</label>
				<div class="stock_max_selectbox">
					<input type="number" id="stock_max" value="2000" step="50" min="0" max="3000" class="stock_max_selectbox">
				</div>
		</div>
		</form>

<?php /* TEST DEMO FIN*/ ?>





<!-- DATALIST Je pense que ce serai plus facil en JS -->
<datalist id="products">
	<!--A-->
		<option value="Abricot"><option value="Acérola"><option value="Agneau (mouton)"><option value="Ail"><option value="Airelles"><option value="Aki"><option value="Algue nori"><option value="Algues"><option value="Alkékenge (cerise de terre)"><option value="Amande"><option value="Ambérique	"><option value="Ananas"><option value="Aneth"><option value="Arachide"><option value="Arbouse"><option value="Argousier"><option value="Artichaut"><option value="Asperge"><option value="Aubergine"><option value="Avocat"><option value="Avoine">
	<!--B-->
		<option value="Baie de goji"><option value="Banane"><option value="Basilic"><option value="Bergamote"><option value="Bette à carde"><option value="Betterave"><option value="Beurre"><option value="Blé"><option value="Blette"><option value="Boeuf"><option value="Boulghour"><option value="Brocoli"><option value="Brugnon"><option value="butternut">
	<!--C-->
		<option value="Cacao"><option value="Café"><option value="Caille"><option value="Calmar"><option value="Canard"><option value="Canneberge"><option value="Cannelle"><option value="Carambole"><option value="Cardamome"><option value="Carotte"><option value="Cassis"><option value="Céleri"><option value="Céleri-rave"><option value="Cerfeuil"><option value="Cerise"><option value="Champignons"><option value="Châtaigne (marron)"><option value="Chocolat	"><option value="Chou"><option value="Chou chinois"><option value="Chou frisé"><option value="Chou-fleur"><option value="Chou-rave"><option value="Choux de Bruxelles"><option value="Ciboulette"><option value="Citron"><option value="Citrouille"><option value="Clémentine"><option value="Coing"><option value="Combava"><option value="Concombre	"><option value="Coquille Saint-Jacques"><option value="Coriandre"><option value="Cornichon"><option value="Courges"><option value="Courgette"><option value="Crabe"><option value="Crème fraîche"><option value="Cresson"><option value="Crevette"><option value="Crosne"><option value="Crosse de fougère"><option value="Cumin"><option value="Curcuma"><option value="Curry">
	<!--D-->
		<option value="Datte"><option value="Dinde"><option value="Dolique"><option value="Durian">
	<!--E-->
		<option value="Echalote"><option value="Endive"><option value="Endive"><option value="Epeautre"><option value="Epinards"><option value="Estragon">
	<!--F-->
		<option value="Fenouil"><option value="Fève des Marais"><option value="Figue"><option value="Figue de Barbarie"><option value="Flageolet"><option value="Foie gras"><option value="Fonio"><option value="Fraise"><option value="Framboise"><option value="Fromage"><option value="Fruit de la passion">
	<!--G-->
		<option value="Garam masala"><option value="Gingembre"><option value="Gombo"><option value="Gourgane"><option value="Goyave"><option value="Grenade"><option value="Griottes"><option value="Groseille">
	<!--H-->
		<option value="Hareng"><option value="Haricot vert"><option value="Haricots coco"><option value="Haricots mungo"><option value="Haricots secs"><option value="Homard"><option value="Huile de colza"><option value="Huile de palme"><option value="huiles végétales"><option value="Huître">
	<!--I-->
		<option value="Igname">
	<!--J-->
		<option value="Jujube">
	<!--K-->
		<option value="Kaki"><option value="Kale"><option value="Kiwi"><option value="Konjac"><option value="Kumquat">
	<!--L-->
		<option value="Lait"><option value="Laitue"><option value="Lapin"><option value="Laurier"><option value="Lentilles"><option value="Lime et citron"><option value="Litchi"><option value="Luzerne">
	<!--M-->
		<option value="maca"><option value="Mâche"><option value="Maïs"><option value="Mandarine"><option value="Mangue"><option value="Manioc"><option value="Maquereau"><option value="Marjolaine"><option value="Marron"><option value="Marron"><option value="Mélasse"><option value="Melon"><option value="Menthe"><option value="Mérou"><option value="Miel"><option value="Millet"><option value="Mirabelle"><option value="Miso"><option value="Morue"><option value="Moule"><option value="Moutarde"><option value="Mouton"><option value="Mûre"><option value="Myrtille">
	<!--N-->
		<option value="Navet"><option value="Nectarine"><option value="Nectarine et pêche"><option value="Noisette"><option value="Noix"><option value="Noix de cajou"><option value="Noix de coco"><option value="Noix de Macadamia"><option value="Noix de muscade"><option value="Noix de pécan"><option value="Noix du Brésil">
	<!--O-->
		<option value="Oeuf"><option value="Oie"><option value="Oignon"><option value="Oignon vert"><option value="Olive"><option value="Orange"><option value="Orge"><option value="Origan"><option value="Origan"><option value="Oseille">
	<!--P-->
		<option value="Pain"><option value="Palourde"><option value="Pamplemousse"><option value="Panais"><option value="Papaye"><option value="Paprika"><option value="Pastèque"><option value="Patate douce"><option value="Pâtisson"><option value="Pavot"><option value="Pêche"><option value="Persil"><option value="Petits pois"><option value="Pétoncle"><option value="Physalis"><option value="Pignon"><option value="Piment"><option value="Pistache"><option value="Pitaya"><option value="Pleurotes"><option value="Poire"><option value="Poireau"><option value="Pois cassé"><option value="Pois chiche"><option value="Poivron"><option value="Pomelo"><option value="Pomme"><option value="Pomme de terre"><option value="Porc (viande)"><option value="potimarron"><option value="Poulet"><option value="Pousse de bambou"><option value="Prune	"><option value="Pruneau">
	<!--Q-->
		<option value="Quetsche"><option value="Quinoa">
	<!--R-->
		<option value="Radis"><option value="Raifort"><option value="Raisin"><option value="Rhubarbe"><option value="Riz"><option value="Riz sauvage"><option value="Roquette"><option value="Rutabaga">
	<!--S-->
		<option value="Safran"><option value="Salicorne"><option value="Salsifis"><option value="Sardine"><option value="Sarrasin"><option value="Sarriette"><option value="Sauge"><option value="Saumon"><option value="Scarole"><option value="Seigle"><option value="Seitan"><option value="Semoule"><option value="Sésame"><option value="Shiitake"><option value="simili-viande"><option value="Sirop d'Erable"><option value="Soda"><option value="Soja"><option value="Sole">
	<!--T-->
		<option value="Tangerine"><option value="Tapioca"><option value="Teff"><option value="Tempeh"><option value="Tête de violon"><option value="Thé"><option value="Thon"><option value="Thym"><option value="Tilapia"><option value="tofu"><option value="Tomate"><option value="Topinambour"><option value="Tournesol"><option value="Truite">
	<!--U-->
	<!--V-->
		<option value="Vanille"><option value="Veau"><option value="Viande de gibier"><option value="Vin"><option value="Vinaigre"><option value="Vivaneau">
	<!--W-->
		<option value="Wakamé"><option value="Wasabi">
	<!--X-->
	<!--Y-->
		<option value="Yaourt"><option value="Yuzu">
	<!--Z-->
</datalist>

