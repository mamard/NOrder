<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include('php/head.php')?>
		<?php $select_menu_ico = 3 ?>
		<?php $user = 'Marine'?>
	</head>

	<body>
		<?php include('php/header.php')?>
		<main>
			<div class="main" style="background: white">
				<?php include('main/liste.php')?>
				<?php include('php/add_product.php')?>
			</div>
		</main>
		<?php include('php/footer.php')?>
	</body>
</html>