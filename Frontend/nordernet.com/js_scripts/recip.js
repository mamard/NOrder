function load_recip_page_data(){
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
		url: "server/scripts/user_recipients.php",
		type: "POST",
		dataType: "json",
		data: {action: "user_erecipients_list", params: fparams},
		success: function(response) { 
			var status = response.status;

			if (status == 1){
				container = document.getElementById("container");
				var items = response.erecipients;
				var itemCounter = 0;
				items.forEach(function(item){
					item.num = ++itemCounter;

					var chrono = {};
					resolve_chrono(chrono, item);
					
    				erecipient_element = make_erecipient_element(item);
    				container.appendChild(erecipient_element);

    				function resolve_chrono(chrono, item){
    					chrono.cloud = 0;
    					chrono.battery = 0;
    					chrono.synchronization = 0;

    					var conf_state_code = item.configuration_state;
						var battery_level = item.battery_level;
						var pending_parameter_settings = item.pending_parameter_settings;
						var awakening_period = parseInt(item.awakening_period);
						var last_connection_date = Date.parse(item.last_connection_date);
						
						var now = new Date();

						if (conf_state_code != "NI") {

							//resolve cloud & sync chrono
							var seconds_diff = Math.round ((now.getTime() - last_connection_date) / (1000));
							
							if (seconds_diff > awakening_period  + 600) {
								chrono.cloud = 1;
    							chrono.synchronization = 1;
							}
							else {
								//resolve battery chrono
								if (battery_level >= 6) {
									chrono.battery = 3;
								}
								else if (battery_level >= 3){
									chrono.battery = 2;
								}
								else {
									chrono.battery = 1;
								}
								if (pending_parameter_settings > 0){
									chrono.cloud = 2;
									chrono.synchronization = 2;
								}
								else {
									chrono.cloud = 3;
									chrono.synchronization = 3;
								}
							}
						}	
					}

	    			function make_erecipient_element(eripient_item) {
						var new_item = document.createElement("div");
						new_item.setAttribute("class","style_box recip_section");
						new_item.setAttribute("id",eripient_item['recipient_id']);

						new_item.appendChild(make_img_child_element(eripient_item));
						new_item.appendChild(make_id_child_element(eripient_item));
						new_item.appendChild(make_title_child_element(eripient_item));
						new_item.appendChild(make_cloud_child_element(eripient_item));
						new_item.appendChild(make_batt_child_element(eripient_item));
						new_item.appendChild(make_sync_child_element(eripient_item));
						new_item.appendChild(make_empty_child_element(eripient_item));

						return new_item;
					}

					function make_img_child_element(eripient_item) {
						var img_element = document.createElement("div");
						img_element.setAttribute("title", "img");
						img_element.setAttribute("class", "recip_ebase");

						var base_img_element = document.createElement("img");
						base_img_element.setAttribute("alt","base");
						base_img_element.setAttribute("class","recip_ebase");
						base_img_element.setAttribute("src","picture/ebase/ebase_w.png");
						base_img_element.setAttribute("height", "40");

						var bocal_img_element = document.createElement("img");
						bocal_img_element.setAttribute("alt","bocal");
						bocal_img_element.setAttribute("class","recip_ebase");
						bocal_img_element.setAttribute("src","picture/ebase/ebase_bocal.png");
						bocal_img_element.setAttribute("height", "40");

						img_element.appendChild(base_img_element);
						img_element.appendChild(bocal_img_element);

						return img_element;
					}

					function make_id_child_element(eripient_item) {
						var id_element= document.createElement("div");
						id_element.setAttribute("title", "id");
						id_element.setAttribute("class", "recip_id");

						var recip_middle_element = document.createElement("div");
						recip_middle_element.innerHTML = '#';

						var span_recip_middle_element  = document.createElement("span");
						span_recip_middle_element.innerHTML = eripient_item.num;

						recip_middle_element.appendChild(span_recip_middle_element);
						id_element.appendChild(recip_middle_element);

						return id_element;
					}

					function make_title_child_element(eripient_item) {
						var title_element = document.createElement("div");
						title_element.setAttribute("title", "title");
						title_element.setAttribute("class", "recip_title");

						if (eripient_item.product_name != 'NA' && eripient_item.product_brand != 'NA') {
							title_element.innerHTML = eripient_item.product_name + " - " + eripient_item.product_brand;
						}
						else {
							title_element.innerHTML = "< inconnu >";
						}

						return title_element;
					}

					function make_cloud_child_element(eripient_item) {
						var cloud_element= document.createElement("div");
						cloud_element.setAttribute("title", "cloud");
						cloud_element.setAttribute("class", "recip_cloud");

						var cloud_img_element = document.createElement("img");
						cloud_img_element.setAttribute("alt","cl");
						cloud_img_element.setAttribute("class","recip_recip_middle");
						cloud_img_element.setAttribute("src","picture/status/cloud_" + chrono.cloud + ".png");
						cloud_img_element.setAttribute("height", "40");

						cloud_element.appendChild(cloud_img_element);

						return cloud_element;
					}

					function make_batt_child_element(eripient_item) {
						var batt_element= document.createElement("div");
						batt_element.setAttribute("title", "batt");
						batt_element.setAttribute("class", "recip_batt");

						var batt_img_element = document.createElement("img");
						batt_img_element.setAttribute("alt","cl");
						batt_img_element.setAttribute("class","recip_recip_middle");
						batt_img_element.setAttribute("src","picture/status/batt_" + chrono.battery +".png");
						batt_img_element.setAttribute("height", "40");

						batt_element.appendChild(batt_img_element);

						return batt_element;
					}

					function make_sync_child_element(eripient_item) {
						var sync_element= document.createElement("div");
						sync_element.setAttribute("title", "sync");
						sync_element.setAttribute("class", "recip_sync");

						var sync_img_element = document.createElement("img");
						sync_img_element.setAttribute("alt","cl");
						sync_img_element.setAttribute("class","recip_recip_middle");
						sync_img_element.setAttribute("src","picture/status/sync_" + chrono.synchronization + ".png");
						sync_img_element.setAttribute("height", "40");

						sync_element.appendChild(sync_img_element);

						return sync_element;
					}

					function make_empty_child_element(eripient_item) {
						var empty_element = document.createElement("div");
						empty_element.setAttribute("title","empty");
						empty_element.setAttribute("class","recip_empty");

						return empty_element;
					}
				});
			}
		},
		error: function(response, statut, erreur) { alert(erreur); }
	});
};






