import { StratoxDom as $ } from '../../stratoxdom/src/StratoxDom.js';
import { Stratox } from '../../stratox/src/Stratox.js';

export const Responder = {
    init: function (settings) {
        Responder.config = {
            lang: "sv",
            template: {
                cache: false,
                directory: "../../../resources/views/jviews/",
                handlers: {
                    //fields: StratoxTemplate, // Not required (se bellow)
                    helper: function() {
                        return $;
                    }
                }
            },
            navigation: {
                container: $("#header"),
                smartBtn: $("#wa-smart-btn"),
            },
            phrases: {
                0: "An unexpected error has occurred",
                204: "204 The document is empty",
                400: "400 The server understood the request but the content was invalid",
                401: "401 Unauthorized",
                403: "403 Forbidden",
                404: "404 The page could not be found",
                414: "414 Request-URI Too Long",
                500: "500 Internal Server Error, try agin later",
                503: "503 Service Unavailable"
            },
            responder: {
                isReady: false,
                ready: function (data) {
                },
                update: function (data) {
                }
            }
        };

        $.extend(Responder.config, settings);


        Responder.data = {
            views: {},
            form: {
                form: {},
                data: {}
            }
        };

        Stratox.setConfigs(Responder.config.template);
        return this;

    }, setup: function () {

        Responder.getResponder();

        $(".domer-get-btn").click(function (e) {
            e.preventDefault();
            Responder.resetConfigViews();
            let btn = $(this), url = btn.data("href"), sendConfig = btn.data("config");
            Responder.ajax({ url: url });
        });

        $(document).on("submit", function (e) {
            e.preventDefault();
            const btn = $(this), form = btn.closest("form");
            Responder.data.form.form = form;
            Responder.data.form.data = new FormData(form.get(0));

            Responder.resetConfigViews();
            Responder.ajax({ url: form.attr("action"), method: form.attr("method"), body: new URLSearchParams(Responder.data.form.data) });
        });

        const header = Responder.config.navigation.container;
        $(".wa-anchor-btn").click(function(e) {
            const btn = $(this);
            if(btn.hasClass("nav-item")) header.removeClass("nav-active");
        });

        Responder.config.navigation.smartBtn.click(function(e) {
            e.preventDefault();
            if(header.hasClass("nav-active")) {
                header.removeClass("nav-active");
            } else {
                header.addClass("nav-active");
            }
        });

        Responder.config.responder.ready(CONFIG);
        Responder.config.responder.isReady = true;
        if (typeof window._deferLoad === "function") {
            window._deferLoad(app);
        }


    /**
     * Reset configs every response on "client" side
     * @return {void}
     */
    }, resetConfigViews: function () {
        CONFIG.values = {};
        CONFIG.views = {};
        
    /**
     * Used to trigger diffrent response
     * @return {void}
     */
    }, getResponder: function () {

        // Acccess view
        // Responder.getView('modal');

        // Access view container
        // Responder.getViewData('modal');

        // View Builder
        if (typeof CONFIG.views === "object") {
            $.each(CONFIG.views, function (k, o) {

                let id = ""+o.type+(o.element ?? "");
                const stratoxView = new Stratox((o.element ?? null));
                let item = stratoxView.view(o.type, o.data);

                if ($.isArray(o?.part?.data) && o.part.data.length > 0) {
                    $.each(o.part.data, function (i, a) {
                        $.each(a, function (k2, o2) {
                            stratoxView.view(k2, o2);
                        });
                    });
                }

                if (typeof o?.part?.fields === "object" && !$.isEmptyObject(o.part.fields)) {
                    item.setFields(o.part.fields);
                }
                if (stratoxView.hasView()) {
                    stratoxView.update();
                } else {
                    stratoxView.execute();
                }
                Responder.data.views[id] = stratoxView;
            });
        }      
        
        // Has yoken changed? Then set it in DOM
        if (typeof CONFIG.csrfToken === "string") {
            $(".inp-csrf-token").val(CONFIG.csrfToken);
        }

        // Status functionallity
        if (CONFIG.status) {
            switch (parseInt(CONFIG.status)) {
                case 1:
                    
                    Stratox.create("modal", {
                        headline: (CONFIG.headline ?? null),
                        content: CONFIG.message
                    });

                break;
                case 2:

                    Stratox.create("modal", {
                        type: "error",
                        headline: (CONFIG.headline ?? null),
                        content: CONFIG.message
                    });

                break;
                case 3:
                    if (CONFIG.error) {
                        if (CONFIG.error.form) {
                            $.each(CONFIG.error.form, function (name, row) {
                                let holder = {},
                                obj = ((Responder?.data?.form?.form?.length > 0) ? Responder.data.form.form.find('[name="'+name+'"]') : $('[name="'+name+'"]')),
                                get = obj.get(0);

                                if (get !== undefined) {
                                    holder = obj.parent();
                                    holder.addClass("error");
                                    holder.find(".message").text(row.message);
                                } else {
                                    console.error("Could not find form field ("+name+") in DOM.");
                                }

                                obj.on("focus.removeErrorInpClass", function () {
                                    holder.removeClass("error");
                                    obj.off("focus.removeErrorInpClass");
                                });

                            });
                        }
                    }
                    
                break;
                case 4:
                    if (CONFIG.redirect) {
                        window.location = CONFIG.redirect.replace(/^\s+|\s+$/g, '');
                    } else {
                        location.reload();
                    }
                break;
                case 5:
                    var modType = "ok";
                    if (CONFIG.type === "confirm") {
                        modType = CONFIG.type;
                    }

                    Stratox.create("modal", {
                        type: modType,
                        headline: (CONFIG.headline ?? null),
                        content: CONFIG.message

                    }).container().set("confirm", function () {
                        if (CONFIG.redirect) {
                            window.location = CONFIG.redirect.replace(/^\s+|\s+$/g, '');
                        } else {
                            location.reload();
                        }

                    });

                break;
                default:
                    //modal.template("message").show(CONFIG.message);
                break;
            }
        }

        if(Responder.getView('modal') && parseInt(CONFIG?.closeModal) === 1) {
            CONFIG.closeModal = 0;
            Responder.getViewData('modal').get("modal-close");
        }

        if (Responder.config.responder.isReady) {
            Responder.config.responder.update(CONFIG);
        }

    }, getView: function (key, id) {
        let view, k = ""+key+(id ?? "");
        if ((view = Responder.data.views?.[k]) && (view instanceof Stratox)) {
            return view;
        }
        return false;
        
    }, getViewData: function (key, id) {
        const view = this.getView(key, id);
        if(view) {
            return view.container();
        }
        return false;

    /**
     * The apps ajax response
     * @param  {object} settings Config
     * @param  {callable} success  success callback
     * @param  {callable} fail     fail callback
     * @return {object}          instance of $.ajax
     */
    }, ajax: function (settings, success, fail) {
        let config = {
            dataType: "json",
            statusCode: Responder.config.phrases
        };

        $.extend(config, settings);
        return $.ajax(config).fail(function (data, status) {
            if (status && data.message) {
                Responder.message("error", data.message);
            } else {
                Responder.message("error", "An unexpected error has occurred. Try again later. Contacts us if the error persists.");
            }
            if (typeof fail === "function") {
                success.call(this, data);
            }

        }).done(function (json, status, data) {
            $.extend(CONFIG, json);
            Responder.getResponder();
            if (typeof success === "function") {
                success.call(this, json);
            }
        });

    }, message: function (type, message) {
        let inst = Stratox.create("modal", {
            type: (type === "error" ? type : "message"),
            content: message
        });
        return inst;
    }
};

if(typeof CONFIG !== "object") var CONFIG = {};