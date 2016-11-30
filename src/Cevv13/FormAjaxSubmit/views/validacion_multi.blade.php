<script>

/*
    {{--   Variables Obligatorias: enviar como parametros   --}}
    var my_form = '';
    var name_class = '';

    {{--   Variables Opcionales   --}}
    var on_start = $(this).data('onstart');
    var auto_focus = $(this).data('autofocus');


    {{--   Variables de control internas   --}}
    var validated = false;
    var buton_submit = false;
    var auxiliar_form_a_reset = '';



    {{--   Verificacion de Variables  --}}
    if (typeof on_start == "undefined"){
        alert("Variable on_start no definida");
        on_start = false;
    }

    if (typeof auto_focus == "undefined"){
        alert("Variable auto_focus no definida");
        auto_focus = false;
    }

    if (typeof my_form == "undefined"){
        alert("Variable my_form no definida");
        my_form = $('#myform');
    }
*/


    var my_form = $('form'); // evaluar si lleva el #
    var name_class = '';

    var on_start = false;
    var auto_focus = true;

    var validated = false;
    var buton_submit = false;
    var auxiliar_form_a_reset = my_form;

    {{--  Para cargar mis funciones auxiliares  --}}
    @section('mi_funciones') @show

    jQuery.fn.resetear = function () {
        $(this).each (function() { this.reset(); });
    }


    initialize();

    function initialize(){
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $("body").find("button[type=submit]").on('click',function(e){
            e.preventDefault();
            buton_submit = true;

            {{--   Verificacion de Variables  --}}
            if (typeof $(this).data('form') != "undefined"){
                my_form = $( '#' + $(this).data('form') );
                auxiliar_form_a_reset = my_form;
            }

            if (typeof $(this).data('class') != "undefined"){
                name_class = 'App/Http/Requests/'+$(this).data('class')+'Request';
            }

            if (typeof $(this).data('onstart') != "undefined"){
                on_start = $(this).data('onstart');
            }

            if (typeof $(this).data('autofocus') != "undefined"){
                auto_focus = $(this).data('autofocus');
            }

            console.log("find: button");

            console.log("variables");
            console.log("on_start " + on_start);
            console.log("auto_focus " + auto_focus);
            console.log("auxiliar_form_a_reset " + auxiliar_form_a_reset);
            console.log(my_form);

            console.log("name_class: " + name_class);
            console.log(" url action: " + my_form.attr('action') );


            validate();

            my_form.find(':input').each(function(){
                console.log("find: input");
                $(this).on('change',function(){
                    console.log("find: change");
                    validate( envio_form=false );
                });
            });
        });
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

        my_form.find('.form-group').append('<div class="help-block with-errors"></div>');
        
        if(on_start=='1'){  validate();  }

        if (auto_focus) {   $(':input:enabled:visible:first').focus();  }
    } // fin de initialize

    @section('form_ajax')
        // dentro del archivo
        function form_ajax_submit() {
            $.ajax({
                url: my_form.attr('action'),
                type: 'POST',
                data: my_form.serialize(),
                dataType: 'json',

                //cache: false,
                //contentType: false,
                //processData: false,
                //contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                //contentType: 'multipart/form-data; charset=UTF-8',

                success: function (check) {
                    console.log('ejecucion success submit: '); console.log(check);
                    @section('form_ajax_success')
                        setTimeout(function () {
                            swal({title: check.title, text: check.text, type: check.type, html: true});
                        }, 100);
                    @show
                },
                error: function (xhr, status) {
                    console.log('error en submit: '); console.log(xhr); console.log(status);
                    swal("Oops!", "Ocurrio un error al intentar procesar la petición. Intenta nuevamente", "error");
                }
            });
        }
    @show


    function validate( envio_form ){

        if (typeof envio_form == "undefined"){
            envio_form = true;
        }

        console.log("validate: init");
        console.log("envio_form: " + envio_form);

        var data = my_form.serializeArray()
        data.push({name:'class',value:name_class});
        for(var i = 0; i < data.length; i++) {
            item = data[i];
            if(item.name == '_method'){
                data.splice(i,1);
            }
        }

        $.ajax({
            url: '{{url('validation')}}',
            type: 'post',
            data: $.param(data),
            dataType: 'json',
            success: function(data){
                if(data.success){

                    $.each(my_form.serializeArray(), function(i, field) {
                        var father = $('#'+field.name).parent('.form-group');
                        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //console.log("father" + father);
                        father.removeClass('has-error');
                        father.addClass('has-success');
                        //father.find('.col-md-6').append('<span class="fa fa-check form-control-feedback"></span>');
                        father.find('.help-block').html('');
                    });

                    validated = true;       console.log("validate: true ---------------- true");
                    if(buton_submit==true && envio_form==true){
                        console.log("validate: submit");
                        //my_form.submit();
                        form_ajax_submit();
                    }
                } else {
                    var campos_error = [];

                    $.each(data.errors,function(key, data){
                        var campo = $('#'+key);
                        var father = campo.parents('.form-group');

                        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //console.log("key:" + key);console.log("data:" + data);console.log("campo:" + campo);console.log("father"); console.log(father);

                        father.removeClass('has-success');
                        father.addClass('has-error');
                        //father.find('.col-md-6').append('<span class="fa fa-close form-control-feedback"></span>');
                        father.find('.help-block').html(data[0]);
                        campos_error.push(key);
                    });

                    $.each(my_form.serializeArray(), function(i, field) {
                        if ($.inArray(field.name, campos_error) === -1)
                        {
                            var father = $('#'+field.name).parents('.form-group');  // ori var father = $('#'+field.name).parent('.form-group');
                            father.removeClass('has-error');
                            father.addClass('has-success');
                            //father.find('.col-md-6').append('<span class="fa fa-check form-control-feedback"></span>');
                            father.find('.help-block').html('');
                        }
                    });

                    validated = false;
                    buton_submit = false;
                }
            },
            error: function(xhr){
                console.log(xhr.status);
            }
        });
        return false;
    }
</script>