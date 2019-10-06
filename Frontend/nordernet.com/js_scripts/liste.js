function load_liste_page_data(){
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
		data: {action: "all_user_shopping_list_items", params: fparams},
		success: function(response) { 
			var status = response.status;
			if (status == 1) {
				container = document.getElementById("items_container");
				var items = response.product_list;
				var itemCounter = 0;
				var norderisable = true;

				var auto_items_list = document.createElement("div");
				auto_items_list.setAttribute("class","liste_title");
				auto_items_list.setAttribute("style","");
				auto_items_list.innerHTML = 'Liste automatique';
				container.appendChild(auto_items_list);

				items.forEach(function(item){
					
					if (norderisable && !(item.norderisable == "t")) {
						norderisable = false;
						var manual_items_list = document.createElement("div");
						manual_items_list.setAttribute("class","liste_title");
						manual_items_list.setAttribute("style","");
						manual_items_list.innerHTML = 'Produits ajoutés manuellement';
						container.appendChild(manual_items_list); 
					}

					var item_element = make_list_grid_element(item);
    				container.appendChild(item_element);
					//alert("List of shopping items : \n" + "Name : " + item.product_name + "\n" +  "Norderisable : " + item.norderisable + "\n" + "Quantity : " + item.item_quantity);
					function make_list_grid_element(list_item) {
						var new_item = document.createElement("div");
						new_item.setAttribute("class","liste_grid");
						new_item.setAttribute("id",list_item['item_id']);

						var  checkbox_text_child_element = make_checkbox_text_child_element(list_item);
						var  checkbox_child_element =  make_checkbox_child_element(list_item);
						var  img_child_element =  make_img_child_element(list_item);
						var  product_child_element =  make_product_child_element(list_item);
						var  comment_child_element =  make_comment_child_element(list_item);
						var  quantity_child_element =  make_quantity_child_element(list_item);
						var  selectbox_child_element =  make_selectbox_child_element(list_item);

						new_item.appendChild(checkbox_text_child_element);
						new_item.appendChild(checkbox_child_element);
						new_item.appendChild(img_child_element);
						new_item.appendChild(product_child_element);
						new_item.appendChild(comment_child_element);
						new_item.appendChild(quantity_child_element);
						new_item.appendChild(selectbox_child_element);

						return new_item;
					}

					function make_checkbox_text_child_element(list_item) {
						var child_element = document.createElement("div");
						
						child_element.setAttribute("class", "liste_checkbox_text");
						child_element.innerHTML = "ajouter?";

						return child_element;
					}

					function make_checkbox_child_element(list_item){
						var child_element = document.createElement("div");
						
						child_element.setAttribute("class", "liste_checkbox");

						var toggle_selected = document.createElement("input");
						toggle_selected.setAttribute("class", "toggle_selected");
						toggle_selected.setAttribute("type", "checkbox");
						toggle_selected.setAttribute("onchange", "submit_new_item_selected_state(this)");

						if (list_item.item_selected == "t") {
							toggle_selected.setAttribute("checked","");
						}
						
						child_element.appendChild(toggle_selected);

						return child_element;
					}

					function make_img_child_element(list_item){
						var child_element = document.createElement("div");
						
						child_element.setAttribute("title", "img");
						child_element.setAttribute("class", "liste_img");

						var img = document.createElement("img");
						img.setAttribute("id", "product_picture");
						img.setAttribute("alt", "img art");
						img.setAttribute("class", "liste_img");
						img.setAttribute("height", "40");
						img.setAttribute("src", list_item.product_image_url);

						child_element.appendChild(img);

						return child_element;
					}

					function make_product_child_element(list_item){
						var child_element = document.createElement("div");
						
						child_element.setAttribute("class", "liste_product");
						child_element.innerHTML = list_item.product_name + " - " +  list_item.product_brand;

						return child_element;
					}

					function make_comment_child_element(list_item){
						var child_element = document.createElement("div");
						
						child_element.setAttribute("class", "liste_comment");

						var comment_text = document.createElement("input");
						comment_text.setAttribute("id", "item_" + list_item.item_id + "_comment");
						comment_text.setAttribute("type", "text");
						comment_text.setAttribute("style", "width: 80px");
						comment_text.setAttribute("placeholder", "commentaire");
						$(comment_text).val(list_item.item_comment);

						var comment_submit = document.createElement("input");
						comment_submit.setAttribute("type", "submit");
						comment_submit.setAttribute("style", "width: 1px");
						comment_submit.setAttribute("value", "ok");
						comment_submit.setAttribute("onclick", "submit_new_item_comment(this)");

						child_element.appendChild(comment_text);
						child_element.appendChild(comment_submit);

						return child_element;
					}

					function make_quantity_child_element(list_item){
						var child_element = document.createElement("div");
						
						child_element.setAttribute("class", "liste_quantity");

						if (list_item.product_quantity != null && list_item.product_quantity != ""){
							child_element.innerHTML = list_item.product_quantity;
						}
						else {
							child_element.innerHTML = "<?>";
						}

						return child_element;
					}

					function make_selectbox_child_element(list_item){
						var child_element = document.createElement("div");
						
						child_element.setAttribute("class", "liste_selectbox");

						var box = document.createElement("div");
						box.setAttribute("class", "box");

						var select = document.createElement("select");
						select.setAttribute("onchange","submit_new_item_quantity(this)");
						var i;
						for (i = 0; i<=10; i++) {
							option = document.createElement("option");
							option.setAttribute("value", i);
							option.innerHTML = i;

							if (i == list_item.item_quantity) {
								option.setAttribute("selected","");
							}

							select.appendChild(option);
						}

						box.appendChild(select);

						child_element.appendChild(box);

						return child_element;
					}					
				});

				var add_product_element = document.createElement("div");
				add_product_element.setAttribute("class","add_product_margin");
				
				container.appendChild(add_product_element);
			}
		},
		error: function(response, statut, erreur) { alert(erreur); }
	});
}

