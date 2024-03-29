/*! TokenLite v1.1.5 | Copyright by Softnio. */
function trim_number(e) {
    if (e - Math.floor(e) != 0) {
        for (var t = e.split("."), n = t[0], a = "", o = t[1].split(""), i = !0, r = o.length - 1; r >= 0; r--) {
            var l = o[r];
            "0" == l ? 1 == i && (l = "") : i = !1, a = l + a
        }
        return "" == a ? $.number(n, decimals.max, ".", "") : n + "." + a
    }
    return $.number(e, 0, ".", "")
}

function token_pay(e) {
    return $(e).val() ? $(e).val() : base_currency
}

function token_alert(e, t, n = "") {
    t = void 0 === t ? "" : t;
    if ("icon" !== n)
        if ("token" !== n) {
            if ("amount" !== n) return "text" === n ? (e.find(".note-text-alert").html(t), void e.find(".note-text-alert").addClass("text-danger")) : void e.html(trim_number(t));
            e.find(".min-amount").text(t)
        } else e.find(".min-token").text(t);
    else e.find(".note-icon").html('<i class="fas fa-' + t + '"></i>')
}

function token_bonus(e, t = "total") {
    var n, a = e ? parseFloat(e) : 0,
        o = 0,
        i = t || "total",
        r = 0,
        l = base_bonus ? parseFloat(base_bonus) : 0,
        s = amount_bonus || {
            1: 0
        };
    for (n in s) r = a >= (n = parseInt(n)) ? parseFloat(s[n]) : r;
    var m = a * l / 100,
        d = a * r / 100;
    return o = m + d, "base" !== i && "amount" !== i || (o = "base" === i ? m : d), o = isNaN(o) || void 0 === o ? 0 : trim_number($.number(o, 0, ".", ""))
}

function token_calc(e) {
    var t, n, a, o = $(e),
        i = ".token-number",
        r = ".pay-amount",
        l = o.parents(".token-purchase"),
        s = l.find(".final-pay"),
        m = l.find(".pay-currency"),
        d = l.find(".tokens-bonuses"),
        u = l.find(".tokens-bonuses-sale"),
        c = l.find(".tokens-bonuses-amount"),
        _ = l.find(".tokens-total"),
        f = l.find(".pay-method:checked"),
        p = l.find(".token-note"),
        h = $(".payment-btn"),
        v = $("#data_amount"),
        g = $("#data_currency"),
        k = $(".modal-payment"),
        b = k.find(".final-pay"),
        y = k.find(".pay-currency"),
        w = k.find(".token-bonuses"),
        x = k.find(".token-total"),
        F = k.find("input#pay_currency"),
        C = k.find(".gateway-name"),
        N = k.find("input#token_amount"),
        S = isNaN(parseFloat(o.val())) ? 0 : parseFloat(o.val()),
        D = token_pay(f),
        j = token_price[D],
        O = 1,
        I = token_price.base,
        z = minimum_token * j,
        T = 0;
    o.is(i) && (O = S, I = trim_number($.number(S * j, decimals.max, ".", "")), $(r).val(parseFloat(I)), $(r + "-u").text(parseFloat(I))), o.is(r) && (I = S, O = trim_number($.number(S / j, decimals.min, ".", "")), $(i).val(parseFloat(O)), $(i + "-u").text(parseFloat(O))), 0 === O ? (token_alert(p, "info-circle", "icon"), h.addClass("disabled").removeAttr("data-amount")) : O >= minimum_token && O <= maximum_token ? (token_alert(p, "check-circle text-success", "icon"), token_alert(p, I * minimum_token, "amount"), token_alert(p, "", "text"), h.removeClass("disabled")) : O < minimum_token ? (token_alert(p, "times-circle text-danger", "icon"), h.addClass("disabled").removeAttr("data-amount")) : O > maximum_token && (token_alert(p, "Maximum you can purchase " + maximum_token + " token per contribution.", "text"), h.addClass("disabled").removeAttr("data-amount")), t = parseFloat(token_bonus(O, "base")), n = parseFloat(token_bonus(O, "amount")), a = parseFloat(token_bonus(O, "total")), T = parseFloat(O) + a, I = isNaN(I) ? 0 : I, T = isNaN(T) ? 0 : T;
    var A = trim_number($.number(T, decimals.min, ".", ""));
    token_alert(p, trim_number($.number(z, decimals.max, ".", "")), "amount"), m.text(D), d.text(a), u.text(t), c.text(n), s.text(trim_number($.number(I, decimals.max, ".", ""))), _.text(A), b.text(trim_number($.number(I, decimals.max, ".", ""))), y.text(D), w.text(a), x.text(A), F.val(D), N.val(O), "btc" != D && "ltc" != D && "eth" != D || C.text('"Coingate"'), "usd" != D && "eur" != D && "gbp" != D || C.text('"Paypal"');
    var L = amount_bonus || {
        1: 0
    };
    for (_t in L) _t = parseInt(_t), O >= _t ? $(".bonus-tire-" + L[_t]).addClass("active") : $(".bonus-tire-" + L[_t]).removeClass("active");
    v.val(O), g.val(D), address_btn(g.val(), minimum_token, maximum_token, O)
}

