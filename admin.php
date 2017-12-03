<?php

session_start();
include_once ('lib/csrf.php');
include_once ('lib/makeAuth.php');


if(!auth_process()){
	header('Location: login.php');
	exit();
}

?>



<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>IERG4210 Shop - Admin Panel</title>

	<link rel="stylesheet" href="bootstrap-social.css"/>
	<link href="mystyles.css" rel="stylesheet"/>
	<link href="incl/admin.css" rel="stylesheet" type="text/css"/>

</head>

<body>

		<ul>
			  <li><a href="/index.php">Back to Home</a></li>
				<li><a href="/auth-process.php?action=<?php  echo ($action='login');  ?>"><span>Login</span></a></li>
				<li><span>User:</span>
					<a href="/auth-process.php?action=<?php  echo ($action='login');  ?>">
					<span><?php

						 if(!empty($_SESSION['t4210'])){
								echo $_SESSION['t4210']['em'];
						 }else{
								echo "No User";
						 }
						 ?></span></a></li>

				 <li><a href="/auth-process.php?action=<?php  echo ($action='logout');  ?>"><span>Logout</span></a></li>
		</ul>


<h1>Buy More Shop - Admin Panel</h1>

<article id="main">

<section id="categoryPanel">
	<fieldset>
		<legend>New Category</legend>
		<form id="cat_insert" method="POST" action="admin-process.php?action=<?php  echo ($action='cat_insert');  ?>" onsubmit="return false;">
			<label for="cat_insert_name">Name *</label>
			<div><input id="cat_insert_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
			<input type="hidden" name="nonce" value="<?php  echo csrf_getNonce($action);   ?>" />
			<input type="submit" value="Submit" />
		</form>
	</fieldset>

	<!-- Generate the existing categories here -->
	<h3>All categories in the database are :</h>
	<ul id="categoryList"></ul>
</section>

<section id="categoryEditPanel" class="hide">
	<fieldset>
		<legend>Editing Category</legend>
		<form id="cat_edit" method="POST" action="admin-process.php?action=<?php  echo ($action='cat_edit');  ?>" onsubmit="return false;">
			<label for="cat_edit_name">Name</label>
			<div><input id="cat_edit_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
			<input type="hidden" id="cat_edit_catid" name="catid" />
			<input type="hidden" name="nonce" value="<?php  echo csrf_getNonce($action);   ?>" />
			<input type="submit" value="Submit" />
			<input type="button" id="cat_edit_cancel" value="Cancel" />
		</form>
	</fieldset>
</section>

<section id="productPanel">
	<fieldset>
		<legend>New Product</legend>
		<form id="prod_insert" method="POST" action="admin-process.php?action=<?php  echo ($action='prod_insert');  ?>" enctype="multipart/form-data">
			<label for="prod_insert_catid">Category *</label>
			<div><select id="prod_insert_catid" name="catid"></select></div>

			<label for="prod_insert_name">Name *</label>
			<div><input id="prod_insert_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>

			<label for="prod_insert_price">Price *</label>
			<div><input id="prod_insert_price" type="number" name="price" required="true" pattern="^[\d\.]+$" /></div>

			<label for="prod_insert_description">Description</label>
			<div><textarea id="prod_insert_description" name="description" pattern="^[\w\-,. ]*$"></textarea></div>

			<label for="prod_insert_name">Image *</label>
			<div><input type="file" name="file" required="true" accept="image/jpeg" /></div>

			<input type="hidden" name="nonce" value="<?php  echo csrf_getNonce($action);   ?>" />
			<input type="submit" value="Submit" />
		</form>
	</fieldset>

  <h3>Products in the database are :</h>
	<ul id="productList"></ul>

</section>



<section id="productEditPanel" class="hide">
		<!--
			Design your form for editing a product's catid, name, price, description and image
			- the original values/image should be prefilled in the relevant elements (i.e. <input>, <select>, <textarea>, <img>)
			- prompt for input errors if any, then submit the form to admin-process.php (AJAX is not required)
		-->

		<fieldset>
			<legend>Edit Product</legend>
			<form id="prod_edit" method="POST" action="admin-process.php?action=<?php  echo ($action='prod_edit');  ?>" enctype="multipart/form-data">
				<label for="prod_edit_catid">Category *</label>
				<div><select id="prod_edit_catid" name="catid"></select></div>

				<label for="prod_edit_name">Name *</label>
				<div><input id="prod_edit_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>

				<label for="prod_edit_price">Price *</label>
				<div><input id="prod_edit_price" type="number" name="price" required="true" pattern="^[\d\.]+$" /></div>

				<label for="prod_edit_description">Description</label>
				<div><textarea id="prod_edit_description" name="description" pattern="^[\w\-\. ]+$"></textarea></div>

				<label for="prod_insert_name">Image *</label>
				<div><input type="file" name="file" required="true" accept="image/jpeg" /></div>

				<input type="hidden" id="prod_edit_pid" name="pid" />
				<input type="submit" value="Submit" />
				<input type="button" id="prod_edit_cancel" value="Cancel" />

				<input type="hidden" name="nonce" value="<?php  echo csrf_getNonce($action);   ?>" />
				<input type="submit" value="Submit" />
			</form>

		</fieldset>

</section>


<section>
	<fieldset>
		<legend>Latest 50 Transaction Records</legend>
		<ul id="ordersList"></ul>

  </fieldset>
