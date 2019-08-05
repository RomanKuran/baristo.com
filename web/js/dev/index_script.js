// alert(localStorage.getItem('Seller_key'));
// Change the selector if needed
var $table = $('table.scroll'),
    $bodyCells = $table.find('tbody tr:first').children(),
    colWidth;
var All_Sum = 0;
var Price = 0;
var Name = "";
var Count = 0;
var Products_id = [];
var Products_count = [];
var Seller_key = "";
// var Products = [];
var Product = null;
var Staus_clear = true;


// Adjust the width of thead cells when window resizes
$(window).resize(function() {
    // Get the tbody columns width array
    colWidth = $bodyCells.map(function() {
        return $(this).width();
    }).get();
    
    // Set the width of thead columns
    $table.find('thead tr').children().each(function(i, v) {
        $(v).width(colWidth[i]);
    });    
}).resize(); // Trigger resize handler






//-------------filter---------

jQuery.fn.fastLiveFilter = function(list, options) {
	// Options: input, list, timeout, callback
	options = options || {};
	list = jQuery(list);
	var input = this;
	var lastFilter = '';
	var timeout = options.timeout || 0;
	var callback = options.callback || function() {};	
	var pType = options.type || "filter";
	
	var keyTimeout;
	
	// NOTE: because we cache lis & len here, users would need to re-init the plugin
	// if they modify the list in the DOM later.  This doesn't give us that much speed
	// boost, so perhaps it's not worth putting it here.
	var lis = list.children();
	var len = lis.length;
	var oldDisplay = len > 0 ? lis[0].style.display : "block";
	callback(len); // do a one-time callback on initialization to make sure everything's in sync
  
  if(pType === "search") {
    for (var i = 0; i < len; i++) {
			lis[i].style.display = "none";
    }
  }
	
	input.change(function() {
		// var startTime = new Date().getTime();
		var filter = input.val().toLowerCase();
    
		var li, innerText;
		var numShown = 0;
		for (var i = 0; i < len; i++) {
			li = lis[i];
			innerText = !options.selector ? 
				(li.textContent || li.innerText || "") : 
				$(li).find(options.selector).text();
      
      if(pType !== "search" || filter.trim().length > 0) {
				if (innerText.toLowerCase().indexOf(filter) >= 0) {
					if (li.style.display == "none") {
						li.style.display = oldDisplay;
					}
					numShown++;
				} else {
					if (li.style.display != "none") {
						li.style.display = "none";
					}
				}
      } else {
				li.style.display = "none";
			}
		}
		callback(numShown);
		// var endTime = new Date().getTime();
		// console.log('Search for ' + filter + ' took: ' + (endTime - startTime) + ' (' + numShown + ' results)');
		return false;
	}).keydown(function() {
		clearTimeout(keyTimeout);
		keyTimeout = setTimeout(function() {
			if( input.val() === lastFilter ) return;
			lastFilter = input.val();
			input.change();
		}, timeout);
	});
	return this; // maintain jQuery chainability
}

$('#filter_input_product').fastLiveFilter('#filter_list_product');
$('#filter_input_category').fastLiveFilter('#filter_list_category');



//---------------------


// $( ".product" ).click(function() {
// 	console.log(this);
// });
// $(document).on("click", ".product",function() {
// 	console.log
// });




$(document).on("click", ".product", function() {

	var values = this.getElementsByClassName('name');
	var name = this.getElementsByClassName('name');
	Product = name[0].getAttribute("product");
		//  alert(values.innerHTML);
		$.ajax({
			url: "get-price-product",
			type: "post",
			data: "id=" +  Product,
			success: function (response) {             
				Price = response.price;
				var count = $(".input .container_count .count");
				
			
				Name = name[0].innerText;
				// Price = +price[0].innerText;
				Sum = Price * (+count.val());
				$(".input .container_name .name").val(name[0].innerText);
				$(".input .container_price .price").val(Sum + "грн");
				$(".input .container_count .count").val(1);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			// console.log(textStatus, errorThrown);
				alert("Щось пішло не так!!! (Запит по продукт)");
			}
			
		});
});




$(document).on("click", ".upp", function() {
	var count = +$("#count").val()+1;
	if(count < 1){
		count = 1;
	}
	$("#count").val(count);

	// var count = $(".input .container_count .count");
	Sum = Price * (+count);
	$(".input .container_price .price").val(Sum + "грн");
});

$(document).on("click", ".down", function() {
	var count = +$("#count").val()-1;

	if(count > 0)
	{
		$("#count").val(count);
	
		Sum = Price * (+count);
		$(".input .container_price .price").val(Sum + "грн");
	}
});

$(document).on("input", '#count', function(){
	var sum = 0;
	if(+$("#count").val()>0){
		sum = Price * (+$("#count").val());
		$(".input .container_price .price").val(sum + "грн");
	}
});


