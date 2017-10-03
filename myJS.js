'use strict';

var shoppingCart = new Array();
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
