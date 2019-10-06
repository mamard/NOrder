<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include('php/head.php')?>
		<?php $select_menu_ico = 2 ?>
		<?php $user = 'Marine'?>
	</head>

	<body onload="load_stock_page_data()">
		<?php include('php/header.php')?>
		<main>
			<div class="main" id="container">
				<?php include('main/stock.php')?>
			</div>
		</main>
		<?php include('php/footer.php')?>
	</body>
</html>
