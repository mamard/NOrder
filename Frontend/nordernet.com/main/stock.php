<div id="block_1" class="stock_grid style_box">

	<!-- Image -->
	<div title="img" class="stock_img"><img id="product_picture" alt="img art" class="stock_img"  height="40"
		src="picture/no_picture.png"/></div>

	<!-- Titre -->
	<div id="product_designation" title="title" class="stock_title">&lsaquo; inconnu &rsaquo;</div>

	<!-- Niveau -->
	<div title="meter" class="stock_meter">
		<div class="styled" id="meter">
			<meter id="avancement_1" value="1500"
			min="0" low="1000" high="1200" optimum="3000" max="3000"></meter>
		</div>
		<div id="slider-range_1"  onmouseup="submit_new_threshold_values(this)" ontouchend="submit_new_threshold_values(this)"></div>
	</div>

	<!-- Scan -->
	<form id="upload_form_1" class="stock_scan" enctype="multipart/form-data">
		<!-- <img alt="img art" class="stock_scan"  height="40" src="picture/ean" onclick="select_ean_picture()"/>-->
		<label for="image_input_1">
			<img alt="img art" class="stock_scan"  height="40" src="picture/ean"/>
			</label>
		<input id="image_input_1" name="image_input" type="file" accept="image/*" capture="camera" style="height: 100px" hidden="true"  onChange="upload_file(this)"/>	
	</form>

	<!-- Mini -->
		<div class="stock_slider_min"> Mini <br><input id="amount_min_1" class="stock_slider" type="text"></div>
	<!-- Actuel -->
		<div class="stock_slider_now"> Actuel<br><span id="amount_now_1" class="stock_slider" style="line-height: 130%;">	&lsaquo; ? 	&rsaquo;</span></div>
	<!-- Maxi -->
		<div class="stock_slider_max"> Maxi <br><input id="amount_max_1" class="stock_slider" type="text"></div>
</div>

<div class="style_box sync_box" id="operation_in_progress" style="display: none">
	<div class="sync_text">TRAITEMENT</div>
	<div class="effect_rotated">
		<img class="sync" src="picture/sync.png" alt="" title="">
	</div>
	<div class="sync_text">EN COURS</div>
</div>

<script>
	$( function() { 
		$( "#slider-range_1" ).slider(
				{
				range: true, min: 0, max: 3 , step: 0.05 ,values: [ 1, 2 ], slide: function( event, ui )
					{
						var ava = document.getElementById("avancement_1");

						var max_capacity  = ava.getAttribute("max");

						if (parseInt((ui.values[ 1 ] - ui.values[ 0 ])*1000) < 100 ){
							return false;
						}

						$( "#amount_min_1" ).val( ui.values[ 0 ].toFixed(3) + "kg" ) ;
						$( "#amount_max_1" ).val( ui.values[ 1 ].toFixed(3) + "kg" ) ;
						
						ava.setAttribute("low", ui.values[ 0 ] * 1000);
						ava.setAttribute("high", (ui.values[ 0 ] + 0.2 * (ui.values[ 1 ] - ui.values[ 0 ])) * 1000);
						
					}
				} ) ;
		$( "#amount_min_1" ).val( $( "#slider-range_1" ).slider( "values", 0 ).toFixed(3) + "kg" ) ;
		$( "#amount_max_1" ).val( $( "#slider-range_1" ).slider( "values", 1 ).toFixed(3) + "kg" ) ;
	} );
</script>

<?php /* Cette partie été suprimée je te la met au cas ou...
	// Partie II: Niveau reel

		function avancement() {
			var ava = document.getElementById("avancement");
			var prc = document.getElementById("amount_now");
			prc.innerHTML = (ava.value / 1000).toFixed(3) + "kg";
		}avancement();
		function modif(val) {
			var ava = document.getElementById("avancement");
			if((ava.value+val)<=ava.max && (ava.value+val)>=0) {ava.value += val;}avancement();
		}
*/ ?>
