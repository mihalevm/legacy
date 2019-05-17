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
    var timerId = null;
    var nCard   = 0;

    return {
        create : function () {
            var cnum  = parseInt($("input[name='cnum']").inputmask('unmaskedvalue'));
            var bblnc = parseInt($("input[name='bblnc']").inputmask('unmaskedvalue'));
            var fio   = $("input[name='fio']").val();
            var phone = $("input[name='phone']").inputmask('unmaskedvalue');
            var birth = $("input[name='birth']").val();
            var sex   = $("select[name='sex']").val();
            var ctype = $("input[name='ctype']").val();
            var csize = $("select[name='csize']").val();
            var fsize = $("select[name='fsize']").val();
            var uid   = parseInt($("input[name='uid']").val());

            var birth_test = birth.split('.');
            var birth_to_date = new Date(birth_test[2],birth_test[1]-1,birth_test[0]);
            $("input[name='birth']").removeClass('lgc_haserror');
            if (birth_test[2] != birth_to_date.getFullYear() || birth_test[1] != birth_to_date.getMonth()+1 || birth_test[0] != birth_to_date.getDate()){
                $("input[name='birth']").addClass('lgc_haserror');
                return;
            }

            if (cnum && !$("input[name='cnum']").hasClass('lgc_haserror')) {
                $('.loader').css('visibility', 'visible');
                if ( uid > 0 ){
                    $.post(
                        window.location.origin+window.location.pathname+'/update',
                        {uid:uid, cnum:cnum, fio:fio, phone:phone, birth: birth, sex: sex, ctype: ctype, csize: csize, fsize: fsize },
                        function (uid) {
                            if ( parseInt(uid) > 0 ) {
                                $("input[name='uid']").val(uid);
                            }
                        }
                    ).always(function() {
                        $('.loader').css('visibility', 'hidden');
                    }).fail(function() {
                        window.location.href = 'search/error';
                    });
                } else {
                    $.post(
                        window.location.href+'/create',
                        { cnum:cnum, bb:bblnc, nc:nCard, fio:fio, phone:phone, birth: birth, sex: sex, ctype: ctype, csize: csize, fsize: fsize },
                        function (uid) {
                            if ( parseInt(uid) > 0 ) {
                                $("input[name='uid']").val(uid);
                                $("button[name='newusersave']").text('Обновить');
                                $("button[name='subbonus']").prop('disabled', false);
                                $("button[name='addbonus']").prop('disabled', false);
                            }
                        }
                    ).always(function() {
                        $('.loader').css('visibility', 'hidden');
                    }).fail(function() {
                        window.location.href = 'search/error';
                    });
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
        },
        transactions : function () {
            var uid   = parseInt($("input[name='uid']").val());
            window.location.href = window.location.origin+'/transactions?u='+uid;
        },
        newcard : function () {
            var card_number = $("input[name='cnum']").inputmask('unmaskedvalue');
            if (timerId) {clearTimeout(timerId);}

            $("input[name='cnum']").removeClass('lgc_haserror');
            $("input[name='bblnc']").val(0);
            nCard = 0;

            if ( parseInt(card_number) > 0 ) {
                timerId = setTimeout(function () {
                    $('.loader').css('visibility', 'visible');
                    $("button[name='newusersave']").prop('disabled',true);
                    $.post(
                        window.location.origin+window.location.pathname+'/newcard',
                        {c: card_number},
                        function (data) {
                            if (null != data) {
                                if (data.is_used == 'Y'){
                                    $("input[name='cnum']").addClass('lgc_haserror');
                                    $.notify({	message: 'Данная карта уже назначена другому клиенту' },{	type: 'danger', delay:10000, offset:{x:0, y:100}, placement: {from: "top",align: "center"},});
                                    return;
                                }
                                if (data.disabled == 'Y'){
                                    $.notify({	message: 'Данная карта заблокирована и не может быть использована' },{	type: 'danger', delay:10000, offset:{x:0, y:100},placement: {from: "top",align: "center"},});
                                    $("input[name='cnum']").addClass('lgc_haserror');
                                    return;
                                }
                                $("input[name='bblnc']").val(data.bsumm);
                                $.notify({	message: 'Карта найдена' },{	type: 'success', delay:10000, offset:{x:0, y:100},placement: {from: "top",align: "center"},});
                            } else {
                                $.notify({	message: 'Карта будет создана' },{	type: 'info', delay:10000, offset:{x:0, y:100},placement: {from: "top",align: "center"},});
                                nCard = 1;
                            }
                        }
                    ).always(function () {
                        $("button[name='newusersave']").prop('disabled',false);
                        $('.loader').css('visibility', 'hidden');
                    }).fail(function() {
                        window.location.href = 'search/error';
                    });
                }, 1000);
            }
        },
        delete_client : function () {
            var uid = parseInt($("input[name='uid']").val());
            $.post(
                window.location.origin+window.location.pathname+'/delete',
                {u: uid},
                function (data) {
                    window.location.href = '/';
                }
            ).fail(function() {
                window.location.href = 'search/error';
            });
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
                    $('.loader').css('visibility', 'visible');
                    $.post(
                        window.location.origin + '/search/newsearch',
                        {s: $("input[name='spattern']").val()},
                        function (data) {
                            if (data.length > 0) {
                                $(data).each(function (item, obj) {
                                    $('.table-hover > tbody:last-child').append('<tr onclick="search.userselected('+obj.uid+')"><th scope="row">'+obj.fio+'</th><td>'+obj.phone+'</td><td>'+obj.cnum+'</td><td>'+obj.bsumm+'</td><td><i class="fa fa-plus-square" title="Зачисление бонусов" style="color: green; font-size: 25px;" aria-hidden="true" onclick="event.stopPropagation();search.goadd('+obj.uid+')"/>&nbsp;<i class="fa fa-minus-square" title="Списание бонусов" style="color: red; font-size: 25px;" aria-hidden="true" onclick="event.stopPropagation();search.gosub('+obj.uid+')"/></td></tr>');
                                });
                            } else {
                                $('.table-hover > tbody:last-child').append('<tr><td colspan="5" style="text-align: center;">Ничего не найдено</td></tr>');
                            }
                        }
                    ).always(function() {
                        $('.loader').css('visibility', 'hidden');
                    }).fail(function() {
                        window.location.href = 'search/error';
                    });
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
                ).fail(function() {
                    window.location.href = 'search/error';
                });
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
                ).fail(function() {
                    window.location.href = 'search/error';
                });
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

var transaction = function() {
    var edit_tid = null;

    return {
        refresh: function () {
            var sd  = $('#transactionsform-sdate').val();
            var ed  = $('#transactionsform-edate').val();
            var uid = parseInt($("input[name='uid']").val());
            $.pjax({url: window.location.origin+window.location.pathname+'?u='+uid+'&s='+sd+'&e='+ed, container:"#transactions_list", timeout:2e3});
        },
        save : function () {
            var bo = $("input[name='bonus_op']:checked").val()
            var s  = parseInt($("input[name='summ']").inputmask('unmaskedvalue'));
            var bs = parseInt($("input[name='bsumm']").inputmask('unmaskedvalue'));
            var d  = $("input[name='descr']").val();

            $("input[name='summ']").removeClass('lgc_haserror');
            $("input[name='bsumm']").removeClass('lgc_haserror');

            if (isNaN(s)){
                $("input[name='summ']").addClass('lgc_haserror');
                return;
            }

            if (isNaN(bs)){
                $("input[name='bsumm']").addClass('lgc_haserror');
                return;
            }

            $.post(
                window.location.origin+window.location.pathname+'/savetransaction',
                {t:edit_tid, bo:bo, s:s, bs:bs, d:d},
                function (data) {
                    $('#editTransaction').modal('hide');
                }
            ).fail(function() {
                window.location.href = 'search/error';
            });
        },
        edit: function (tid) {
            edit_tid = tid;

            $('#editTransaction').on('hidden.bs.modal', function (e) {
                transaction.refresh();
                edit_tid = null;
            })

            $.post(
                window.location.origin+window.location.pathname+'/gettransaction',
                {t:tid},
                function (data) {
                    $("input[name='pay_date']").val(data.tdate);
                    $("input[name='summ']").val(data.summ);
                    $("input[name='bsumm']").val(data.bsumm);
                    $("input[name='descr']").val(data.tdesc);
                    $("input[name=bonus_op][value=" + data.ttype + "]").prop('checked', true);
                    $('#editTransaction').modal('show');
                }
            ).fail(function() {
                window.location.href = 'search/error';
            });
        }
    };
}();