function address_btn(e, t, n, a) {
    "usd" == e.toLowerCase() || "gbp" == e.toLowerCase() || "eur" == e.toLowerCase() || (Number(a) >= Number(t) && Number(a) <= Number(n) ? $("a.payment-btn.offline_payment").removeClass("disabled") : $("a.offline_payment").addClass("disabled"))
}

function purchase_form_submit(e = $(".validate-form"), t = !0, n = "ti ti-info-alt") {
    e.validate({
        errorClass: "text-danger border-danger error",
        submitHandler: function(a) {
            $(a).ajaxSubmit({
                dataType: "json",
                success: function(o) {
                    if (btn_actived(e.find("button.save-disabled"), !1), o.trnx || show_toast(o.msg, o.message, n), "success" == o.msg || !0 === t && $(a).clearForm(), o.link && setTimeout(function() {
                        window.location.href = o.link
                    }, 2e3), o.modal) {
                        var i = e.parents(".modal"),
                            r = !0;
                        is_changed = !0, i.modal("hide").addClass("hold"), i.find(".modal-content").html(o.modal), init_inside_modal(), i.on("hidden.bs.modal", function() {
                            1 == r ? (i.modal("show"), r = !1) : i.modal("hide")
                        })
                    }
                },
                error: function(e, t, a) {
                    e.responseJSON.length > 0 ? cl(e.responseJSON.exception + "\n" + e.responseJSON.message) : cl(e), show_toast("warning", "Something is Wrong!\n(" + (null != a ? a : "API Issue") + ")", n), cl("Ajax Error!!")
                }
            })
        },
        invalidHandler: function(e, t) {}
    })
}! function(e) {
    "use strict";
    var t = e("#ajax-modal"),
        n = e("#nio-user-personal, #nio-user-settings, #nio-user-password");
    n.length > 0 && ajax_form_submit(n, !1);
    var a = e("form#user_wallet_update");
    a.length > 0 && ajax_form_submit(a, !1);
    var o = e("#activity_action").val();
    e(".activity-delete").length > 0 && e(document).on("click", ".activity-delete", function() {
        swal({
            title: "Are you sure?",
            text: "Once Delete, You will not get back this log in future!",
            icon: "warning",
            buttons: !0,
            dangerMode: !0
        }).then(t => {
            if (t) {
                var n = e(this).data("id");
                e.post(o, {
                    _token: csrf_token,
                    delete_activity: n
                }).done(t => {
                    cl(t), "success" == t.msg && ("all" == n ? e("#activity-log tr").fadeOut(1e3, function() {
                        e(this).remove(), e("#activity-log").hide()
                    }) : e(".activity-delete").parents("tr.activity-" + n).fadeOut(1e3, function() {
                        e(this).remove()
                    }))
                }).fail(function(e, t, n) {
                    show_toast("error", "Something is wrong!\n" + n), _log(e, t, n)
                })
            }
        })
    });
    var i = e(".document-type");
    i.length > 0 && i.on("click", function() {
        var t = e(this).data("title"),
            n = e(".doc-upload-d2"),
            a = void 0 !== e(this).data("change"),
            o = e(this).data("img");
        e(".doc-type-name").text(t), e("._image").attr("src", o), n.length > 0 && a ? n.removeClass("hide") : n.addClass("hide")
    });
    var r = e("form#kyc_submit");
    if (r.length > 0 && ajax_form_submit(r, !1), e(".upload-zone").length > 0) {
        Dropzone.autoDiscover = !1;
        var l = e("input#file_uploads").val(),
            s = e('meta[name="csrf-token"]').attr("content");
        if (e(".document_one").length > 0) {
            var m = new Dropzone(".document_one", {
                url: l,
                uploadMultiple: !1,
                maxFilesize: 5.1,
                maxFiles: 1,
                addRemoveLinks: !0,
                acceptedFiles: "image/jpeg,image/png,application/pdf",
                hiddenInputContainer: ".hiddenFiles",
                paramName: "kyc_file_upload",
                headers: {
                    "X-CSRF-TOKEN": s
                }
            });
            m.on("sending", function(e, t, n) {
                n.append("docType", "doc-one")
            }).on("success", function(t, n) {
                cl(n);
                var a = n.message;
                "error" == n.msg ? (alert(a), m.removeFile(t)) : e('input[name="document_one"]').val(n.file_name)
            }).on("removedfile", function(t) {
                var n = e('input[name="document_one"]').val();
                n.length > 0 && e.post(l, {
                    _token: csrf_token,
                    action: "delete",
                    file: n
                }).done(t => {
                    cl(t), e('input[name="document_one"]').val("")
                })
            })
        }
        if (e(".document_two").length > 0) {
            var d = new Dropzone(".document_two", {
                url: l,
                uploadMultiple: !1,
                maxFilesize: 5.1,
                maxFiles: 1,
                addRemoveLinks: !0,
                acceptedFiles: "image/jpeg,image/png,application/pdf",
                hiddenInputContainer: ".hiddenFiles",
                paramName: "kyc_file_upload",
                headers: {
                    "X-CSRF-TOKEN": s
                }
            });
            d.on("sending", function(e, t, n) {
                n.append("docType", "doc-two")
            }).on("success", function(t, n) {
                cl(n);
                var a = n.message;
                "error" == n.msg ? (alert(a), d.removeFile(t)) : e('input[name="document_two"]').val(n.file_name)
            }).on("removedfile", function(t) {
                var n = e('input[name="document_two"]').val();
                n.length > 0 && e.post(l, {
                    _token: csrf_token,
                    action: "delete",
                    file: n
                }).done(t => {
                    cl(t), e('input[name="document_two"]').val("")
                })
            })
        }
        if (e(".document_upload_hand").length > 0) {
            var u = new Dropzone(".document_upload_hand", {
                url: l,
                uploadMultiple: !1,
                maxFilesize: 5.1,
                maxFiles: 1,
                addRemoveLinks: !0,
                acceptedFiles: "image/jpeg,image/png,application/pdf",
                hiddenInputContainer: ".hiddenFiles",
                paramName: "kyc_file_upload",
                headers: {
                    "X-CSRF-TOKEN": s
                }
            });
            u.on("sending", function(e, t, n) {
                n.append("docType", "doc-hand")
            }).on("success", function(t, n) {
                cl(n);
                var a = n.message;
                "error" == n.msg ? (alert(a), u.removeFile(t)) : e('input[name="document_image_hand"]').val(n.file_name)
            }).on("removedfile", function(t) {
                var n = e('input[name="document_image_hand"]').val();
                n.length > 0 && e.post(l, {
                    _token: csrf_token,
                    action: "delete",
                    file: n
                }).done(t => {
                    cl(t), e('input[name="document_image_hand"]').val("")
                })
            })
        }
    }
    var c = e(".token-number"),
        _ = e(".pay-amount"),
        f = e(".pay-method");
    c.numericInput({
        allowFloat: !1
    }), _.numericInput({
        allowFloat: !0
    }), c.add(_).on("keyup", function() {
        token_calc(this)
    }), f.on("change", function() {
        token_calc(c)
    });
    var p = e("form#offline_payment");
    p.length > 0 && purchase_form_submit(p, !1);
    var h = !1;
    e(".modal-close").on("click", function(t) {
        t.preventDefault(), !0 === h ? confirm("Do you really cancel your order?") && (bs_modal_hide(e(this)), h = !1) : bs_modal_hide(e(this))
    });
    var v = e(".token-payment-btn"),
        g = e("#payment-modal"),
        k = e("#data_amount"),
        b = e("#data_currency");
    g = e("#payment-modal");
    v.on("click", function(t) {
        t.preventDefault();
        var n = e(this),
            a = n.data("type") ? n.data("type") : "offline",
            o = k.val(),
            i = b.val();
        o >= minimum_token && "" != i ? e.post(access_url, {
            _token: csrf_token,
            req_type: a,
            min_token: minimum_token,
            token_amount: o,
            currency: i
        }).done(t => {
            g.find(".modal-content").html(t.modal), init_inside_modal(), g.modal("show"), e("#offline_payment").length > 0 && purchase_form_submit(e("#offline_payment")), o = i = ""
        }).fail(function(e, t, n) {
            show_toast("error", "Something is wrong!\n" + n), _log(e, t, n)
        }) : (o = k.val(), i = b.val(), show_toast("warning", "Enter minimum " + minimum_token + " token and select currency!"))
    }), e("a.user-wallet").on("click", function(n) {
        n.preventDefault(), user_wallet_address.length > 5 && e.post(user_wallet_address, {
            _token: csrf_token
        }).done(e => {
            cl(e), t.html(e), init_inside_modal(), t.children(".modal").length > 0 && t.children(".modal").modal("show")
        }).fail(function(e, t, n) {
            show_toast("error", "Something is wrong!\n" + n), _log(e, t, n)
        })
    });
    e("a.select-kyc").on("click", function(n) {
        n.preventDefault();
        e("#modal-select-kyc").modal("show");
    });

    e("a.self-cert").on("click", function(n) {
        n.preventDefault();
        e("#modal-self-cert").modal("show");
    });
    e(document).on("click", "a.view-transaction", function(n) {
        n.preventDefault();
        var a = e(this).data("id");
        e.post(view_transaction_url, {
            tnx_id: a,
            _token: csrf_token
        }).done(e => {
            cl(e), t.html(e), t.children(".modal").length > 0 && t.children(".modal").modal("show")
        }).fail(function(e, t, n) {
            show_toast("error", "Something is wrong!\n" + n), _log(e, t, n)
        })
    });
    e(document).on("click", "a.user-modal-request", function(n) {
        n.preventDefault();
        var a = null,
            o = e(this).data("action"),
            i = e(this).data("type") ? e(this).data("type") : "";
        "send-token" == o && "undefined" != typeof user_token_send ? a = user_token_send : "withdraw-token" == o && "undefined" != typeof user_token_withdraw && (a = user_token_withdraw), null !== a && i ? e.post(a, {
            type: i
        }).done(function(e) {
            if (void 0 !== e.modal) t.html(e.modal), init_inside_modal(), t.children(".modal").length > 0 && t.children(".modal").modal("show");
            else if (e.message) {
                var n = e.icon ? e.icon : "ti ti-info-alt";
                show_toast(e.msg, e.message, n)
            }
        }).fail(function(e, t, n) {
            show_toast("error", "Something is wrong!\n" + n), _log(e, t, n)
        }) : show_toast("warning", "Unable process request!", "ti ti-info-alt")
    });
    e(document).on("click", "a.user_tnx_trash", function(t) {
        t.preventDefault();
        var n = e(this).data("tnx_id"),
            a = e(this).attr("href");
        confirm("Are you sure?") && e.post(a, {
            tnx_id: n,
            _token: csrf_token
        }).done(t => {
            cl(t), e("tr.tnx-item-" + n).fadeOut(400, function() {
                e(this).remove()
            }), cl(n), show_toast(t.msg, t.message, "ti ti-trash")
        }).fail(function(e, t, n) {
            show_toast("error", "Something is wrong!\n" + n), _log(e, t, n)
        })
    })
}(jQuery);
