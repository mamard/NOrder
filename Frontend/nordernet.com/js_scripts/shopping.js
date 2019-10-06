function load_shopping_page_data(){
	var user_login = sessionStorage.getItem("user");
	if (user_login == null || user_login == ""){
		window.location.href = "logon.php";
	}
	else{
		user_login_element = document.getElementById("user_login");
		user_login_element.innerHTML = user_login;
	}

	var fparams = [];
	fparams.push(user_login);

	$.ajax({
		url: "server/scripts/user_shopping_list.php",
		type: "POST",
		dataType: "json",
		data: {action: "selected_user_shopping_list_items", params: fparams},
		success: function(response) { 
			var status = response.status;
			if (status == 1) {
				container = document.getElementById("items_container");

				var margin_top = document.createElement("div");
				margin_top.setAttribute("class","liste_shopping_margin_top");

				container.appendChild(margin_top);

				var items = response.items_list;

				items.forEach(function(item){
					var item_element = make_list_shopping_grid_element(item);
    				container.appendChild(item_element);

    				function make_list_shopping_grid_element(list_item) {
    					var new_item = document.createElement("div");
						new_item.setAttribute("class","liste_shopping_grid");
						new_item.setAttribute("id",list_item['item_id']);

						var  checkbox_child_element = make_checkbox_child_element(list_item);
						var  quantity_child_element =  make_quantity_child_element(list_item);
						var  product_child_element =  make_product_child_element(list_item);
						
						new_item.appendChild(checkbox_child_element);
						new_item.appendChild(quantity_child_element);
						new_item.appendChild(product_child_element);

						if (list_item.item_comment != ""){
							var  comment_child_element =  make_comment_child_element(list_item);
						
							new_item.appendChild(comment_child_element);
						}
						
						return new_item;
    				}

    				function make_checkbox_child_element(list_item) {
    					var child_element = document.createElement("div");
						
						child_element.setAttribute("class", "liste_shopping_checkbox");

						var checkBox =  document.createElement("input");
						checkBox.setAttribute("class", "liste_shopping_checkbox");
						checkBox.setAttribute("type", "checkbox");
						checkBox.setAttribute("name", "case1");
						checkBox.setAttribute("onclick", "submit_new_purchased_state(this)");

						if (list_item.item_purchased == "t") {
							checkBox.setAttribute("checked","");
						}
						
						child_element.appendChild(checkBox);
						
						return child_element;
    				}

    				function make_quantity_child_element(list_item) {
    					var child_element = document.createElement("div");

						if (list_item.item_purchased == "t") {
							child_element.setAttribute("class", "liste_shopping_number liste_shopping_checked");
						}
						else
						{
							child_element.setAttribute("class", "liste_shopping_number liste_shopping_unchecked");
						}

						child_element.innerHTML = '\u00a0' + list_item.item_quantity + '\u00a0';
						
						return child_element;
    				}

    				function make_product_child_element(list_item) {
    					var child_element = document.createElement("div");
						
						if (list_item.item_purchased == "t") {
							child_element.setAttribute("class", "liste_shopping_product liste_shopping_checked");
						}
						else
						{
							child_element.setAttribute("class", "liste_shopping_product liste_shopping_unchecked");
						}

						child_element.innerHTML = list_item.product_name + "  " + list_item.product_brand + " " + list_item.product_quantity;
						
						return child_element;
    				}

    				function make_comment_child_element(list_item) {
    					var child_element = document.createElement("div");

    					if (list_item.item_purchased == "t") {
							child_element.setAttribute("class", "liste_shopping_comment liste_shopping_checked");
						}
						else
						{
							child_element.setAttribute("class", "liste_shopping_comment liste_shopping_unchecked");
						}

						var inner = document.createElement("i");
						inner.innerHTML = list_item.item_comment;

						child_element.appendChild(inner);

    					return child_element;
    				}
				});

				var margin_bottom = document.createElement("div");
				margin_bottom.setAttribute("class","liste_shopping_margin_bottom");

				container.appendChild(margin_bottom);
			}
		},
		error: function(response, statut, erreur) { alert(erreur); }
	});
}

function switchToListContext(){
	window.location.href = "liste.php";
}

function submit_new_purchased_state(chxBox){
	var parentListGrid = chxBox.closest('.liste_shopping_grid');

	var list_item_id = parentListGrid.id;
	var purchased = chxBox.checked;

	var numberChild = $(parentListGrid).find('.liste_shopping_number');
	var productChild = $(parentListGrid).find('.liste_shopping_product');
	var commentChild = $(parentListGrid).find('.liste_shopping_comment');

	if (purchased){
		$(numberChild).removeClass('liste_shopping_unchecked');
		$(productChild).removeClass('liste_shopping_unchecked');
		$(commentChild).removeClass('liste_shopping_unchecked');

		$(numberChild).addClass('liste_shopping_checked');
		$(productChild).addClass('liste_shopping_checked');
		$(commentChild).addClass('liste_shopping_checked');
	}
	else
	{
		$(numberChild).removeClass('liste_shopping_checked');
		$(productChild).removeClass('liste_shopping_checked');
		$(commentChild).removeClass('liste_shopping_checked');

		$(numberChild).addClass('liste_shopping_unchecked');
		$(productChild).addClass('liste_shopping_unchecked');
		$(commentChild).addClass('liste_shopping_unchecked');
	}

	var fparams = [];

	fparams.push(list_item_id);
	fparams.push(purchased);

	$.ajax({
		url: "server/scripts/user_shopping_list.php",
		type: "POST",
		dataType: "json",
		data: {action: "update_item_purchased_state", params: fparams},
		success: function(response) { },
		error: function(response, statut, erreur) { alert(erreur); }
	});
}