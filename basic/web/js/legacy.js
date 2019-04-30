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
        create : function () {
            $("input[name='cnum']").removeClass('lgc_haserror');

            var cnum  = parseInt($("input[name='cnum']").val());
            var fio   = $("input[name='fio']").val();
            var phone = $("input[name='phone']").inputmask('unmaskedvalue');
            var birth = $("input[name='birth']").val();
            var sex   = $("select[name='sex']").val();
            var ctype = $("input[name='ctype']").val();
            var csize = $("select[name='csize']").val();
            var fsize = $("select[name='fsize']").val();
            var uid   = parseInt($("input[name='uid']").val());

            if (cnum) {
                if ( uid > 0 ){
                    $.post(
                        window.location.href+'/update',
                        {uid:uid, cnum:cnum, fio:fio, phone:phone, birth: birth, sex: sex, ctype: ctype, csize: csize, fsize: fsize },
                        function (uid) {
                            if ( parseInt(uid) > 0 ) {
                                $("input[name='uid']").val(uid);
                            }
                        }
                    );
                } else {
                    $.post(
                        window.location.href+'/create',
                        { cnum:cnum, fio:fio, phone:phone, birth: birth, sex: sex, ctype: ctype, csize: csize, fsize: fsize },
                        function (uid) {
                            if ( parseInt(uid) > 0 ) {
                                $("input[name='uid']").val(uid);
                                $("button[name='newusersave']").text('Обновить');
                                $("button[name='subbonus']").prop('disabled', false);
                                $("button[name='addbonus']").prop('disabled', false);
                            }
                        }
                    );
                }
            } else {
                $("input[name='cnum']").addClass('lgc_haserror');
            }
        }
    };
}();


var search = function() {
    var timerId = null;

    return {
        userselected:function(uid){
            console.log(uid);
        },

        newsearch: function () {
            if (timerId) {clearTimeout(timerId);}
            $('.table-hover > tbody:last-child').empty();
            if ($("input[name='spattern']").val().length > 0 ) {
                timerId = setTimeout(function () {
                    $.post(
                        window.location.origin + '/search/newsearch',
                        {s: $("input[name='spattern']").val()},
                        function (data) {
                            $(data).each(function (item, obj) {
                                $('.table-hover > tbody:last-child').append('<tr onclick="search.userselected('+obj.uid+')"><th scope="row">'+obj.fio+'</th><td>'+obj.phone+'</td><td>'+obj.cnum+'</td><td>'+obj.bsumm+'</td></tr>');
                            });
                        }
                    );
                }, 1000);
            }
        }
    };
}();
