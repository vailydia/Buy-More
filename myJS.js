
(function(){

	get = function(param, successCallback) {
		param = param || {};
		param.rnd =  new Date().getTime(); // to avoid caching in IE
		myLib.processJSON('main-process.php?' + encodeParam(param), null, successCallback);
	};

	encodeParam = function(obj) {
		var data = [];
		for (var key in obj)
			data.push(encodeURIComponent(key) + '=' + encodeURIComponent(obj[key]));
		return data.join('&');
	}


	function updateUI() {
		get({action:'cat_fetchall'}, function(json){
			for (var subNavibarItems = [],productListItem = [],i = 0, cat; cat = json[i]; i++) {
				subNavibarItems.push('<li id="cat' , parseInt(cat.catid) ,'">', '<a href="#">' ,  '<span class="name">' , cat.name.escapeHTML() , '</span></a></li>');

				get({action:'prod_fetch',catid:parseInt(cat.catid)}, function(jsonP){
					for (var j = 0, prod; prod = jsonP[j]; j++) {
							productListItem.push('<li id="prod' , parseInt(prod.pid) ,'">',
							 '<div class = "media"><a class="media-top" href="#"><img class="media-object" src="incl/img/' , prod.pid , '.jpg" width="100" height="100" alt="productcell"></a>' ,
							 '<div class="media-body"><h4 class="media-heading">', '<span class="name">' , prod.name.escapeHTML() , '</span></h4>$',prod.price,'&nbsp&nbsp&nbsp',
							 '<button class="addButton" type="button">Add</button></div></div></li>');
					}
					if(i == json.length){
						el('productListDetails').innerHTML = productListItem.join('');
					}

				});

			}
			el('subnavbar').innerHTML = subNavibarItems.join('');
			el('breadcrumbDetails').innerHTML = '<li class="active"><a href="index.php">Home</a></li>';

		});


		$(".shoppingList").mouseenter(function(){

			  shoppingLists = window.localStorage.getItem('shoppingCart_storage');
			  shoppingLists = shoppingLists ? JSON.parse(shoppingLists):{};
				var sum = 0;

				var shoppingCartItems = [];


				var count = Object.keys(shoppingLists).length;
				count = count * 9;

				jQuery.each(shoppingLists, function(id, val) {


						get({action:'prod_fetchOne',pid:parseInt(id)}, function(json){
								 sum = sum + parseInt(json[0].price) * parseInt(val);

								 shoppingCartItems.push('<tr id="',id,'"><td>', json[0].name,'</td><td>edit:  ',json[0].price,
								 '  x  <input size="5" type="number" id="numofProd" value=',val,
								 '></input></td><td><button id="delete-prod" class="btn btn-danger btn-sm">delete</button></td></tr>');

								 if(count === shoppingCartItems.length){

										 var innerString = "Total Amount:   $";
								 		 el('sum').innerHTML = innerString.concat(sum) ;
								 		 el('shopping-cart').innerHTML = shoppingCartItems.join('');

										 el('delete-prod').addEventListener("click",
										 function(e) {
												 var parent = e.target.parentNode.parentNode;
												 var id = parent.id;
												 delete shoppingLists[id];
												 window.localStorage.setItem('shoppingCart_storage',JSON.stringify(shoppingLists));
												 showShoppingCart();

										 });

										 el('numofProd').addEventListener("input",
										 function(e){
											 var parent = e.target.parentNode.parentNode;
											 var id = parent.id;
											 shoppingLists[id] = e.target.value;
											 window.localStorage.setItem('shoppingCart_storage',JSON.stringify(shoppingLists));
											 showShoppingCart();

										 });

								 }

							});

				});


				var cartElement = document.getElementsByClassName("dropdown-content");
				cartElement[0].style.display = 'block';

		});
    $(".shoppingList").mouseleave(function() {
		    var cartElement = document.getElementsByClassName("dropdown-content");
				el('shopping-cart').innerHTML = "";
		    cartElement[0].style.display = 'none';
		});

	}
	updateUI();

  // handle the subnavbar click
	el('subnavbar').onclick = function(e) {

		var target = e.target,
			parent = target.parentNode,
			id = target.parentNode.id.replace(/^cat/, ''),
			name = target.parentNode.querySelector('.name').innerHTML;


			get({action:'prod_fetch',catid:id}, function(json){
				for (var productListItems = [],i = 0, prod; prod = json[i]; i++) {
					  //productListItems.push('<li id="prod' , parseInt(prod.pid) ,'">', '<a href="#">' ,  '<span class="name">' , prod.name.escapeHTML() , '</span></a></li>');
						productListItems.push('<li id="prod' , parseInt(prod.pid) ,'">',
						 '<div class = "media"><a class="media-top" href="#"><img class="media-object" src="incl/img/' , prod.pid , '.jpg" width="100" height="100" alt="productcell"></a>' ,
						 '<div class="media-body"><h4 class="media-heading">', '<span class="name">' , prod.name.escapeHTML() , '</span></h4>$',prod.price,'&nbsp&nbsp&nbsp',
						 '<button class="addButton" type="button">Add</button></div></div></li>');

				}
				el('productListDetails').innerHTML = productListItems.join('');

			});

			//update the breadcrumb and subNavibar
			var breadcrumbItem = [];
			breadcrumbItem.push('<li><a href="index.php">Home</a></li><li class = "active">', name ,'</li>');
			el('breadcrumbDetails').innerHTML = breadcrumbItem.join('');
	}


	//handle to deploy the detail of products
	el('productListDetails').onclick = function(e) {

			var target = e.target,
	      parent = target.parentNode.parentNode.parentNode,
	      id = parent.id.replace(/^prod/, '');
				name = parent.id;

			if('addButton' === target.className || 'productDetails' === target.className){

				get({action:'prod_fetchOne',pid:id}, function(json){
						var prodName = json[0].name;
						var prodPrice = parseInt(json[0].price);

						addToCart(prodName,prodPrice,id);
					});
			}

			else{

				get({action:'prod_fetchOne',pid:id}, function(json){
						var productListItems = [];

						 productListItems.push('<h2 class="media-heading">',json[0].name,
						 '</h2><div class="media"><a class="media-top" href="#"><img class="media-object" src="incl/img/', json[0].pid ,
						  '.jpg" width="200" height="200" alt="productcell"></a><div id="prod',id, '" class="media-body"><p></p><h4 class="media-heading">$',
							 json[0].price , '</h4><div><div><button class="productDetails" type="button">Add</button></div></div></div></div><h5>Description:</h5><P>',
							 json[0].description,'</P>');

						 el('productListDetails').innerHTML = productListItems.join('');

					});


			}

			//update the breadcrumb and subNavibar
			var breadcrumbItem = [];
			breadcrumbItem.push('<li><a href="index.php">Home</a></li><li class = "active">', name ,'</li>');
			el('breadcrumbDetails').innerHTML = breadcrumbItem.join('');

	}

})();


