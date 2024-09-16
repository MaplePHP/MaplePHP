import { Responder } from 'frontresponder/src/Responder';
import { Stratox, StratoxTemplate } from 'stratox';
import { StratoxDom as $ } from '../../node_modules/stratoxdom/src/StratoxDom.js';
import { StratoxModal, StratoxForm, StratoxTable } from 'stratoxcomponents';
import { ingressComponent } from '../views/jviews/ingress.js';

Responder.init({
    lang: "en",
    template: {
        cache: false,
        handlers: {
            fields: StratoxTemplate, // Not required (se bellow)
            helper: function() {
                return $;
            }
        }
    },
    phrases: {
        0: "An unexpected error has occurred",
        204: "204 The document is empty",
        400: "400 The server understood the request but the content was invalid",
        401: "401 Unauthorized",
        403: "403 Forbidden",
        404: "404 The page could not be found",
        414: "414 Request-URI Too Long",
        500: "500 Internal Server Error, try again later",
        503: "503 Service Unavailable"
    },
    responder: {
        setup: function(config) {
            Stratox.setComponent("modal", StratoxModal);
            Stratox.setComponent("form", StratoxForm);
            Stratox.setComponent("table", StratoxTable);
            Stratox.setComponent("ingress", ingressComponent);
        },
        ready: function (data) {
            // The document is ready
            // Your code here
        },
        update: function (data) {
            console.log("Responder update: ", data);
            // There has been a responder update
            // Your code here
        }
    }
});

$(document).ready(Responder.setup);
