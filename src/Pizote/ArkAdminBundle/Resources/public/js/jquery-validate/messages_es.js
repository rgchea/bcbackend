/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: ES (Spanish; Español)
 */
$.extend( $.validator.messages, {


        required: "Este campo es requerido.",
        remote: "Por favor arreglar este campo.",
        email: "Por favor ingresar un correo válido.",
        url: "Por favor ingresar una URL válida.",
        date: "Por favor ingresar una fecha válida.",
        dateISO: "Por favor ingresar una fecha válida ( ISO ).",
        number: "Por favor ingresar un número válido.",
        digits: "Por favor ingresar solamente dígitos.",
        creditcard: "Por favor ingresar un número válido de tarjeta de crédito.",
        equalTo: "Por favor ingresar el mismo valor de nuevo.",
        maxlength: $.validator.format( "No ingresar mas de {0} caracteres." ),
        minlength: $.validator.format( "Ingresar al menos {0} caracteres." ),
        rangelength: $.validator.format( "Ingresar un valor entre {0} y {1} caracteres de largo." ),
        range: $.validator.format( "Por favor ingresar un valor entre {0} y {1}." ),
        max: $.validator.format( "Por favor ingresar un valor menor o igual a {0}." ),
        min: $.validator.format( "Por favor ingresar un valor mayor o igual a {0}." )

} );