var shoppingLists = {};

function submitShoppingCart() {
	var form = el('checkout_form');

	var buyList = "";
	shoppingLists = window.localStorage.getItem('shoppingCart_storage');
	shoppingLists = shoppingLists ? JSON.parse(shoppingLists):{};

	for(var i in shoppingLists){
		//buyList[i] = parseInt(shoppingLists[i]); //combine the pid and quantity into array
		buyList = buyList + i + "," + shoppingLists[i] + ",";
	}

	get = function(param, successCallback) {
		param = param || {};
		param.rnd =  new Date().getTime(); // to avoid caching in IE
		myLib.processJSON('main-process.php?' + encodeParam(param), null, successCallback);
	};

	encodeParam = function(obj) {
		var data = [];
		for (var key in obj)
			data.push(encodeURIComponent(key) + '=' + encodeURIComponent(obj[key]));
		return data.join('&');
	}

	myLib.processJSON(
		    "checkout-process.php",
		    {action: "handle_checkout", list:buyList},
		    function(returnValue){

						form.custom.value = returnValue.digest;
						form.invoice.value = returnValue.invoice;

					//Specify details about the item that buyers will purchase.
					var index = 1;
					for (var i in shoppingLists){
						 get({action:'prod_fetchOne',pid:parseInt(i)}, function(json){
								 // Create <input> for name, number, quantity, and price
								 var name_input = document.createElement("input");
								 name_input.type = "hidden";
								 name_input.name = "item_name_" + index;
								 name_input.value = json[0].name;
								 form.appendChild(name_input);

								 var number_input = document.createElement("input");
								 number_input.type = "hidden";
								 number_input.name = "item_number_" + index;
								 number_input.value = "item" + json[0].pid;
								 form.appendChild(number_input);

								 var quantity_input = document.createElement("input");
								 quantity_input.type = "hidden";
								 quantity_input.name = "quantity_" + index;
								 quantity_input.value = parseInt(shoppingLists[json[0].pid]);
								 form.appendChild(quantity_input);

								 var price_input = document.createElement("input");
								 price_input.type = "hidden";
								 price_input.name = "amount_" + index;
								 price_input.value = json[0].price;
								 form.appendChild(price_input);

								 if(index == Object.keys(shoppingLists).length){
										 form.submit();
										 for (var j in shoppingLists) {
												window.localStorage.removeItem(shoppingLists[j]); //remove local storage
										 }
										 window.localStorage.removeItem('shoppingCart_storage');
								 }

								 index += 1;
						 });
				  }
			},
		    {method:"POST"});

	//return false;
}

