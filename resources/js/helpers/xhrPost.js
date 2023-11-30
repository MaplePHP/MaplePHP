import { StratoxDom as $ } from '../stratox/StratoxDom.js';

export const xhrPost = {
    init: function (settings) {
        xhrPost.config = {
            bind: "form",
            submitBtn: ".xhr-post-btn",
            progress: "#progressbar",
            tokenClass: ".csrf-token",
            before: function (form) {
                return true;
            },
            complete: function (json, form, event) {
            },
            error: function (obj, status, event, ev) {
            }
        };

        $.extend(xhrPost.config, settings);

        if (window.XMLHttpRequest) {
            xhrPost.xhr = new XMLHttpRequest();
        } else {
            xhrPost.xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xhrPost.data = {
            target: {},
            formData: null,
            fields: {}
        }

        if (typeof xhrPost.config.progress === "string") {
            xhrPost.config.progress = $(xhrPost.config.progress);
        }
        return this;

    }, setup: function (files) {
        if (xhrPost.xhr) {
            $(xhrPost.config.bind).on("click", xhrPost.config.submitBtn, xhrPost.submit);
        }

    }, supported: function () {
        return (typeof window.FormData === "function");

    }, open: function (URL, e) {
        xhrPost.error(e);
        xhrPost.ready(e);

        if (typeof xhrPost.config.progress === "string") {
            xhrPost.xhr.upload.addEventListener("progress", xhrPost.progress, false);
        }

        xhrPost.xhr.open("POST", URL);
        xhrPost.xhr.setRequestHeader("Cache-Control", "no-cache");
        xhrPost.xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    }, buffer: function (URL, files) {
        for (var i = 0; i < files.length; i++) {
            xhrPost.open(URL);
            xhrPost.xhr.setRequestHeader("Content-Type", "application/octet-stream");
            xhrPost.xhr.setRequestHeader("Content-Length", files[i].size);
            xhrPost.xhr.setRequestHeader("X-File-Name", files[i].name.replace(/[^a-zA-Z0-9,-.]/g,''));
            xhrPost.xhr.setRequestHeader("X-File-Size", files[i].size);
            xhrPost.xhr.send(files[i]);
        }

        return xhrPost.xhr;
    }, files: function (url, files) {

        var before;
        xhrPost.data.formData = new FormData();

        before = xhrPost.config.before();
        if (before === false) {
            return false;
        }

        xhrPost.open(url);
        for (var i = 0; i < files.length; i++) {
            xhrPost.data.formData.append("upload[]", files[i]);
        }

        xhrPost.xhr.send(xhrPost.data.formData);
        
    }, formDataToObj: function (formData) {
        let dataObject = {};
        for (let [key, value] of formData.entries()) {
            dataObject[key] = value;
        }
        return dataObject;

    }, constructEventData: function (e) {
        xhrPost.data.formData = new FormData(e.target);
        xhrPost.data.target = e.target;
        xhrPost.data.fields = xhrPost.formDataToObj(xhrPost.data.formData);

    }, getField: function (name) {
        return xhrPost.data.fields[name];

    }, getData: function () {
        return xhrPost.data;

    }, submit: function (e, uri) {
        e.preventDefault();
        if (typeof window.FormData === "function") {
            uri = (typeof uri === "string") ? uri : e.target.action;
            
            xhrPost.constructEventData(e);
            let before = xhrPost.config.before(e.target);
            if (before === false) {
                return false;
            }
            xhrPost.open(uri, e);
            xhrPost.xhr.send(xhrPost.data.formData);
        }

    }, error: function (e) {

        xhrPost.xhr.onerror = function () {
            if (typeof xhrPost.config.progress === "string") {
                xhrPost.config.progress.removeClass("show");
            }
            xhrPost.config.error({ status: 2, message: this.statusText, obj: this }, xhrPost.xhr.status);
        };

    }, ready: function (e) {
        
        xhrPost.xhr.onreadystatechange = function () {
            // WHILE TRIGGER
            //xhrPost.config.progress.removeClass("show");
        }

        xhrPost.xhr.onload = function () {

            var status = parseInt(xhrPost.xhr.status);
            if (typeof xhrPost.config.progress === "string") {
                xhrPost.config.progress.removeClass("show");
            }

            try {
                xhrPost.json = JSON.parse(xhrPost.xhr.responseText);
                if (status === 200) {
                    if (xhrPost.xhr.readyState === 4) {
                        xhrPost.config.complete(xhrPost.json, xhrPost.data, e);
                    }
                } else {
                    if (status === 413) {
                        xhrPost.config.error({ status: 2, message: "File size exceeds the servers max memory size" }, status, e);
                    } else {
                        xhrPost.config.error(xhrPost.json, status, e);
                    }
                }
            } catch (ev) {
                xhrPost.config.error({ status: 2, message: ev.message }, status, e);
            }
        }

        return this;


    }, progress: function (evt) {
        if (evt.lengthComputable) {
            var loaded = Math.ceil(((evt.loaded / evt.total)*100));
            xhrPost.config.progress.addClass("show");
            xhrPost.config.progress.attr("aria-valuenow", loaded);
            xhrPost.config.progress.find(".progress").css({ width: loaded+"%" });
        }
    }

};