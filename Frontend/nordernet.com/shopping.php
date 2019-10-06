<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include('php/head.php')?>
		<?php $select_menu_ico = 3 ?>
	</head>

	<body onload="load_shopping_page_data()">
		<?php include('php/header.php')?>
		<main>
			<div class="main" style="background: white">
				<?php include('main/shopping.php')?>
				<div id="items_container">
			</div>
		</main>
		<?php include('php/footer.php')?>
	</body>
</html>
