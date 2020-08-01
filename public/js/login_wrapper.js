$("#reg_submit").click(function(e){ // #- если id, . - если class, [] - name
    e.preventDefault();
    $.ajax({
        url: "/?section=reg",
        //url: "reg.route.php",
        method: "POST",
        data: {
            "firstname":$("#firstname").val(),
            "secondname":$("#secondname").val(),
            "phone":$("#phone").val(),
            "password":$("#password").val(),
            "re_password":$("#re_password").val()
        }
    }).done(function(r){ //обработчик окончания операции
        console.log(r);
        //$(".kristi-success,.kristi-errors").html("");
        $("[name=success],[name=errors]").html("");
        if (r.data !== undefined) {
            console.log("success");
            $("#s1").hide(200);
            $("#s2").show(200);
        } else if (r.errors !== undefined) {
            /*let error_str = "<ul>"
            $(r.errors).each(function(key,value){                //$(r.errors) - объект wrapper  - обёртка
                error_str += "<li>"+value+"</li>";
            });
            error_str +="</ul>";
            $(".kristi-errors").html(error_str);                       //$(".errors") - selectор
            //console.log(1);*/                                 //  СХЕМА ВЫВОДА НЕ ИМЕННЫХ ОШИБОК
            $("[name=errors]").html("");                        //Схема ВЫВОДА ИМЕННЫХ ОШИБОК
            for(key in r.errors) {
                $("#error-"+key).html(r.errors[key]);
            }
        }
    });
});
$("#phone").blur(function(e){   //срабатывает при уходе со строки
    $.ajax({
        url: "/?section=reg_blur",
        method: "POST",
        data: {
            "phone":$("#phone").val()
        }
    }).done(function(r){
        //console.log("123");
        $("[name=warning]").html("");
        if (r.data !== undefined) {
            console.log("success");
        } else if (r.warning !== undefined) {
            $("[name=warning]").html("");
            for(key in r.warning) {
                $("#warning-"+key).html(r.warning[key]);
            }
        }
    })
});
$("#reg_finish").click(function(e){                             // #- если id, . - если class, [] - name
    e.preventDefault();
    $.ajax({
        url: "/?section=reg_finish",
        //url: "reg.route.php",
        method: "POST",
        data: {
            "code":$("#code").val(),
        }
    }).done(function(r){                                       //обработчик окончания операции
        //$(".kristi-success,.kristi-errors").html("");
        $("#s2").hide(200);
        $("#s3").show(200);

        $("[name=success],[name=errors]").html("");
        if (r.data !== undefined) {
            console.log("success");
            setTimeout(function(){                               //АВТОМАТ ПЕРЕЗАГРУЗКА СТР ЧЕРЕЗ 2СЕК
                location.reload(); 
            }, 2000);
        } else if (r.errors !== undefined) {
            let error_str = "<ul>"
            $(r.errors).each(function(key,value){                //$(r.errors) - объект wrapper  - обёртка
                error_str += "<li>"+value+"</li>";
            });
            error_str +="</ul>";
            $("#s3").html(error_str); 
        }
    });
});
$("#auth_submit").click(function(e){ // #- если id, . - если class, [] - name
    e.preventDefault();
    $.ajax({
        url: "/?section=auth",
        method: "POST",
        data: {
            "phone":$("#auth_phone").val(),
            "password":$("#auth_password").val()
        }
    }).done(function(r){ //обработчик окончания операции
        console.log(r);
        $("[name=success],[name=errors]").html("");
        if (r.data !== undefined) {
            location.reload();
        } else if (r.errors !== undefined) {
            $("[name=errors]").html("");                        //Схема ВЫВОДА ИМЕННЫХ ОШИБОК
            for(key in r.errors) {
                $("#error-"+key).html(r.errors[key]);
            }
        }
    });
});