(function(){

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


  //submit for product
  el('prod_insert').onsubmit = function() {
    return myLib.submit(this, updateUI);
  }
  el('prod_edit').onsubmit = function() {
    return myLib.submit(this, function() {
      // toggle the edit/view display
      el('productEditPanel').hide();
      el('productPanel').show();
      updateUI();
    });
  }
  el('prod_edit_cancel').onclick = function() {
    // toggle the edit/view display
    el('productEditPanel').hide();
    el('productPanel').show();
  }

})();
