import { app } from './core.js';
import { Stratox } from '../../node_modules/stratox/src/Stratox.js';
import { StratoxDom as $ } from '../../node_modules/stratox/src/StratoxDom.js';
import { modalComponent } from '../views/jviews/modal.js';

app.init({
    lang: "sv",
    template: {
        cache: false,
        directory: "../../../resources/views/jviews/"
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
        ready: function (data) {
            Stratox.prepareView("modal", modalComponent);
        },
        update: function (data) {
            //console.log("update", data);
        }
    }
});

$(document).ready(app.setup);