function switchToShoppingContext(){
	window.location.href = "shopping.php";
}

function submit_new_item_selected_state(obj)
{
	var parentItemList = obj.closest(".liste_grid");

	var list_item_id = parentItemList.id;
	var selected = obj.checked;

	var fparams = [];

	fparams.push(list_item_id);
	fparams.push(selected);

	$.ajax({
		url: "server/scripts/user_shopping_list.php",
		type: "POST",
		dataType: "json",
		data: {action: "update_item_selected_state", params: fparams},
		success: function(response) { },
		error: function(response, statut, erreur) { alert(erreur); }
	});
}

function submit_new_item_comment(obj)
{
	var parentItemList = obj.closest(".liste_grid");

	var list_item_id = parentItemList.id;
	var newComment = $("#item_" + list_item_id + "_comment").val();

	var fparams = [];

	fparams.push(list_item_id);
	fparams.push(newComment);

	$.ajax({
		url: "server/scripts/user_shopping_list.php",
		type: "POST",
		dataType: "json",
		data: {action: "update_item_comment", params: fparams},
		success: function(response) { },
		error: function(response, statut, erreur) { alert(erreur); }
	});
}

function submit_new_item_quantity(obj)
{
	var parentItemList = obj.closest(".liste_grid");

	var list_item_id = parentItemList.id;
	var newQuantity = $(obj).val();

	var fparams = [];

	fparams.push(list_item_id);
	fparams.push(newQuantity);

	$.ajax({
		url: "server/scripts/user_shopping_list.php",
		type: "POST",
		dataType: "json",
		data: {action: "update_item_quantity", params: fparams},
		success: function(response) { },
		error: function(response, statut, erreur) { alert(erreur); }
	});
}

function add_list_product_from_scan(obj){
	
	var uploadForm = obj.closest("form");

	$.ajax({
		    type: "POST",
		    url: "server/scripts/upload_file.php",
		    //dataType: "json",
			data: new FormData(uploadForm),
			contentType: false,
		    //cache: false,   
			processData: false, 
		    success: function(response){ //console.log(response);
				var res=JSON.parse(response);
				
		        if (res["status"] == 1){
					//document.body.style.cursor = 'wait';
					socket.emit('product_infos_request',{"element_id": element_id, "file_name": res["image"]});
					/*setTimeout(function(){
					//do what you need here
					}, 5000);*/
					//document.body.style.cursor = 'default';
				}
				else{
					alert('Erreur - ' + res['message']);
				}	
		    },

			error: function(response, statut, erreur) { alert(erreur); }
		});		
} 


