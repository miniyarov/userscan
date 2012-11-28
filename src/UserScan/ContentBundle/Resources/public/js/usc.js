$(document).ready(function() {
    $('form').submit(function (e) {
        //e.preventDefault();
        var inputs = $(this).find('.required');
        var $return = true;

        $.each(inputs, function() {
            if (this.value.length == 0 && $return === true) {
                this.focus();
                $return = 'Lütfen gerekli alanları girin';
            }
            //if (this.hasClass('email') && this.value)
        });

        if ($return === true) {
            return true;
        } else {
            alert($return);
            return false;
        }
    });

    $('#modal').modal({
        keyboard: true,
        backdrop: true
    });

    setTimeout(function() {
        $('#flash-message-container').slideUp();
    }, 5000);

    $('[data-toggle=modal]').click(function(e) {
        e.preventDefault();

        target = $(this).data('target');
        width = (typeof $(this).data('modalWidth') != 'undefined') ? $(this).data('modalWidth') : '350px';
        left = (typeof $(this).data('modalLeft') != 'undefined') ? $(this).data('modalLeft') : '60%';
        url = $(this).attr('href');

        if ($('#'+target).length == 0) {
            $('body').append('<div class="modal hide" id="'+target+'" style="width: '+width+', left: '+left+'"><div class="modal-header"><a class="close" href="#" data-dismiss="modal">×</a><h3>Yükleniyor...</h3></div></div>');
        }
        $('#'+target).css('width', width).css('left', left).html('<div class="modal-header"><a class="close" href="#" data-dismiss="modal">×</a><h3>Yükleniyor...</h3></div>').modal('show').load(url);
    });
    $("a.register").click(function(e){
        e.preventDefault();

        $("div.login").hide();
        $("div.register").show();
    });
    price = '49';
    $('#part-count').change(function() { 
        if ($(this).val() == 3) {
            $('.special-offer').show();
        }
        total = ($(this).val()) * price;
        $('.total span').html(total+' TL').effect("highlight", {}, 2000);;
        $('#total-price').val(total);
    });

    //iframe js's
    var tasks={
        "task":[
            {
                "task_id":"1",
                "task_text":"Bu birinci görev",
                "task_url":"http://www.enmoda.com"
            },
            {
                "task_id":"2",
                "task_text":"Bu ikinci görev",
                "task_url":"http://www.enmoda.com"
            },       
            {
                "task_id":"3",
                "task_text":"Bu da üçüncü ve son görev",
                "task_url":"http://www.enmoda.com"
            }
        ]
    };
    $(function() {
        var cnt = 0;
        var counter = setInterval(function() {
            if (cnt < 20) {
                $('.step-2 .black-label').html(cnt);
                cnt++;
            }
            else {
                clearInterval(counter);
                $('.step-2 .black-label').html('%100');
            }
        }, 100);
    });
    $('.overlay').show();
    $('.start-btn').click(function(){
        pdiv = $(this).attr('pdiv');
        adiv = $(this).attr('adiv');
        $('.'+pdiv).hide(300);
        $('.'+adiv).show(400);

        $('.step-2').delay(2000).hide(300);
        $('.overlay').delay(2000).hide(300);
        $('.step-3').delay(2400).show(500);

    });

    $('.task-complete').click(function(){
        $(this).css('background' , '#ccc')
        $('.step-3').hide();
        $('.step-4').show();

        $(function() {
            var cnt = 0;
            var counter = setInterval(function() {
                if (cnt < 20) {
                    $('.step-4 .black-label').html(cnt);
                    cnt++;
                }
                else {
                    clearInterval(counter);
                    $('.step-4 .black-label').html('%100');
                    console.log();
                    if($('.step-4 .black-label').html() == '%100') {
                        $('.step-4').hide();
                        $('.step-5').show();
                        $('.tester-form').show();
                        $('.overlay').show();
                    }
                }
            }, 300);
        });
    });

});