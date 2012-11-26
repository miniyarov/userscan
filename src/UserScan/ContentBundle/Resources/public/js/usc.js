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

    $('.test-start').click(function(){ 
        $('li.cases .welcome').html('');
        $.each(tasks.task,function(i,task){
            $('.task_id').html('<span class="task-'+task.task_id+'">'+task.task_id+'</span>');
            $('.task_text').append( 
                task.task_id+'. Görev :'+
                task.task_text+'<br/>');
            $('body.iframe iframe').attr('src', task.task_url);
        });
        
        //console.log($('body.iframe iframe').attr('src'));
    });
    $('.prev-btn').click(function(){
    
    });
    $('.next-btn').click(function(){

    });
    $('.test-finish').click(function(){
        $('div.bar').animate({
            height: 200
        }, 500, function() {    
    });
  
    $('body.iframe iframe').animate({
        opacity: .5
    }, 500, function() {

    });
        $('.last-screen').show();
    });

});