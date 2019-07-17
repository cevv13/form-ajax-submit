<script>
    var my_form = $('form');
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

    @section('mi_funciones') @show

        jQuery.fn.resetear = function () {
        $(this).each (function() { this.reset(); });
    }

    jQuery.fn.removerClases = function () {
        $.each( $(this).serializeArray(), function(i, field) {
            try {
                var father = $('#'+field.name).parent('.form-group');
            } catch(err){
                return false;
            }
            father.removeClass('has-error');
            father.addClass('has-success');
            father.find('.help-block').html('');
        });
    }

    jQuery.fn.silenciarEventos = function () {
        $(this).find(':input').each(function(){
            $(this).off("change");
        });
    }


    initialize();

    function initialize(){
        $("body").find("button[type=submit]").on('click',function(e){
            e.preventDefault();
            buton_submit = true;

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
            validate();
            if ( !validate_only_in_submit ){
                my_form.find(':input').each(function(){
                    $(this).on('change',function(){
                        validate( envio_form=false );
                    });
                });
            }else {  off_events_in_submit = false;  }

        });


        my_form.find('.form-group').append('<div class="help-block with-errors"></div>');

        if(on_start=='1'){  validate();  }

        if (auto_focus) {   $(':input:enabled:visible:first').focus();  }
    } // fin de initialize

    @section('form_ajax')
    // dentro del archivo
    function form_ajax_submit() {

        {{--   test: disable button submit  --}}
        if (typeof lock_button_on_submit.data('lock') == "undefined"  ||  lock_button_on_submit.data('lock') ){
            lock_button_on_submit.prop("disabled",true);

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
            success: function (check) {
                @section('form_ajax_success')
                setTimeout(function () {
                    swal({title: check.title, text: check.text, type: check.type, html: true});
                }, 100);
                @show

            },
            error: function (xhr, status) {
                swal("Oops!", "Ocurrio un error al intentar procesar la petición. Intenta nuevamente", "error");
            },
            complete : function(xhr, status) {
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
                lock_button_on_submit.prop("disabled",false);
                lock_button_on_submit.text( text_button_on_submit );
                lock_button_on_submit.addClass('fa fa-save');

                if(data.success){
                    my_form.removerClases();
                    validated = true;
                    if(buton_submit==true && envio_form==true){
                        if(off_events_in_submit) my_form.silenciarEventos();
                        form_ajax_submit();
                    }
                } else {
                    var campos_error = [];

                    $.each(data.errors,function(key, data){
                        var campo = $('#'+key);
                        var father = campo.parents('.form-group');
                        father.removeClass('has-success');
                        father.addClass('has-error');
                        father.find('.help-block').html(data[0]);
                        campos_error.push(key);
                    });

                    $.each(my_form.serializeArray(), function(i, field) {
                        if ($.inArray(field.name, campos_error) === -1) {
                            var father = $('#'+field.name).parents('.form-group');
                            father.removeClass('has-error');
                            father.addClass('has-success');
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