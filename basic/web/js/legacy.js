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
                        window.location.origin+window.location.pathname+'/update',
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
        },
        bonusadd : function () {
            var uid   = parseInt($("input[name='uid']").val());
            window.location.href = window.location.origin+'/bonus-add?u='+uid;
        },
        bonussub : function () {
            var uid   = parseInt($("input[name='uid']").val());
            window.location.href = window.location.origin+'/bonus-sub?u='+uid;
        }

    };
}();


var search = function() {
    var timerId = null;

    return {
        userselected:function(uid){
            window.location.href = window.location.origin+'/client-card?u='+uid;
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
                                $('.table-hover > tbody:last-child').append('<tr onclick="search.userselected('+obj.uid+')"><th scope="row">'+obj.fio+'</th><td>'+obj.phone+'</td><td>'+obj.cnum+'</td><td>'+obj.bsumm+'</td><td><i class="fa fa-plus-square" style="color: green;" aria-hidden="true" onclick="event.stopPropagation();search.goadd('+obj.uid+')"/>&nbsp;<i class="fa fa-minus-square"  style="color: red;" aria-hidden="true" onclick="event.stopPropagation();search.gosub('+obj.uid+')"/></td></tr>');
                            });
                        }
                    );
                }, 1000);
            }
        },
        goadd:function (uid) {
            window.event.cancelBubble = true;
            window.location.href = window.location.origin+'/bonus-add?u='+uid;
            return false;
        },
        gosub:function (uid) {
            window.event.cancelBubble = true;
            window.location.href = window.location.origin+'/bonus-sub?u='+uid;
            return false;
        },
    };
}();

var bonus = function() {
    return {
        add: function () {
            var summ  = parseInt($("input[name='summ']").val());
            var bsumm = parseInt($("input[name='bsumm']").val());
            var descr = $("input[name='descr']").val();
            var uid   = parseInt($("input[name='uid']").val());

            $("input[name='summ']").removeClass('lgc_haserror');
            $("input[name='bsumm']").removeClass('lgc_haserror');

            if (isNaN(bsumm)) {
                $("input[name='bsumm']").addClass('lgc_haserror');
            }
            
            if (isNaN(summ)) {
                $("input[name='summ']").addClass('lgc_haserror');

            }
            
            if (bsumm >= 0 && summ>=0) {
                $.post(
                    window.location.origin+window.location.pathname+'/addbonus',
                    {u:uid, bs:bsumm, s:summ, d:descr},
                    function (res) {
                        if (parseInt(res)>0){
                            var cur_bsumm = parseInt($("input[name='cur_bcumm']").val());
                            $("input[name='cur_bcumm']").val(cur_bsumm+bsumm);
                            bonus.addtransaction('a');
                        }
                    }
                );
            }

        },
        sub: function () {
            var summ  = parseInt($("input[name='summ']").val());
            var bsumm = parseInt($("input[name='bsumm']").val());
            var descr = $("input[name='descr']").val();
            var uid   = parseInt($("input[name='uid']").val());

            $("input[name='summ']").removeClass('lgc_haserror');
            $("input[name='bsumm']").removeClass('lgc_haserror');

            if (isNaN(bsumm)) {
                $("input[name='bsumm']").addClass('lgc_haserror');
            }

            if (isNaN(summ)) {
                $("input[name='summ']").addClass('lgc_haserror');

            }

            if (bsumm >= 0 && summ>=0) {
                $.post(
                    window.location.origin+window.location.pathname+'/subbonus',
                    {u:uid, bs:bsumm, s:summ, d:descr},
                    function (res) {
                        if (parseInt(res)>0){
                            var cur_bsumm = parseInt($("input[name='cur_bcumm']").val());
                            cur_bsumm = cur_bsumm-bsumm > 0 ? cur_bsumm-bsumm :  0;
                            $("input[name='cur_bcumm']").val(cur_bsumm);
                            bonus.addtransaction('s');
                        }
                    }
                );
            }

        },
        subcalc: function () {
            var bsumm = parseInt($("input[name='cur_bcumm']").val());
            var summ  = parseInt($("input[name='summ']").val());
            var max_bsumm = parseInt(summ*0.2);
            bsumm = bsumm > max_bsumm ? max_bsumm : bsumm;
            $("input[name='bsumm']").val(bsumm);
        },
        addtransaction: function (type) {
            var summ  = parseInt($("input[name='summ']").val());
            var bsumm = parseInt($("input[name='bsumm']").val());
            var descr = $("input[name='descr']").val();
            var date  = new Date().toLocaleDateString();

            bsumm = type == 'a' ? bsumm : -bsumm;

            $('.table-hover > tbody:last-child').append('<tr><th scope="row">'+date+'</th><td>'+summ+'</td><td>'+bsumm+'</td><td>'+descr+'</td></tr>');
            $('#list_transaction').show();
            $('#list_empty').hide();
        },
    };
}();