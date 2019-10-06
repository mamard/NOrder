<!-- Add Product -->
<div class="add_product">
	<!-- Metode -->
	<form action="" id="add_product_method">
		<a href="#" class="add_product_close"><em>fermer</em></a>
		<div class="add_product_method_grid">
			<!-- Scan produit -->
			<div class="add_product_method_scan add_product_style_button" id="hey">
				<form id="upload_form" enctype="multipart/form-data"> 
				<!-- <img alt="img art" class="stock_scan"  height="40" src="picture/ean" onclick="select_ean_picture()"/>-->
					<label for="image_input">
					    <img img id="scan_picture" alt="scan"  class="add_product_method_scan" height="40" src="picture/scan2.png"/>
					</label>
					<input id="image_input" name="image_input" type="file" accept="image/*" capture="camera"  hidden="true"  onChange="add_list_product_from_scan(this)"/>	
				</form>	
			</div>		
			<div class="add_product_method_scan_text">Scanner avec smartphone</div>
			<!-- Ou -->
			<div class="add_product_method_or">ou</div>
			<!-- Ajout manuel -->	
			<a href="#add_product_manual" class="add_product_method_manual add_product_style_button">Ajout<br>manuel</a>
			<div class="add_product_method_manual_text">texte</div>
		</div>
	</form>
	<!--Manuel-->
	<form action="" id="add_product_manual">
		<a href="#" class="add_product_close"><em>fermer</em></a>
		<div class="add_product_manual_grid">
			<!--INPUT-->
			<input list="products" type="text" placeholder="Riz, Café..." autocomplete="off" id="search_products" class="add_product_manual_value" required/>
			<input class="add_product_manual_submit" type="submit" value="ok"/>
			<div class="add_product_manual_text">texte</div>
		</div>
	</form>
	<div class="add_product_add_button">
		<a href="#add_product_method"><img alt="plus" height="40" src="picture/ico_more.png" class="add_product_add_button"/></a>
	</div>
</div>

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