var createcard = function(){

    return {
        start : function () {
            var has_error = false;
            var bn   = parseInt($("input[name='sid']").val());
            var en   = parseInt($("input[name='eid']").val());
            var blnc = parseInt($("input[name='blnc']").val());
            var days = parseInt($("input[name='days']").val());

            if (!bn) {
                $("input[name='sid']").addClass('lgc_haserror');
                has_error = true;
            } else {
                $("input[name='sid']").removeClass('lgc_haserror');
            }

            if (!en) {
                $("input[name='eid']").addClass('lgc_haserror');
                has_error = true;
            } else {
                $("input[name='eid']").removeClass('lgc_haserror');
            }

            if ( !has_error && bn > en ) {
                $("input[name='sid']").addClass('lgc_haserror');
                $("input[name='eid']").addClass('lgc_haserror');
                has_error = true;
            }

            if(has_error) {
                return;
            }

            $.post(
                window.location.href+'/generate',
                { bn:bn, en:en, b:blnc, d:days },
                function (data) {
                    if (parseInt(data) > 0) {
                        $("label[name='last_num']").text('Номер последней успешно созданной карты:'+data);
                    }
                }
            ).fail(function () {
                $("label[name='last_num']").text('Ошибка создания карт');
            });
        }
    };
}();

var newclient = function(){
    return {
        start : function () {

        }
    };
}();
