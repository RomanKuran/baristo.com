// $(document).on("click", function() {
//     $("#sukes").hide();
// });

var action = null;
$("#coming_ok").on("click", function() {
    $.ajax({
        type: 'post',
        url: action,
        data: $('.modal-footer.coming').serialize(),
        success: function (response) {
            if(response.name){
                $('.b_close').click();
                if(action === 'plus')
                $("#sukes").text("Продукту '"+response.name+"' успішно добавлена кількість");
                else if(action === 'minus')
                $("#sukes").text("Продукту '"+response.name+"' успішно списана кількість");
            }
            else{
                $("#modal_error").text(response);
            }
        }
    });
});

$(document).on("click", ".btn.btn-success.btn-action", function() {
    var data_key = $(this).parent().parent()[0].getAttribute("data-key");
    $(".hidden_data_key").val(data_key);
    action = 'plus';
});

$(document).on("click", ".btn.btn-danger.btn-action", function() {
    var data_key = $(this).parent().parent()[0].getAttribute("data-key");
    $(".hidden_data_key").val(data_key);
    action = 'minus';
});