$(document).on("click", ".ok", function() {

	if(Staus_clear)
	{
		Count = +$(".input .container_count .count").val();	
		var sum = 0;
		
		var product = "";
		

		if((/^[1-9]{1}[0-9]*$/.test(+$(".input .container_count .count").val()) != false && Count != 0 && Price != 0 && Name != "")){
			Products_id.push(Product);
			Products_count.push(Count);

			if(localStorage.getItem('Seller_key') != null)
			{
				Seller_key = localStorage.getItem('Seller_key');
			}
			else{
				Seller_key = Date.now();
				localStorage.setItem('Seller_key', Seller_key);
			}


			$.ajax({
				url: "get-sum-product",
				type: "post",
				// async: false,
				
				data: {id: Product, count: Count, all_sum: All_Sum, key: Seller_key},
				success: function (response) {     
					if(response.status_reserve)
					{
						sum = response.sum;
						All_Sum = response.all_sum;

						// All_Sum += sum;
						product = '<li class="item" product="' + Product + '"><div class="info"><p>'+
						Name + '</p><div class="price"><span id="price">' + sum + '</span><span>грн</span></div>'+
						'</div><div class="count">' + Count + 'шт</div>'+
						'<div class="delete"></div></li>';
						$('.items').append(product);

						$(".input .container_count .count").val(1);
						$(".input .container_name .name").val("");
						$(".input .container_price .price").val("0грн");

						$(".check .sum").html(All_Sum + "грн");
						Count = 0;
						Price = 0;
						Name = "";
						Product = null;
					}
					else
					{
						alert("На складі недостатньо продукту!!!");
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
				// console.log(textStatus, errorThrown);
					alert("Щось пішло не так!!! (Запит по ціні продукту)");
				}
				
			});
		}
		else
		{
			alert("Щось не так!!!");
		}
	}
	else{
		alert("Щось пішло не так!!! Заборонено продавати даний продукт ще раз натисніть 'Відмінити' і повторіть спробу!");
	}
});

$(document).on("click", ".delete", function() {

	if(Staus_clear)
	{
		var product = $(this).parent()[0];
		var product_id = product.getAttribute('product');
		var index = Products_id.indexOf(product_id);
		// var product_count = Products_count[index];
		
		$.ajax({
			url: "delete-product-from-list-products",
			type: "post",
			// async: false,
			data: {id: product_id, index: index, key: Seller_key, products_id: Products_id, products_count: Products_count, all_sum: All_Sum},
			
			success: function (response) 
			{    
				if(response.status_update)
				{
					if(response.products_id.length >= 0)
					{
						Products_id = response.products_id;
						Products_count = response.products_count;
						$('.items').html(response.products_items);
						All_Sum = response.all_sum;
						$(".check .sum").html(All_Sum + "грн");
					}
					else alert("Щось пішло не так при видаленні з списку резервації!!! Рівень 3");										
				}
				else alert("Щось пішло не так при видаленні з списку резервації!!! Рівень 2");
			},
			error: function(jqXHR, textStatus, errorThrown) {
			// console.log(textStatus, errorThrown);
				alert("Щось пішло не так при видаленні з списку резервації!!! Рівень 1 Помилка!");
			}
		});
	}
	else alert("Щось пішло не так!!! Заборонено продавати даний продукт ще раз натисніть 'Відмінити' і повторіть спробу!");

});


$(document).on("click", ".action .cancel", function() 
{
	if(Staus_clear)
	{
		$.ajax({
			url: "clear-list-products",
			type: "post",
			// async: false,
			data: {key: Seller_key, products_id: Products_id, products_count: Products_count},
			success: function (response) 
			{     
				if(response.status_clear === true | response.status_clear == 1)
				{
					$('.items').empty();
					All_Sum = 0;
					$(".check .sum").html(All_Sum + "грн");
					Products_id.splice(0, Products_id.length);
					Products_count.splice(0, Products_count.length);
				}
				else if(response.status_clear === 2){
					Staus_clear = false;
					response.status.forEach(function(element) {
						$('.item[product = '+element+']').css({ "background-color": 'red'});									
					});
					alert("Щось пішло не так при очистці списку резервації. Ваші данні не сходяться з даними на складі!!! Заборонено продавати даний продукт ще раз натисніть 'Відмінити' і повторіть спробу! Рівень 3");
				}
				else{
					Staus_clear = false;
					alert("Щось пішло не так при очистці списку резервації!!! Рівень 21");
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
			// console.log(textStatus, errorThrown);
				alert("Щось пішло не так при видаленні з списку резервації!!! Рівень 1 Помилка!");
			}
		});
	}
	else
	{
		$('.items').empty();
		Staus_clear = true;
	}

});


$(document).on("click", ".action .buy", function() 
{
	if(Staus_clear)
	{
		$.ajax({
			url: "sale-products",
			type: "post",
			// async: false,
			data: {key: Seller_key, products_id: Products_id, products_count: Products_count},
			success: function (response) 
			{     
				// console.log(response.status_clear);
				if(response.status_clear === true | response.status_clear == 1)
				{
					$('.items').empty();
					All_Sum = 0;
					$(".check .sum").html(All_Sum + "грн");
					Products_id.splice(0, Products_id.length);
					Products_count.splice(0, Products_count.length);
				}
				else if(response.status_clear === 2){
					Staus_clear = false;
					response.status.forEach(function(element) {
						$('.item[product = '+element+']').css({ "background-color": 'red'});									
					});
					alert("Щось пішло не так при очистці списку резервації. Ваші данні не сходяться з даними на складі!!! Заборонено продавати даний продукт ще раз натисніть 'Відмінити' і повторіть спробу! Рівень 3");
				}
				else{
					Staus_clear = false;
					alert("Щось пішло не так при очистці списку резервації!!! Рівень 22");
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
			// console.log(textStatus, errorThrown);
				alert("Щось пішло не так при видаленні з списку резервації!!! Рівень 1 Помилка!");
			}
		});
	}
	else
	{
		$('.items').empty();
		Staus_clear = true;
	}

});



