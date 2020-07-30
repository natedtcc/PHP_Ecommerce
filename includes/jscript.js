// jscript.js - N. Nasteff

/* This script includes all relevant Javascript functions

*/


// Function to change the displayed order total based on shipping costs
// by modifying the shipping <p> element on the order_form page

function calc_total() {
	// Get string value of total from html element
	var sub_string = document.getElementById("subtotal").innerHTML;
	// Remove $ sign and cast to a decimal
	var subtotal = Number(sub_string.replace("$", ""));
	// Get value of shipping cost from the dropdown
	var shipping_id = Number(document.getElementById("shipping").value);
	
	var shipping_cost = 0.00;
	switch (shipping_id) {
  		case 1:
    		shipping_cost = 9.99;
    		break;
  		case 2:
    		shipping_cost = 12.99;
    		break;
		case 3:
			shipping_cost = 12.99;
			break;
		default:
			shipping_cost = 9.99;
}
	
	var total = subtotal + shipping_cost;
	document.getElementById("total").innerHTML = '<b>$' + total.toFixed(2);
	
}