function cart_box_render() {        //событие полной загрузки элемента
	$.ajax({
		url: "/?section=cart_box",
		method: "GET"
	}).done(function(r) {
		//console.log(r);
		$(".cartbox__items").empty();
		$(r.data.items).each(function(key,product){
			//console.log(product);
			var item = $("<div></div>").addClass("cartbox__item").attr("data-product_id", product.product_id);

			var img = $("<div>").addClass("cartbox__item__thumb").append(
				$("<a></a>").attr("href","/?section=menu_details&product_id="+product.product_id).append(
					$("<img>").attr("src","images/product/"+product.product_id+"/"+product.photo_small).attr("alt",product.name)
					)
				);
			item.append(img);

			var content = $("<div>").addClass("cartbox__item__content").append(
				$("<h5>").append(
					$("<a>").attr("href","/?section=menu_details&product_id="+product.product_id).addClass("product-name").text(product.name)
					)
				).append(
					$("<p>").text("Кол-во: ").append(
						$("<span>").text(product.quantity + " шт.")
					)
				).append(
					$("<span>").addClass("price").text((product.price * product.quantity)+ " руб.")
				);
			item.append(content);


			var button = $("<button></button>").addClass("cartbox__item__remove").append($("<i></i>").addClass("fa fa-trash"));
			item.append(button);

			$(".cartbox__items").append(item);
		});
		//console.log(r.data.total_price);
		if (r.data.total_price > 0) {
			$(".cartbox__buttons").show();
		} else {
			$(".cartbox__buttons").hide();
		}
		$(".shopping__cart > .shop__qun > span").html(r.data.items.length);
		$(".grandtotal > .price").html(r.data.total_price + " руб.");
	})
}