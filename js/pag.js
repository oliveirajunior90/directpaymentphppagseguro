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
            dataType : "JSON",
            data: fields,
        
            success: function(res) {
                console.log(res);
            },
        
            error: function(err) {
                console.log(err);
            }
        });

        return false;
        
    }
});

/*
dataForm = {
    Produto1: "",
    
    anoVencimentoCartao : "2030",

    bandeiraCartao : "VISA",

    cpf : "131.962.147-38",

    cvvCartao : "123",

    ddd : "21",

    email : "oliveira_junior90@hotmail.com",

    mesVencimentoCartao : "12",

    nome : "Almir Barbosa de Oliveira Júnior",

    numeroCartao : "4111111111111111",

    telefone : "219745201",

    tokenCartao : "636b2022379a4a138586f97b4ddedb5a",

    senderHash : PagSeguroDirectPayment.getSenderHash()
}


$.ajax({
    method: "POST",
    url: "http://localhost/meupag/api/payment-request",
    dataType : "JSON",
    async: false,
    data: dataForm,

    success: function(res) {
        console.log(res);
    },

    error: function(err) {
        console.log(err);
    }
});

*/
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
            console.log(res);
            $("#tokenCartao").val(res.card.token);
        },

        error: function(err){
            console.log(err);
        }
      }

    PagSeguroDirectPayment.createCardToken(param);
    
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
