<script>
    <?php
    if(!isset($form)){
        $form = 'form';
    }
    if(!isset($on_start)){
        $on_start = false;
    }
    if (!isset($auto_focus)) {
        $auto_focus = true;
    }
    ?>
    var validated = false;
    var buton_submit = false;
    var my_form = $('<?=$form?>');
    var name_class = '<?=$request?>';
    var on_start = '<?=$on_start?>';
    var auto_focus = '<?=$auto_focus?>';


    jQuery.fn.resetear = function () {
        $(this).each (function() { this.reset(); });
    }


    initialize();

    function initialize(){
        my_form.find("button[type=submit]").on('click',function(e){
            e.preventDefault();
            buton_submit = true;

            console.log("find: button");
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

        my_form.find('.form-group').append('<div class="help-block with-errors"></div>');
        
        if(on_start=='1'){  validate();  }

        if (auto_focus) {   $(':input:enabled:visible:first').focus();  }
    }

    @section('form_ajax')
        // dentro del archivo
        function form_ajax_submit() {
            $.ajax({
                url: my_form.attr('action'),
                type: 'POST',
                data: my_form.serialize(),
                dataType: 'json',
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
                    swal("Oops!", "Ocurrio un error al intentar procesar la petición.", "error");
                }
            });
        }
    @show


    function validate( envio_form=true ){

        console.log("validate: init");

        var data = my_form.serializeArray()
        data.push({name:'class',value:name_class});
        for(var i = 0; i < data.length; i++) {
            item = data[i];
            if(item.name == '_method'){
                data.splice(i,1);
            }
        }

        $.ajax({
            url: '<?=url('validation')?>',
            type: 'post',
            data: $.param(data),
            dataType: 'json',
            success: function(data){
                if(data.success){

                    $.each(my_form.serializeArray(), function(i, field) {
                        var father = $('#'+field.name).parent('.form-group');
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