</section>


<div class="clear"></div>
</article>
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript">
(function(){
	get = function(param, successCallback) {
		param = param || {};
		param.rnd =  new Date().getTime(); // to avoid caching in IE
		myLib.processJSON('checkout-process.php?' + encodeParam(param), null, successCallback);
	};

	encodeParam = function(obj) {
		var data = [];
		for (var key in obj)
			data.push(encodeURIComponent(key) + '=' + encodeURIComponent(obj[key]));
		return data.join('&');
	}

	function updateUI() {
		myLib.get({action:'cat_fetchall'}, function(json){
			// loop over the server response json
			//   the expected format (as shown in Firebug):
			for (var options = [], listItems = [],
					i = 0, cat; cat = json[i]; i++) {
				options.push('<option value="' , parseInt(cat.catid) , '">' , cat.name.escapeHTML() , '</option>');
				listItems.push('<li id="cat' , parseInt(cat.catid) , '"><span class="name">' , cat.name.escapeHTML() , '</span> <span class="delete">[Delete]</span> <span class="edit">[Edit]</span></li>');
			}
			el('prod_insert_catid').innerHTML = '<option></option>' + options.join('');
			el('categoryList').innerHTML = listItems.join('');
		});
		el('productList').innerHTML = '';
	}
	updateUI();

	function updateOrdersList() {
		get({action:'order_fetchall'}, function(json){
			for (var listItems = [],i = 0, order; order = json[i]; i++) {
				listItems.push('<li id="order' , parseInt(order.oid) , '"><span class="user">' ,
				order.user, '</span><span class="digest">',order.digest , '</span><span class="salt">',order.salt ,
				 '</span><span class="tid">',order.paid ,'</span></li>');
			}
			el('ordersList') = innerHTML = listItems.join('');

		});

	}

	updateOrdersList();

	el('categoryList').onclick = function(e) {
		if (e.target.tagName != 'SPAN')
			return false;

		var target = e.target,
			parent = target.parentNode,
			id = target.parentNode.id.replace(/^cat/, ''),
			name = target.parentNode.querySelector('.name').innerHTML;

		// handle the delete click
		if ('delete' === target.className) {
			confirm('Sure?') && myLib.post({action: 'cat_delete', catid: id}, function(json){
				alert('"' + name + '" is deleted successfully!');
				updateUI();
			});

		// handle the edit click
		} else if ('edit' === target.className) {
			// toggle the edit/view display
			el('categoryEditPanel').show();
			el('categoryPanel').hide();

			// fill in the editing form with existing values
			el('cat_edit_name').value = name;
			el('cat_edit_catid').value = id;

		//handle the click on the category name
		} else {
			el('prod_insert_catid').value = id;

			myLib.get({action:'prod_fetch',catid:id}, function(json){
			        for (var productListItems = [],i = 0, prod; prod = json[i]; i++) {
			            productListItems.push('<li id="', parseInt(prod.catid), 'prod' , parseInt(prod.pid) , '"><span class="name">' , prod.name.escapeHTML() , '</span> <span class="delete">[Delete]</span> <span class="edit">[Edit]</span></li>');
			        }
			        el('productList').innerHTML = productListItems.join('');
			 });

		}
	}

	el('productList').onclick = function(e) {
		if (e.target.tagName != 'SPAN')
			return false;

		var target = e.target,
			parent = target.parentNode,
			catid = target.parentNode.id.replace(/prod\d*/, ''),
			id = target.parentNode.id.replace(/\d*prod/, ''),
			name = target.parentNode.querySelector('.name').innerHTML;


		// handle the delete click
		if ('delete' === target.className) {
			confirm('Sure?') && myLib.post({action: 'prod_delete', pid: id}, function(json){
				alert('"' + name + '" is deleted successfully!');
				updateUI();
			});

		// handle the edit click
		} else if ('edit' === target.className) {
			// toggle the edit/view display
			el('productEditPanel').show();
			el('productPanel').hide();

			// fill in the editing form with existing values
			el('prod_edit_name').value = name;
			el('prod_edit_pid').value = id;

			myLib.get({action:'cat_fetchall'}, function(json){
				for (var options = [],i = 0, cat; cat = json[i]; i++) {
					options.push('<option value="' , parseInt(cat.catid) , '">' , cat.name.escapeHTML() , '</option>');
				}
				el('prod_edit_catid').innerHTML = '<option></option>' + options.join('');
			});

			myLib.get({action:'prod_fetchOne',pid:id}, function(json){
				el('prod_edit_price').value = json[0].price;
				el('prod_edit_description').value = json[0].description;
				el('prod_edit_catid').value = json[0].catid;
			});

		}

	}


  //submit for category
	el('cat_insert').onsubmit = function() {
		return myLib.submit(this, updateUI);
	}
	el('cat_edit').onsubmit = function() {
		return myLib.submit(this, function() {
			// toggle the edit/view display
			el('categoryEditPanel').hide();
			el('categoryPanel').show();
			updateUI();
		});
	}

	el('cat_edit_cancel').onclick = function() {
		// toggle the edit/view display
		el('categoryEditPanel').hide();
		el('categoryPanel').show();
	}

	el('prod_edit_cancel').onclick = function() {
		// toggle the edit/view display
		el('productEditPanel').hide();
		el('productPanel').show();
	}

})();
</script>
</body>
</html>