function addToCart(productName,productPrice,pid){

		shoppingLists = window.localStorage.getItem('shoppingCart_storage');
	  shoppingLists = shoppingLists ? JSON.parse(shoppingLists):{};

		var contains = false;
		jQuery.each(shoppingLists, function(id, val) {
			if(parseInt(id) == parseInt(pid)) {
				 contains = true;
				 shoppingLists[parseInt(id)] = parseInt(val) + 1;
			}
		});
		if(contains == false){
			 shoppingLists[parseInt(pid)] = 1;
		}

		window.localStorage.setItem('shoppingCart_storage',JSON.stringify(shoppingLists));
		showShoppingCart();
}

function showShoppingCart(){

	  shoppingLists = window.localStorage.getItem('shoppingCart_storage');
	  shoppingLists = shoppingLists ? JSON.parse(shoppingLists):{};
		var sum = 0;

		var shoppingCartItems = [];

		var count = Object.keys(shoppingLists).length;
		count = count * 9;

		jQuery.each(shoppingLists, function(id, val) {

				get({action:'prod_fetchOne',pid:parseInt(id)}, function(json){
						 sum = sum + parseInt(json[0].price) * parseInt(val);

						 shoppingCartItems.push('<tr id="',id,'"><td>', json[0].name,'</td><td>edit:  ',json[0].price,
						 '  x  <input size="5" type="number" id="numofProd" value=',val,
						 '></input></td><td><button id="delete-prod" class="btn btn-danger btn-sm">delete</button></td></tr>');

						 if(count === shoppingCartItems.length){

								 var innerString = "Total Amount:   $";
						 		 el('sum').innerHTML = innerString.concat(sum) ;
						 		 el('shopping-cart').innerHTML = shoppingCartItems.join('');

								 el('delete-prod').addEventListener("click",
								 function(e) {
										 var parent = e.target.parentNode.parentNode;
										 var id = parent.id;
										 delete shoppingLists[id];
										 window.localStorage.setItem('shoppingCart_storage',JSON.stringify(shoppingLists));
										 showShoppingCart();

								 });

								 el('numofProd').addEventListener("input",
								 function(e){
									 var parent = e.target.parentNode.parentNode;
									 var id = parent.id;
									 shoppingLists[id] = e.target.value;
									 window.localStorage.setItem('shoppingCart_storage',JSON.stringify(shoppingLists));
									 showShoppingCart();

								 });

						 }

					});

		});


		var cartElement = document.getElementsByClassName("dropdown-content");
		cartElement[0].style.display = 'block';

}

function editShoppingCart() {

	  el('delete-prod').addEventListener("click",
		function(e) {
				var parent = e.target.parentNode.parentNode;
				var id = parent.id;
				delete shoppingLists[id];
				window.localStorage.setItem('shoppingCart_storage',JSON.stringify(shoppingLists));
				showShoppingCart();

		});


		el('numofProd').addEventListener("input",
		function(e){
			var parent = e.target.parentNode.parentNode;
			var id = parent.id;
			shoppingLists[id] = e.target.value;
			window.localStorage.setItem('shoppingCart_storage',JSON.stringify(shoppingLists));
			showShoppingCart();

		});


}

function closeShoppingCart() {
    var cartElement = document.getElementsByClassName("dropdown-content");
		el('shopping-cart').innerHTML = "";
    cartElement[0].style.display = 'none';
}

function clearAll(){

    shoppingCart = new Array();
    sum = 0;
    showShoppingCart();

}
