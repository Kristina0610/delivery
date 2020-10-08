$(document).ready(function(){
	$('input[type=radio][name=payment_type]').change(function(e) {
		//console.log(e.target.value);
		if (e.target.value=="cash") {
			$(".change_for").show();
		} else{
			$(".change_for").hide();
		}
	})
})
$(document).ready(function() {
	cart_box_render();
    $(document).on("click", ".cartbox__item__remove", function(e) {
        var product_id = $(this).parent().data("product_id");
        $.ajax({
            url:"/?section=remove_from_cart",
            method: "POST",
            data: {
                "product_id": product_id
            }
        }).done(function(r) {
            if (r.data !== undefined) {
                cart_box_render();
            }
        })
    })
});