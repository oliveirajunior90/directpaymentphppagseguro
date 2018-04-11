$('#formPayment').submit(function(e){ e.preventDefault(); })

$.ajax({
    method: 'GET',
    url: 'http://localhost/meupag/api/auth',
    success: function(res) {
        PagSeguroDirectPayment.setSessionId(res);
    },

    error : function(err) {
        console.log(err);
    }
});

$('#formPayment').validate({

    submitHandler: function(event) {
        
        var fields = getFields();

        $.ajax({
            method: "POST",
            url: "http://localhost/meupag/api/payment-request",
            dataType: "JSON",
            data: fields,
        
            success: function(res) {
                $('#formResponse').html(res);
                //$('#formPayment').hide();
            },
        
            error: function(err) {
                console.log(err);
                transformResponseToObject(err.responseText);
            }
        });

        return false;
        
    }
});

function getFields() {
    var formdata = $("#formPayment").serializeArray();
    var data = {};
    $(formdata).each(function(index, obj){
        data[obj.name] = obj.value;
    });
    return data;
}

$('#bandeiraCartao').on('change', function() {
    $("#dadosCartao input").prop('disabled', false);
    var senderHash = PagSeguroDirectPayment.getSenderHash();
    $("#formPayment #senderHash").val(senderHash);
    console.log($("#formPayment #senderHash").val());
})

$('input[name=cvvCartao], input[name=numeroCartao], input[name=mesVencimentoCartao], input[name=anoVencimentoCartao]').keyup(function(){

    var param = {
        cardNumber: $("input[name=numeroCartao]").val(),
        brand: $("input[name=bandeiraCartao]").val(),
        cvv: $("input[name=cvvCartao]").val(),
        expirationMonth: $("input[name=mesVencimentoCartao]").val(),
        expirationYear: $("input[name=anoVencimentoCartao]").val(),
        success: function(res){
            $("#tokenCartao").val(res.card.token);
        },

        error: function(err){
            console.log(err);
        }
      }

    PagSeguroDirectPayment.createCardToken(param);
    
});

jQuery.extend(jQuery.validator.messages, {
    required: "Campo de preenchimento obrigatório.",
    remote: "Please fix this field.",
    email: "Use um endereço de e-mail válido.",
    url: "Please enter a valid URL.",
    date: "Please enter a valid date.",
    dateISO: "Please enter a valid date (ISO).",
    number: "Please enter a valid number.",
    digits: "Please enter only digits.",
    creditcard: "Please enter a valid credit card number.",
    equalTo: "Please enter the same value again.",
    accept: "Please enter a value with a valid extension.",
    maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
    minlength: jQuery.validator.format("Please enter at least {0} characters."),
    rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
    range: jQuery.validator.format("Please enter a value between {0} and {1}."),
    max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
    min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
});


/*
$("#numeroCartao").keyup(function(){
    PagSeguroDirectPayment.getBrand({
        cardBin: $("#numeroCartao").val(),
        success: function(res) {
            console.log(res);
        },
        error: function(err) {
            console.log(err);
        } ,
        complete: function() {
    
        }
      });
})
*/

function transformResponseToObject(text) {
    let regex = /\[([^\]]+?)\]\s\-\s([^\[]+)/g;
    let matches;
    const errors = [];
    const formResponse = $(".formErrorList");
    var i=0;
    while (matches = regex.exec(text)) {
        console.log(i);
        errors.push({
            key: matches[1],
            message: matches[2]
        });
        console.log(errors[i].key)
        $(".formErrorList").append("<li>"+errors[i].key+"</li>").show(2000);
        i++;
    }
}
