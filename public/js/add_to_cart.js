
$(".add__to__cart__btn a,.beef__cart__btn a").click(function(e) {
	e.preventDefault();
	//console.log($(this).parent().children("[name=product_id]").val());
    var product_id = $(this).parent().children("[name=product_id]").val();
    var quantity = $("[name=qtybutton]").val();
	$.ajax({
        url: "/?section=add_to_cart",
        //url: "reg.route.php",
        method: "POST",
        data: {
            "product_id": product_id,
            "quantity": quantity,
        }
    }).done(function(r){ //обработчик окончания операции
        //console.log(r);
        if (r.data !== undefined) {
            toastr.success('Это окно закроется автоматически','Товар добавлен в корзину');
            cart_box_render();
        } else if (r.errors !== undefined) {
            let error_str = "<ul>"
            $(r.errors).each(function(key,value){                //$(r.errors) - объект wrapper  - обёртка
                error_str += "<li>"+value+"</li>";
            });
            error_str +="</ul>";
        }
    });
})
