var connectionOptions = {
	"reconnectiontransports": true,
	"reconnectionAttempts": "Infinity",
	"timeout": 10000,
	"transports": ["polling", "websocket"]
};

var socket = io('http://<server_dns_name>:9000', connectionOptions);

//var socket = io.connect('http://sbdedemo.ddns.net:9000', connectionOptions);

socket.on('product_infos_response', function(data) {
	//var response = response.data;
	var response_data = JSON.parse(data);

	if (response_data['status'] == 1){
		
		var productInfos = response_data['product_infos'];
		var product = productInfos['product'];
		var brandName = product['brands'];
		var productName = product['product_name'];
		var productImageUrl = product['image_url'];

		var elementId = response_data['element_id'];

		var element = document.getElementById(elementId);
		var ancestor =  $('#' + elementId).closest('.stock_grid');

		ancestor.find('#product_picture').attr("src", productImageUrl);
		ancestor.find('#product_designation').html(productName + ' - ' + brandName);		
	}
	else{
		alert('Erreur - ' + response_data['error_message']);
	}

	$('#operation_in_progress').hide();
});

function load_stock_page_data(){
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
		url: "server/scripts/user_stores.php",
		type: "POST",
		dataType: "json",
		data: {action: "user_product_stores", params: fparams},
		success: function(response) { 
			var status = response.status;

			if (status == 1){
				container = document.getElementById("container");
				var items = response.erecipient_stores;
				var itemCounter = 0;
				items.forEach(function(item){
					if (item.store_quantity != null){
						var val_in_kg = (item.store_quantity / 1000).toFixed(3) + " kg";
						document.getElementById("amount_now_1").innerHTML = val_in_kg;

						var min = document.getElementById("avancement_1").getAttribute("min");
						var max = document.getElementById("avancement_1").getAttribute("max");

						var newVal = item.store_quantity;

						if (newVal < 50) {
							newVal = 50;
						}

						$( "#amount_min_1" ).val((item.low_quantity_threshold/1000).toFixed(3) + " kg");
						$( "#amount_max_1" ).val((item.high_quantity_threshold/1000).toFixed(3) + " kg" );
						
						$( "#slider-range_1" ).slider( "values", 0 , item.low_quantity_threshold/1000);
						$( "#slider-range_1" ).slider( "values", 1 , item.high_quantity_threshold/1000);

						document.getElementById("avancement_1").setAttribute("value", newVal);
						document.getElementById("avancement_1").setAttribute("low", item.low_quantity_threshold);
						document.getElementById("avancement_1").setAttribute("high", parseInt(item.low_quantity_threshold) + 0.2 * (parseInt(item.high_quantity_threshold) - parseInt(item.low_quantity_threshold)));


						var stock_grid_element = $("#avancement_1").closest('.stock_grid');

						stock_grid_element.attr("id", item.recipient_id);
						stock_grid_element.find('#product_picture').attr("src", item.product_image_url);
						stock_grid_element.find('#product_designation').html(item.product_name + ' - ' + item.product_brand);

						//break;
					}
					
				});
			}
		},
		error: function(response, statut, erreur) { alert(erreur); }
	});
};

// Partie I: selecteur min max
//* version d'origine: https://jqueryui.com/slider/#range */

function date2str(x, y) {
    var z = {
        M: x.getMonth() + 1,
        d: x.getDate(),
        h: x.getHours(),
        m: x.getMinutes(),
        s: x.getSeconds()
    };
    y = y.replace(/(M+|d+|h+|m+|s+)/g, function(v) {
        return ((v.length > 1 ? "0" : "") + eval('z.' + v.slice(-1))).slice(-2)
    });

    return y.replace(/(y+)/g, function(v) {
        return x.getFullYear().toString().slice(-v.length)
    });
}

function upload_file(obj){
	var parent = obj.parentNode;
	var element_id = parent.id;

	var uploadForm = document.getElementById(element_id);

	var postData = new FormData(uploadForm); 
	
	var customFileName = sessionStorage.getItem("user") + "_" + date2str(new Date(), "yyyy_MM_dd_hh_mm_ss") + ".jpg";
	
	postData.append('custom_name', customFileName);

	$('#operation_in_progress').show();

	//postData.append('file[]',)
	
	$.ajax({
		    type: "POST",
		    url: "server/scripts/upload_file.php",
		    //dataType: "json",
			data: postData,//ew FormData(uploadForm),
			contentType: false,
		    //cache: false,   
			processData: false, 
		    success: function(response){ //console.log(response);
				var res=JSON.parse(response);
				
		        if (res["status"] == 1){
					//document.body.style.cursor = 'wait';
					socket.emit('product_infos_request',{"user_login": sessionStorage.getItem("user"), "element_id": element_id, "file_name": res["image"]});
					/*setTimeout(function(){
					//do what you need here
					}, 5000);*/
					//document.body.style.cursor = 'default';
				}
				else{
					alert('Erreur - ' + res['message']);
					$('#operation_in_progress').hide();
				}
				
				
		    },

			error: function(response, statut, erreur) { alert(erreur); },
			timeout: 10000
		});		
} 

function submit_new_threshold_values(obj)
{
	var parentItemList = obj.closest(".stock_grid");

	var userLogin = sessionStorage.getItem("user");
	var newLowValue = $("#slider-range_1").slider( "values", 0) * 1000;
	var newHighValue = $("#slider-range_1").slider( "values", 1) * 1000;
	var erecipientId = parentItemList.id;

	var fparams = [];

	fparams.push(userLogin);
	fparams.push(erecipientId);
	fparams.push(newLowValue);

	$.ajax({
		url: "server/scripts/user_stores.php",
		type: "POST",
		dataType: "json",
		data: {action: "update_low_quantity_threshold", params: fparams},
		success: function(response) { },
		error: function(response, statut, erreur) { alert(erreur); }
	});

	fparams = [];

	fparams.push(userLogin);
	fparams.push(erecipientId);
	fparams.push(newHighValue);

	$.ajax({
		url: "server/scripts/user_stores.php",
		type: "POST",
		dataType: "json",
		data: {action: "update_high_quantity_threshold", params: fparams},
		success: function(response) { },
		error: function(response, statut, erreur) { alert(erreur); }
	});
}
