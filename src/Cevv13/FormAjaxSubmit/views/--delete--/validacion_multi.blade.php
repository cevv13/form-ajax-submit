<script>
{{--
    --  Variables Obligatorias: enviar como parametros  --
        data-form="formName"
        data-class="RequestName"

    --  Variables Opcionales  --
        data-onstart="true"
        data-autofocus="true"

    --   Variables de control internas   --
        data-lock="true"
        data-lock-text=" Enviando..."

        data-off-events="false"        :  controla si se remueve el evento change de los inputs antes de enviar el form, valor por defecto: true
        data-valid-only-submit="true" :  controla si se valida algun input al detectarse un cambio de valor. si es false, se validan los inputs solo al darle "guardar". valor por defecto: false

    Ej:  <button type="submit" class="btn btn-success" data-form="formMail" data-lock="true" data-lock-text=" Verificando Servidor..." data-off-events="false" data-valid-only-submit="true" data-class="ConfigMail"><i class="fa fa-save"></i> Guardar </button>
--}}
    var my_form = $('form'); // evaluar si lleva el #
    var name_class = '';

    var on_start = false;
    var auto_focus = true;

    var validated = false;
    var buton_submit = false;
    var auxiliar_form_a_reset = my_form;

    var lock_button_on_submit = false;
    var text_button_on_submit = " Guardar";
    var off_events_in_submit = true;
    var validate_only_in_submit = false;

    {{--  Para cargar mis funciones auxiliares  --}}
    @section('mi_funciones') @show

    jQuery.fn.resetear = function () {
        $(this).each (function() { this.reset(); });

        console.log(' -------- en resetear() ------- ');
    }

    jQuery.fn.removerClases = function () {
        console.log(' -------- en removerClases() ------- ');

        $.each( $(this).serializeArray(), function(i, field) {
            {{--   test: evitar erros en select multiple, ejeplo:  campo[]  --}}
            try {
                var father = $('#'+field.name).parent('.form-group');
            } catch(err){
                console.log(' -------- error evitado: en removerClases() ------- ');
                return false;
            }

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //console.log("father:"); console.log(father); console.log( $('#'+field.name) );
            father.removeClass('has-error');
            father.addClass('has-success');
            //father.find('.col-md-6').append('<span class="fa fa-check form-control-feedback"></span>');
            father.find('.help-block').html('');
        });
    }

    jQuery.fn.silenciarEventos = function () {
        console.log(' -------- en silenciarEventos() ------- ');console.log($(this));

        $(this).find(':input').each(function(){
            $(this).off("change");
            console.log("borrado find: change");
            console.log($(this));
        });
    }


    initialize();

    function initialize(){
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $("body").find("button[type=submit]").on('click',function(e){
            e.preventDefault();
            buton_submit = true;

            {{--   test: disable button submit  --}}
            lock_button_on_submit = $(this);
            if (typeof lock_button_on_submit.data('lock') == "undefined"  ||  lock_button_on_submit.data('lock') ){
                lock_button_on_submit.prop("disabled",true);
                if ( typeof $(this).data('lock-text') != "undefined" ){
                    text_button_on_submit = $(this).text();
                    lock_button_on_submit.text( $(this).data('lock-text') );
                }else
                lock_button_on_submit.text(" Guardando...");
            }

            if (typeof $(this).data('off-events') != "undefined"){
                off_events_in_submit = $(this).data('off-events');
            }

            if (typeof $(this).data('valid-only-submit') != "undefined"){
                validate_only_in_submit = $(this).data('valid-only-submit');
            }


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

            {{--   test: disable button submit  --}}
            if ( !validate_only_in_submit ){
                my_form.find(':input').each(function(){
                    console.log("find: input");
                    $(this).on('change',function(){
                        console.log("find: change");
                        validate( envio_form=false );
                    });
                });
            }else {  off_events_in_submit = false;  }

        });
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

        my_form.find('.form-group').append('<div class="help-block with-errors"></div>');
        
        if(on_start=='1'){  validate();  }

        if (auto_focus) {   $(':input:enabled:visible:first').focus();  }
    } // fin de initialize

    @section('form_ajax')
        // dentro del archivo
        function form_ajax_submit() {

            {{--   test: disable button submit  --}}
            if (typeof lock_button_on_submit.data('lock') == "undefined"  ||  lock_button_on_submit.data('lock') ){
                lock_button_on_submit.prop("disabled",true); console.log("desactivado boton en submit.");

                if ( typeof lock_button_on_submit.data('lock-text') != "undefined" ){
                    text_button_on_submit = lock_button_on_submit.text();
                    lock_button_on_submit.text( lock_button_on_submit.data('lock-text') );
                }else
                    lock_button_on_submit.text(" Guardando..." );
            }


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
                    console.log('ejecucion success submit: '); console.log(check);  console.log(my_form.serialize());
                    @section('form_ajax_success')
                        setTimeout(function () {
                            swal({title: check.title, text: check.text, type: check.type, html: true});
                        }, 100);
                    @show

                },
                error: function (xhr, status) {
                    console.log('error en submit: '); console.log(xhr); console.log(status);
                    swal("Oops!", "Ocurrio un error al intentar procesar la petición. Intenta nuevamente", "error");
                },
                complete : function(xhr, status) {
                    {{--   test: disable button submit  --}}
                    lock_button_on_submit.prop("disabled",false);
                    lock_button_on_submit.text( text_button_on_submit );
                    lock_button_on_submit.addClass('fa fa-save');
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

                {{--   test: disable button submit  --}}
                lock_button_on_submit.prop("disabled",false);
                lock_button_on_submit.text( text_button_on_submit );
                lock_button_on_submit.addClass('fa fa-save');

                if(data.success){

                    my_form.removerClases();
                    /*
                    $.each(my_form.serializeArray(), function(i, field) {
                        //var father = $('#'+field.name).parent('.form-group');

                        {{--   test: evitar erros en select multiple, ejeplo:  campo[]  --}}
                        try {
                            var father = $('#'+field.name).parent('.form-group');
                        } catch(err){
                            console.log(' -------- error evitado ------- ');
                            return false;
                        }

                        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //console.log("father" + father);
                        father.removeClass('has-error');
                        father.addClass('has-success');
                        //father.find('.col-md-6').append('<span class="fa fa-check form-control-feedback"></span>');
                        father.find('.help-block').html('');
                    });
                    */

                    validated = true;       console.log("validate: true ---------------- true");
                    if(buton_submit==true && envio_form==true){
                        console.log("validate: submit");
                        if(off_events_in_submit) my_form.silenciarEventos();
                        form_ajax_submit();
                    }
                } else {
                    var campos_error = [];

                    $.each(data.errors,function(key, data){
                        var campo = $('#'+key);
                        var father = campo.parents('.form-group');

                        ////////////////////////////////////////////////////////////////////////////////////////////////
                        console.log("key:" + key);console.log("data:" + data);console.log("campo:" + campo);console.log("father"); console.log(father);

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
