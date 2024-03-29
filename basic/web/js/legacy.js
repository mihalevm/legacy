var newclient = function(){
    var timerId = null;
    var nCard   = null;

    return {
        create : function () {
            var cnum  = parseInt($("input[name='cnum']").inputmask('unmaskedvalue'));
            var bblnc = parseInt($("input[name='bblnc']").inputmask('unmaskedvalue'));
            var fio   = $("input[name='fio']").val();
            var phone = $("input[name='phone']").inputmask('unmaskedvalue');
            var birth = $("input[name='birth']").val();
            var sex   = $("select[name='sex']").val();
            var sell_point = $("select[name='sell_point']").val();
            var ctype = $("input[name='ctype']").val();
            var csize = $("select[name='csize']").val();
            var fsize = $("select[name='fsize']").val();
            var uid   = parseInt($("input[name='uid']").val());
            var coid  = $("select[name='company']").val();

            var birth_test = birth.split('.');
            var birth_to_date = new Date(birth_test[2],birth_test[1]-1,birth_test[0]);
//            $("input[name='birth']").removeClass('lgc_haserror');
            if (birth_test[2] != birth_to_date.getFullYear() || birth_test[1] != birth_to_date.getMonth()+1 || birth_test[0] != birth_to_date.getDate()){
//                $("input[name='birth']").addClass('lgc_haserror');
//                return;
                birth = '';
            }

            $("input[name='fio']").removeClass('lgc_haserror');
            if (fio.length == 0) {
                $("input[name='fio']").addClass('lgc_haserror');
                return;
            }

            $("input[name='phone']").removeClass('lgc_haserror');
            if (phone.length == 0) {
                $("input[name='phone']").addClass('lgc_haserror');
                return;
            }

            if (cnum && !$("input[name='cnum']").hasClass('lgc_haserror')) {
                $('.loader').css('visibility', 'visible');
                if ( uid > 0 ){
                    $.post(
                        window.location.origin+window.location.pathname+'/update',
                        {uid:uid, cnum:cnum, fio:fio, phone:phone, birth: birth, sex: sex, ctype: ctype, csize: csize, fsize: fsize, spoint: sell_point, coid: coid },
                        function (uid) {
                            if ( parseInt(uid) > 0 ) {
                                $("input[name='uid']").val(uid);
                            }
                        }
                    ).always(function() {
                        $('.loader').css('visibility', 'hidden');
                        location.reload();
                    }).fail(function() {
                        window.location.href = 'search/error';
                    });
                } else {
                    $.post(
                        window.location.href+'/create',
                        { cnum:cnum, bb:bblnc, nc:(nCard === null ? 1: nCard), fio:fio, phone:phone, birth: birth, sex: sex, ctype: ctype, csize: csize, fsize: fsize, spoint: sell_point, coid: coid },
                        function (uid) {
                            if ( parseInt(uid) > 0 ) {
                                $("input[name='uid']").val(uid);
                                $("button[name='newusersave']").text('Обновить');
                                $("button[name='subbonus']").prop('disabled', false);
                                $("button[name='addbonus']").prop('disabled', false);
                                $("button[name='credit']").prop('disabled', false);
                            }
                        }
                    ).always(function() {
                        $('.loader').css('visibility', 'hidden');
                        location.reload();
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
        сtransactions : function () {
            var uid   = parseInt($("input[name='uid']").val());
            window.location.href = window.location.origin+'/ctransactions?u='+uid;
        },
        sell : function () {
            var uid   = parseInt($("input[name='uid']").val());
            window.location.href = window.location.origin+'/ctransactions/sell?u='+uid;
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
    var item_per_page = 10;
    var requested_users = null;

    return {
        userselected:function(uid){
            window.location.href = window.location.origin+'/client-card?u='+uid;
        },

        newsearch: function () {
            if (timerId) {clearTimeout(timerId);}
            $('.table-hover > tbody:last-child').fadeOut()
            $('.table-hover > tbody:last-child').empty();
            $('.lgc_search_pager').fadeOut();
            $('.lgc_search_pager').empty();
            if ($("input[name='spattern']").val().length > 0 ) {
                timerId = setTimeout(function () {
                    $('.loader').css('visibility', 'visible');
                    requested_users = null;
                    $.post(
                        window.location.origin + '/search/newsearch',
                        {s: $("input[name='spattern']").val()},
                        function (data) {
                            if (data.length > 0) {
                                requested_users = data;
                                search.setPage(null, 1);
                                var page_cnt = data.length/item_per_page;
                                page_cnt = page_cnt - Math.floor(page_cnt) > 0 ? Math.floor(page_cnt) + 1 : Math.floor(page_cnt);
                                if (page_cnt > 1) {
                                    var i = 1;
                                    var page_item = '';
                                    while (i <= page_cnt){
                                        if (i == 1){
                                            page_item = page_item+'<label class="lgc_page_active" onclick="search.setPage(this,'+i+')">'+i+'</label>';
                                        } else {
                                            page_item = page_item+'<label onclick="search.setPage(this,'+i+')">'+i+'</label>';
                                        }
                                        i++;
                                    }
                                    $('.lgc_search_pager').append(page_item);
                                    $('.lgc_search_pager').fadeIn();
                                }
                            } else {
                                $('.table-hover > tbody:last-child').append('<tr><td colspan="5" style="text-align: center;">Ничего не найдено</td></tr>');
                            }
                            $('.table-hover > tbody:last-child').fadeIn();
                        }
                    ).always(function() {
                        $('.loader').css('visibility', 'hidden');
                    }).fail(function() {
                        window.location.href = 'search/error';
                    });
                }, 1000);
            } else {
                setTimeout(function () {
                    $('.table-hover > tbody:last-child').empty();
                    $('.table-hover > tbody:last-child').append('<tr><td colspan="5" style="text-align: center;">Для поиска введите ФИО, номер телефона либо номер карты</td></tr>');
                    $('.table-hover > tbody:last-child').fadeIn();
                }, 500);
            }
        },
        setPage: function(obj, pid) {
            $('.table-hover > tbody:last-child').fadeOut('slow',
                function () {
                    $('.table-hover > tbody:last-child').empty();
                    $('.lgc_page_active').removeClass('lgc_page_active');
                    $(obj).addClass('lgc_page_active');
                    $(requested_users).each(function (item, obj) {
                        if (item >= (pid-1)*item_per_page && item < (pid-1)*item_per_page+item_per_page) {
                            $('.table-hover > tbody:last-child').append('<tr onclick="search.userselected(' + obj.uid + ')"><td scope="row">' + obj.fio + '</td><td>' + obj.phone + '</td><td>' + obj.cnum + '</td><td>' + obj.bsumm + '</td><td>' + obj.cbalance + '</td>><td class="lgc_search_control"><i class="fa fa-plus-square" title="Зачисление бонусов" style="color: green; font-size: 25px;" aria-hidden="true" onclick="event.stopPropagation();search.goadd(' + obj.uid + ')"/>&nbsp;<i class="fa fa-minus-square" title="Списание бонусов" style="color: red; font-size: 25px;" aria-hidden="true" onclick="event.stopPropagation();search.gosub(' + obj.uid + ')"/></td></tr>');
                        }
                    });
                    $('.table-hover > tbody:last-child').fadeIn();
                }
            );
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
                            $('button[name=addbonus]').prop('disabled', true);
                            setTimeout(function () {
                                window.location.href = window.location.origin+'/client-card?u='+uid;
                            }, 1000);
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
                            $('button[name=addbonus]').prop('disabled', true);
                            setTimeout(function () {
                                window.location.href = window.location.origin+'/client-card?u='+uid;
                            }, 1000);
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
            bonus.payCalcSub();
        },
        addcalc: function (){
            var summ   = parseInt($("input[name='summ']").val());
            var bprcnt = parseInt($("select[name='bprcnt']").val());
            var bsumm  = Math.floor(summ * bprcnt/100);

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
        payCalcSub: function () {
            var summ  = parseInt($("input[name='summ']").val());
            var bsumm = parseInt($("input[name='bsumm']").val());
            $("input[name='pay_summ']").val(summ-bsumm);
        }
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
        },
        show_confirm_dialog: function (tid) {
            $('#confirm_delete').modal('show');
            edit_tid = tid;
        },
        delete: function () {
            $.post(
                window.location.origin+window.location.pathname+'/deltransaction',
                {t:edit_tid},
                function () {
                    transaction.refresh();
                    edit_tid = null;
                }
            ).fail(function() {
                edit_tid = null;
                window.location.href = 'search/error';
            });
        }
    };
}();

var sending = function() {
    var edit_tid = null;

    return {
        refresh: function () {
            $.pjax({url: window.location.origin+window.location.pathname, container:"#sending_list", timeout:2e3});
        },
        save : function () {
            var sdate   = $("input[name='SendingForm[sdate]']").val();
            var sname   = $("input[name='sname']").val();
            var msg     = $("textarea[name='message']").val();
            var spoints = $('#sell_point').val().toString();

            $("input[name='sname']").removeClass('lgc_haserror');
            $("textarea[name='message']").removeClass('lgc_haserror');

            if (sname.length == 0){
                $("input[name='sname']").addClass('lgc_haserror');
                return;
            }

            if (msg.length == 0){
                $("textarea[name='message']").addClass('lgc_haserror');
                return;
            }

            if (edit_tid) {
                $.post(
                    window.location.origin + window.location.pathname + '/savesend',
                    {s: edit_tid, d: sdate, n: sname, m: msg, spoint:spoints},
                    function (data) {
                        $('#editSendItem').modal('hide');
                        sending.refresh();
                    }
                ).fail(function () {
                    window.location.href = 'search/error';
                });
            } else {
                $.post(
                    window.location.origin + window.location.pathname + '/newsend',
                    {d: sdate, n: sname, m: msg, spoint: spoints},
                    function (data) {
                        $('#editSendItem').modal('hide');
                        sending.refresh();
                    }
                ).fail(function () {
                    window.location.href = 'search/error';
                });
            }
        },
        edit: function (slid) {
            edit_tid = slid;
            $("input[name='sname']").removeClass('lgc_haserror');
            $("textarea[name='message']").removeClass('lgc_haserror');

            $('#editSendItem').on('hidden.bs.modal', function (e) {
                sending.refresh();
                edit_tid = null;
            });

            if (slid) {
                $.post(
                    window.location.origin + window.location.pathname + '/getsend',
                    {s: slid},
                    function (data) {
                        $("input[name='SendingForm[sdate]']").val(data.sdate);
                        $("input[name='sname']").val(data.sname);
                        $("textarea[name='message']").val(data.message);
                        $('#editSendItem').modal('show');
                        $('#sell_point').multiselect('deselectAll', false);
                        $('#sell_point').multiselect('updateButtonText');
                        if (data.spoints) {
                            $('#sell_point').multiselect('select', data.spoints.split(','));
                        }

                        sending.smslengthcounter($("textarea[name='message']"));
                    }
                ).fail(function () {
                    window.location.href = 'search/error';
                });
            } else {
                sending.smslengthcounter($("textarea[name='message']"));
                $('#editSendItem').modal('show');
            }
        },
        show_confirm_dialog: function (slid) {
            $('#confirm_delete').modal('show');
            edit_tid = slid;
        },
        delete: function () {
            $.post(
                window.location.origin+window.location.pathname+'/delsend',
                {s:edit_tid},
                function () {
                    sending.refresh();
                    edit_tid = null;
                }
            ).fail(function() {
                edit_tid = null;
                window.location.href = 'search/error';
            });
        },

        show_confirm_dialog_restart: function (slid) {
            $('#confirm_restart').modal('show');
            edit_tid = slid;
        },
        restart: function (){
            $.post(
                window.location.origin+window.location.pathname+'/restart',
                {s:edit_tid},
                function () {
                    sending.refresh();
                    edit_tid = null;
                }
            ).fail(function() {
                edit_tid = null;
                window.location.href = 'search/error';
            });
        },
        create: function () {
            var now = new Date();
            var sdate = now.getDate()+'.'+(now.getMonth()+1)+'.'+now.getFullYear();
            $("input[name='SendingForm[sdate]']").val(sdate);
            $("input[name='sname']").val('');
            $("textarea[name='message']").val('');

            sending.edit(null);
        },
        smslengthcounter: function (obj) {
            var t = $(obj).val();
            var sms_length =  parseInt(t.length/70)+(t.length?1:0);
            $('#text_count').text(sms_length+'/'+t.length);
        },
    };
}();

var company = function () {
    return {
        refresh: function () {
            $.pjax({url: window.location.origin+window.location.pathname, container:"#company_list", timeout:2e3});
        },

        save: function () {

            $("input[name='coname']").removeClass('lgc_haserror');

            if ($("input[name='coname']").val().length == 0) {
                $("input[name='coname']").addClass('lgc_haserror');
                return;
            }

            $.post(
                window.location.origin+window.location.pathname+'/save',
                {
                    id: $("input[name='coid']").val(),
                    n:  $("input[name='coname']").val(),
                    m:  $("input[name='manager']").val(),
                    c:  $("textarea[name='contacts']").val(),
                    d:  $("input[name='disabled']:checked").val()?1:0,
                    p:  $("select[name='paytype']").val(),
                },
                function (data) {
                    if (parseInt(data) > 0){
                        company.refresh();
                    }
                }
            ).fail(function() {
                window.location.href = 'search/error';
            });
        },

        setActiveItem : function (o) {
            $("input[name='coid']").val($(o).data('coid'));
            $("input[name='coname']").val($(o).data('name'));
            $("input[name='manager']").val($(o).data('manager'));
            $("textarea[name='contacts']").val($(o).data('contacts'));
            $("input[name='disabled']").prop('checked', $(o).data('disabled')==='Y');
            $("select[name='paytype']").val($(o).data('ptype'));
        }
    };
}();

var ctransaction = function () {
    var __perodList = null;
    var __cPaymentInProgress = false;

    function __creditCalculator(sum, payData, months) {
        var list = [];
        var s    = 0;
        var s1   = sum;
        var dt = payData.split('.');
        dt     = new Date(dt[2], dt[1]-1, dt[0]);
        var payPerMonth = Math.round(sum/(months*100))*100;

        for (var i = 1; i<=months; i++) {
            var item = {};
            item.i = i;
            if (s1 - payPerMonth >= payPerMonth){
                item.sum = payPerMonth;
            } else {
                if (s1 - payPerMonth > 0) {
                    item.sum = payPerMonth;
                } else {
                    item.sum = s1;
                }
            }
            s1 = s1 - item.sum;
            s = s + item.sum;
            item.residue = sum-s;

            if (i === months && item.residue !== 0) {
                item.sum = item.sum + item.residue;
                item.residue = 0;
            }

            dt = new Date(dt.setMonth(dt.getMonth()+1));
            item.date = dt.toLocaleDateString();

            list.push(item);
        }

        return list;
    }

    return {
        creditListItems: function(){
            var paySumm    = parseInt($("input[name='summ']").inputmask('unmaskedvalue'));
            var creditSumm = parseInt($("input[name='cblnc']").val());
            var firstPay   = parseInt($("input[name='firstPay']").val());

            firstPay = isNaN(firstPay) ? 0 : firstPay;

            $("#paymentPeriod").find('tbody:first').empty();

            var payList = __creditCalculator(
                paySumm + creditSumm - firstPay,
                $("#ctransactionsform-sdate").val(),
                parseInt($("select[name='months']").val())
            );

            if (payList.length > 0) {
                if (firstPay !== 0) {
                    var dt = new Date();
                    payList.unshift({
                        i: 0,
                        date: dt.toLocaleDateString(),
                        sum: firstPay,
                        residue: paySumm + creditSumm - firstPay
                    });
                }

                $(payList).each(function (i, o) {
                    var prepayed = o.i === 0 ? 'lgc_credit_prepaid_item' : '';
                    $("#paymentPeriod").find('tbody:first').append('<tr class="' + prepayed + '"><th scope="row">' + o.i + '</th><td>' + o.date + '</td><td>' + o.sum + '</td><td>' + o.residue + '</td></tr>');
                }).promise().done(function () {
                    $('#creditCalculateModal').modal('show');
                });

                __perodList = payList;
            }
        },
        creditCalculatorShow: function () {
            var paySumm    = parseInt($("input[name='summ']").inputmask('unmaskedvalue'));
            var creditSumm = parseInt($("input[name='cblnc']").val());
            var firstPay   = parseInt($("input[name='firstPay']").val());

            $("input[name='summ']").removeClass('lgc_haserror');
            $("input[name='firstPay']").removeClass('lgc_haserror');

            firstPay = isNaN(firstPay) ? 0 : firstPay;

            if ( paySumm-firstPay > 0) {
                $("input[name='creditSumm']").val(paySumm + creditSumm);
                ctransaction.creditListItems();
            } else {
                $("input[name='summ']").addClass('lgc_haserror');
                if ( paySumm === firstPay &&  firstPay !== 0 || firstPay > paySumm) {
                    $("input[name='firstPay']").addClass('lgc_haserror');
                }
            }
        },
        saveCreditPeriods: function () {
            if (null !== __perodList) {
                $.post(
                    window.location.origin+window.location.pathname+'/saveperiods',
                    {
                        u: $("input[name='uid']").val(),
                        p: JSON.stringify(__perodList),
                    },
                    function (data) {
                        if (parseInt(data) === 1) {
                            $.post(
                                window.location.origin+window.location.pathname+'/savecorder',
                                {
                                    u: $("input[name='uid']").val(),
                                    s: parseInt($("input[name='summ']").inputmask('unmaskedvalue')),
                                    d: $("input[name='descr']").val()
                                },
                                function (data) {
                                    if (parseInt(data) > 0) {
                                        $('#creditCalculateModal').modal('hide');
                                        location.reload();
                                    }
                                }
                            ).fail(function() {
                                window.location.href = 'search/error';
                            });
                        }
                    }
                ).fail(function() {
                    window.location.href = 'search/error';
                });
            }
        },
        showAddPayModal: function () {
            $.post(
                window.location.origin+window.location.pathname+'/getperiods',
                {
                    u: $("input[name='uid']").val(),
                },
                function (data) {
                    if (data.length > 0) {
                        var tableContent = $('#paymentPeriodForPay').find('tbody:first');
                        $(tableContent).empty();
                        var currentDate = new Date();
                        var totalPostDue = 0;

                        $(data).each(function (i, o) {
                            var payDate = o.pay_data.split('.');
                            payDate = new Date(payDate[2], payDate[1] - 1, payDate[0]);
                            var payed = '';
                            var pastdue = '';
                            var payControl = '';
                            var payStatus  = '';

                            if (o.payed === 'Y') {
                                payed = 'lgc_credit_prepaid_item';
                                payStatus = 'Оплачен';
                                payControl = '<i class="fa fa-check" aria-hidden="true"></i>';
                            } else {
                                totalPostDue  += parseFloat(o.pay_sum);
                                payControl = '<input type="checkbox" data-pid="'+o.pid+'" data-sum="'+o.pay_sum+'" onchange="ctransaction.calculateSubTotal(this)"/>';
                                payStatus = 'Предстоящий';
                                if (currentDate > payDate) {
                                    pastdue = '<i class="fa fa-exclamation-triangle lgc_hint_warning" aria-hidden="true"></i>';
                                    payStatus = 'Просрочен';
                                }
                            }

                            $(tableContent).append('<tr class="'+payed+'"><th>'+o.pitem+'</th><td>'+o.pay_data+pastdue+'</td><td name="payItem" data-sum="'+o.pay_sum+'">'+o.pay_sum+'</td><td name="payResidue" data-residue="'+o.pay_residue+'">'+o.pay_residue+'</td><td>'+payStatus+'</td><td>'+payControl+'</td></tr>');
                            $('#totalPostDue').text(totalPostDue);
                        }).promise().done(function () {
                            $("input[name=totalForPay]").val(0)
                            $('#addpayModal').modal('show');
                        });
                    }
                }
            ).fail(function() {
                window.location.href = 'search/error';
            });
        },

        calculateSubTotal: function (o) {
            var curVal = parseFloat($("input[name=totalForPay]").inputmask('unmaskedvalue'));
            var sumVal = parseFloat($(o).data('sum'));

            curVal = isNaN(curVal) ? 0 : curVal;
            sumVal = $(o).prop("checked") ? sumVal : -sumVal;

            $("input[name=totalForPay]").val(curVal + sumVal);

            ctransaction.calculateCustomPaySum();
        },

        calculateCustomPaySum: function () {
            var tableContent = $('#paymentPeriodForPay').find('tbody:first').find('tr');
            var sumForPay = parseFloat($("input[name='totalForPay']").inputmask('unmaskedvalue'));

            if (!isNaN(sumForPay)) {
                var flg_restore = false;

                $(tableContent).each(function (i, o) {
                    $("button[name=bnt_addpay]").prop('disabled', false);
                    var el_sum = $(o).find('td').eq(1);
                    var el_res = $(o).find('td').eq(2);
                    var el_chk = $(o).find('input:first');

                    var periodPayItem = parseFloat($(el_sum).data('sum'));
                    var periodpayResidue  = parseFloat($(el_res).data('residue'));

                    if (!flg_restore && !$(o).hasClass('lgc_credit_prepaid_item')) {
                        if (periodPayItem > sumForPay) {
                            el_sum.text(periodPayItem - sumForPay);
                            el_res.text(periodpayResidue === 0 ? 0: periodpayResidue - sumForPay);
                            el_chk.prop('checked', false);
                            el_chk.data('sum', periodPayItem - sumForPay);

                            flg_restore = true;
                        } else {
                            sumForPay = sumForPay - periodPayItem;

                            el_sum.text($(el_sum).data('sum'));
                            if (sumForPay > periodpayResidue && periodpayResidue === 0) {
                                el_res.text('+'+Math.abs(periodpayResidue - sumForPay));
                                $("button[name=bnt_addpay]").prop('disabled', true);
                            } else {
                                el_res.text($(el_res).data('residue'));
                            }
                            el_chk.prop('checked', true);
                            el_chk.data('sum', $(el_sum).data('sum'));
                        }
                    } else {
                        el_sum.text($(el_sum).data('sum'));
                        el_res.text($(el_res).data('residue'));
                        el_chk.data('sum', $(el_sum).data('sum'));

                        el_chk.prop('checked', false);

                    }
                });
            } else {
                $(tableContent).each(function (i, o) {
                    var el_sum = $(o).find('td').eq(1);
                    var el_res = $(o).find('td').eq(2);
                    var el_chk = $(o).find('input:first');

                    el_sum.text($(el_sum).data('sum'));
                    el_res.text($(el_res).data('residue'));
                    el_chk.prop('checked', false);
                    el_chk.data('sum', $(el_sum).data('sum'));
                });
            }
        },

        AddPayments: function () {
            var tableContent = $('#paymentPeriodForPay').find('tbody:first');
            var selected_pays = [];

            if ( !__cPaymentInProgress ) {
                __cPaymentInProgress = true;

                $(tableContent).find('tr').each(function (i, o) {
                    var el_sum = $(o).find('td').eq(1);
                    var el_chk = $(o).find('input:first');
                    var el_res = $(o).find('td').eq(2);

                    if (el_chk.prop('checked')) {
                        selected_pays.push({pid: $(el_chk).data('pid'), sum: 0, residue: 0});
                    }

                    if (parseFloat($(el_sum).data('sum')) !== parseFloat(el_sum.text())) {
                        selected_pays.push({
                            pid: $(el_chk).data('pid'),
                            sum: parseFloat($(el_sum).text()),
                            residue: parseFloat($(el_res).text())
                        });
                    }
                }).promise().done(function () {
                    $.post(
                        window.location.origin + window.location.pathname + '/addpayments',
                        {
                            p: JSON.stringify(selected_pays)
                        },
                        function (data) {
                            if (parseInt(data) === 1) {
                                console.log('Credit payment add successful');
                            }
                        }
                    ).fail(function () {
                        window.location.href = 'search/error';
                    }).always(function () {
                        __cPaymentInProgress = false;
                        $('#addpayModal').modal('hide');
                        location.reload();
                    });
                });
            }
        },

        showPaysModal: function () {
            $.post(
                window.location.origin+window.location.pathname+'/getpayments',
                {
                    u: $("input[name='uid']").val(),
                    s: $('#ctransactionsform-pays_sdate').val(),
                    e: $('#ctransactionsform-pays_edate').val(),
                },
                function (data) {
                    if (data) {
                        var tableContent = $('#paymentsList').find('tbody:first');
                        $(tableContent).empty();

                        $(data).each(function (i, o) {
                            $(tableContent).append('<tr><th>'+(i+1)+'</th><td>'+o.pdate+'</td><td>'+o.psum+'</td><td>'+o.pdesc+'</td></tr>');
                        }).promise().done(function () {
                            if (! ($("#paysModal").data('bs.modal') || {}).isShown){
                                $('#paysModal').modal('show');
                            }
                        });
                    }
                }
            ).fail(function() {
                window.location.href = 'search/error';
            });
        },
    };
}();

var sell = function () {
    return {
        calcsum : function () {
            var psell = $("select[name='sprcnt']").val();
            var sum   = parseFloat($("input[name='summ']").inputmask('unmaskedvalue'));
            if (sum > 0) {
                $("input[name='sellsum']").val(Math.round(sum * (1 - psell / 100)));
            } else {
                $("input[name='sellsum']").val(0);
            }

        },
        saveOrder: function () {
            $("input[name='summ']").removeClass('lgc_haserror');
            var url = window.location.origin+window.location.pathname;
            url = url.replace('/sell', '')+'/saveporder'

            if (isNaN(parseFloat($("input[name='summ']").val()))) {
                $("input[name='summ']").addClass('lgc_haserror');
                return;
            }

            $.post(
                url,
                {
                    u: $("input[name='uid']").val(),
                    s: parseFloat($("input[name='sellsum']").val()),
                    d: $("input[name='descr']").val()
                },
                function (data) {
                    if (data) {
                        location.reload();
                    }
                }
            ).fail(function() {
                window.location.href = 'search/error';
            });
        },
    };
}();

var check = function () {
    return {
        fsspCheckShow: function () {
            var body = $("#fsspcheckModal").find(".modal-body:first");

            $(body).data('status', 'params');
            $(body).find("[name='fssp_result']:first").hide();
            $(body).find("[name='fssp_captcha']:first").hide();
            $(body).find("[name='fssp_params']:first").show();
            $("[name='fssp_bnt_refresh']").hide();
            $("[name='fssp_bnt_next']").text('Далее');

            $('#fsspcheckModal').modal('show');
        },

        fsspReloadCaptcha:function(){
            $("[name='fssp_loader']").addClass('loader');
            $("[name='fssp_img_captcha']").attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAP7//wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');
            $("[name='fssp_bnt_refresh']").hide();
            $("[name='fssp_lbl_status']").html('');
            $("[name='fssp_str_captcha']").val('')

            $.post(window.location.origin +  '/check/fsspcaptcha', {},
                function (r) {
                    if(parseInt(r.error) == 200){
                        $("[name='fssp_img_captcha']").attr('src', r.captcha);
                        $("[name='fssp_img_captcha']").data('sid', r.cookies);
                    }
                }).always(function () {
                $("[name='fssp_loader']").removeClass('loader');
                $("[name='fssp_bnt_refresh']").show();
            });
        },
        fsspNextStep: function() {
            var body = $("#fsspcheckModal").find(".modal-body:first");


            if ($(body).data('status') == 'params') {
                var vrf = true;
                $(body).find("[name='fssp_params']:first");

                $("[name='fssp_param_fn']").removeClass('lgc_haserror');
                $("[name='fssp_param_sn']").removeClass('lgc_haserror');
//                $("[name='fssp_param_mn']").removeClass('lgc_haserror');
                $("[name='fssp_param_bd']").removeClass('lgc_haserror');

                if (!$("[name='fssp_param_fn']").val()){
                    $("[name='fssp_param_fn']").addClass('lgc_haserror');
                    vrf = false;
                }

                if (!$("[name='fssp_param_sn']").val()){
                    $("[name='fssp_param_sn']").addClass('lgc_haserror');
                    vrf = false;
                }
                /*
                                if (!$("[name='fssp_param_mn']").val()){
                                    $("[name='fssp_param_mn']").addClass('lgc_haserror');
                                    vrf = false;
                                }
                */
                if (!$("[name='fssp_param_bd']").val()){
                    $("[name='fssp_param_bd']").addClass('lgc_haserror');
                    vrf = false;
                }

                if (vrf) {
                    $("[name='fssp_str_captcha']").removeClass('lgc_haserror');
                    $(body).find("[name='fssp_params']:first").hide();
                    $(body).find("[name='fssp_captcha']:first").show();
                    $(body).data('status', 'captcha');

                    ctransaction.fsspReloadCaptcha();
                }
            } else if ($(body).data('status') == 'captcha') {
                $("[name='fssp_str_captcha']").removeClass('lgc_haserror');
                $("[name='fssp_loader']").addClass('loader');

                if( !$("[name='fssp_str_captcha']").val() ){
                    $("[name='fssp_str_captcha']").addClass('lgc_haserror');
                } else {
                    $.post(window.location.origin + '/check/fsspresult', {
                            captcha: $("[name='fssp_str_captcha']").val(),
                            sid:     $("[name='fssp_img_captcha']").data('sid'),
                            fn:      $("[name='fssp_param_sn']").val(),
                            sn:      $("[name='fssp_param_fn']").val(),
                            mn:      $("[name='fssp_param_mn']").val(),
                            bd:      $("[name='fssp_param_bd']").val(),
                        },
                        function (r) {
                            if(parseInt(r.error) == 200) {
                                if (parseInt(r.data) > 0) {
                                    $("[name='fssp_result_text']").html('Найдена общая сумма задолженности: <b>' + r.data + '</b>');
                                } else {
                                    $("[name='fssp_result_text']").text('Задолженностей по базе ФССП не найдено.');
                                }

                                $(body).find("[name='fssp_captcha']:first").hide();
                                $(body).find("[name='fssp_result']:first").show();
                                $(body).data('status', 'result');
                                $("[name='fssp_bnt_refresh']").hide();
                                $("[name='fssp_bnt_next']").text('Готово');
                            } else if(parseInt(r.error) == 500) {
                                $("[name='fssp_lbl_status']").html(r.data);
                                $("[name='fssp_bnt_next']").text('Повторить');
                            } else {
                                $("[name='fssp_bnt_refresh']").show();
                                $("[name='fssp_lbl_status']").html(r.data);
                            }
                        }).always(function () {
                        $("[name='fssp_loader']").removeClass('loader');
                    });
                }
            } else if ($(body).data('status') == 'result') {
                $('#fsspcheckModal').modal('hide');
            }
        },
        PassportCheckShow: function () {

            $("[name='passport_img_captcha']").data('uid', '');
            $("[name='passport_img_captcha']").data('jid', '');
            $("[name='passport_img_captcha']").attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAP7//wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');
            $("[name='passport_param_ps']").val('');
            $("[name='passport_param_pn']").val('');
            $("[name='passport_param_pc']").val('');
            $("[name='passport_param_ps']").removeClass('lgc_haserror');
            $("[name='passport_param_pn']").removeClass('lgc_haserror');
            $("[name='passport_param_pc']").removeClass('lgc_haserror');
            $("[name='passport_result']").hide();
            $("[name='passport_params']").show();
            $("[name='passport_bnt_refresh']").hide();
            $("[name='passport_bnt_next']").text('Проверить');
            $("[name='passport_bnt_next']").data('status', 'params');
            $('#passportheckModal').modal('show');

            check.PassportReloadCaptcha();
        },
        PassportReloadCaptcha: function(){
            $("[name='passport_loader']").addClass('loader');
            $("[name='passport_img_captcha']").attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAP7//wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');
            $("[name='passport_bnt_refresh']").hide();
            $("[name='passport_lbl_status']").html('');
            $("[name='passport_param_pc']").val('')

            $.post(window.location.origin + '/check/passportcaptcha', {},
                function (r) {
                    if(parseInt(r.error) == 200){
                        $("[name='passport_img_captcha']").attr('src', r.captcha);
                        $("[name='passport_img_captcha']").data('uid', r.uid);
                        $("[name='passport_img_captcha']").data('jid', r.jid);
                    } else {
                        $("[name='passport_lbl_status']").html(r.data);
                    }
                }).always(function () {
                $("[name='passport_loader']").removeClass('loader');
                $("[name='passport_bnt_refresh']").show();
            });
        },
        PassportValidation: function () {
            var body = $("#passportheckModal").find(".modal-body:first");

            $("[name='passport_param_ps']").removeClass('lgc_haserror');
            $("[name='passport_param_pn']").removeClass('lgc_haserror');
            $("[name='passport_param_pc']").removeClass('lgc_haserror');


            if ($("[name='passport_bnt_next']").data('status') === 'params') {
                var flg = true;
                if (! $("[name='passport_param_ps']").val() ){
                    $("[name='passport_param_ps']").addClass('lgc_haserror');
                    flg = false;
                }
                if (! $("[name='passport_param_pn']").val() ){
                    $("[name='passport_param_pn']").addClass('lgc_haserror');
                    flg = false;
                }
                if (! $("[name='passport_param_pc']").val() ){
                    $("[name='passport_param_pc']").addClass('lgc_haserror');
                    flg = false;
                }

                if (flg) {
                    $("[name='passport_bnt_refresh']").hide();
                    $("[name='passport_loader']").addClass('loader');
                    $.post(window.location.origin + '/check/passportcheck', {
                            s: $("[name='passport_param_ps']").val(),
                            n: $("[name='passport_param_pn']").val(),
                            c: $("[name='passport_param_pc']").val(),
                            uid: $("[name='passport_img_captcha']").data('uid'),
                            jid: $("[name='passport_img_captcha']").data('jid'),
                        },
                        function (r) {
                            if (parseInt(r.error) == 200) {
                                $("[name='passport_result_text']:first").text(r.data);
                                $("[name='passport_params']:first").hide();
                                $("[name='passport_result']:first").show();
                                $("[name='passport_bnt_next']").text('Готово');
                                $("[name='passport_bnt_next']").data('status', 'result');
                            } else {
                                $("[name='passport_lbl_status']").html(r.data);
                                $("[name='passport_bnt_refresh']").show();
                            }
                        }).always(function () {
                        $("[name='passport_loader']").removeClass('loader');
                    });
                }
            } else {
                $('#passportheckModal').modal('hide');
            }

        }
    }
}();