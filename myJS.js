
(function(){

	function updateUI() {
		myLib.get({action:'cat_fetchall'}, function(json){
			for (var subNavibarItems = [],productListItem = [],i = 0, cat; cat = json[i]; i++) {
				subNavibarItems.push('<li id="cat' , parseInt(cat.catid) ,'">', '<a href="#">' ,  '<span class="name">' , cat.name.escapeHTML() , '</span></a></li>');

				myLib.get({action:'prod_fetch',catid:parseInt(cat.catid)}, function(jsonP){
					for (var j = 0, prod; prod = jsonP[j]; j++) {
							productListItem.push('<li id="prod' , parseInt(prod.pid) ,'">',
							 '<div class = "media"><a class="media-top" href="#"><img class="media-object" src="incl/img/' , prod.pid , '.jpg" width="100" height="100" alt="productcell"></a>' ,
							 '<div class="media-body"><h4 class="media-heading">', '<span class="name">' , prod.name.escapeHTML() , '</span></h4>$',prod.price,'&nbsp&nbsp&nbsp',
							 '<button type="button">Add</button></div></div></li>');
					}
					if(i == json.length){
						el('productListDetails').innerHTML = productListItem.join('');
					}

				});

			}
			el('subnavbar').innerHTML = subNavibarItems.join('');
			el('breadcrumbDetails').innerHTML = '<li class="active"><a href="index.html">Home</a></li>';

		});


	}
	updateUI();

  // handle the subnavbar click
	el('subnavbar').onclick = function(e) {

		var target = e.target,
			parent = target.parentNode,
			id = target.parentNode.id.replace(/^cat/, ''),
			name = target.parentNode.querySelector('.name').innerHTML;


			myLib.get({action:'prod_fetch',catid:id}, function(json){
				for (var productListItems = [],i = 0, prod; prod = json[i]; i++) {
					  //productListItems.push('<li id="prod' , parseInt(prod.pid) ,'">', '<a href="#">' ,  '<span class="name">' , prod.name.escapeHTML() , '</span></a></li>');
						productListItems.push('<li id="prod' , parseInt(prod.pid) ,'">',
						 '<div class = "media"><a class="media-top" href="#"><img class="media-object" src="incl/img/' , prod.pid , '.jpg" width="100" height="100" alt="productcell"></a>' ,
						 '<div class="media-body"><h4 class="media-heading">', '<span class="name">' , prod.name.escapeHTML() , '</span></h4>$',prod.price,'&nbsp&nbsp&nbsp',
						 '<button type="button">Add</button></div></div></li>');
				}
				el('productListDetails').innerHTML = productListItems.join('');

			});

			//update the breadcrumb and subNavibar
			var breadcrumbItem = [];
			breadcrumbItem.push('<li><a href="index.html">Home</a></li><li class = "active">', name ,'</li>');
			el('breadcrumbDetails').innerHTML = breadcrumbItem.join('');


	}


	//handle to deploy the detail of products
	el('productListDetails').onclick = function(e) {

			var target = e.target,
	      parent = target.parentNode.parentNode.parentNode,
	      id = parent.id.replace(/^prod/, '');

			myLib.get({action:'prod_fetchOne',pid:id}, function(json){
					var productListItems = [];

					 productListItems.push('<h2 class="media-heading">',json[0].name,
					 '</h2><div class="media"><a class="media-top" href="#"><img class="media-object" src="incl/img/', json[0].pid ,
					  '.jpg" width="200" height="200" alt="productcell"></a><div class="media-body"><p></p><h4 class="media-heading">$',
						 json[0].price , '</h4><button type="button" class="btn .btn-primary">Add</button></div></div><h5>Description:</h5><P>',
						 json[0].description,'</P>');

					 el('productListDetails').innerHTML = productListItems.join('');

				});

			//update the breadcrumb and subNavibar
			var breadcrumbItem = [];
			breadcrumbItem.push('<li><a href="index.html">Home</a></li><li class = "active">', name ,'</li>');
			el('breadcrumbDetails').innerHTML = breadcrumbItem.join('');

	}



})();


var shoppingCart = [];
var sum = 0;

function addToCart(productName,productPrice){

    shoppingCart.push(productName);
    sum = sum + productPrice;

}

function showShoppingCart(){

    var cartElement = document.getElementsByClassName("dropdown-content");
    var para=document.createElement("p");
    var text = "";

    for (var i = 0; i < shoppingCart.length; i++) {
        var value = shoppingCart[i];
        text += value + " ";
    }
    //var node=document.createTextNode(text);
    //para.appendChild(node);
    para.innerHTML = text;

    cartElement[0].insertBefore(para,cartElement[0].firstChild);

    var total=document.createElement("p");
    var sumNum=document.createTextNode("total: $"+ sum + " ");
    total.appendChild(sumNum);
    cartElement[0].insertBefore(total,para);

    cartElement[0].style.display = 'block';

}

function closeShoppingCart() {
    var cartElement = document.getElementsByClassName("dropdown-content");
    cartElement[0].removeChild(cartElement[0].childNodes[0]);
    cartElement[0].removeChild(cartElement[0].childNodes[0]);
    cartElement[0].style.display = 'none';
}

function clearAll(){

    shoppingCart = new Array();
    sum = 0;
    showShoppingCart();

}
