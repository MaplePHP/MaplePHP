import { Responder } from '../../node_modules/frontresponder/src/Responder.js';
import { Stratox } from '../../node_modules/stratox/src/Stratox.js';
import { StratoxTemplate } from '../../node_modules/stratox/src/StratoxTemplate.js';
import { StratoxDom as $ } from '../../node_modules/stratoxdom/src/StratoxDom.js';
import { StratoxModal } from '../../node_modules/stratoxcomponents/src/StratoxModal.js';
import { StratoxForm } from '../../node_modules/stratoxcomponents/src/StratoxForm.js';
import { StratoxTable } from '../../node_modules/stratoxcomponents/src/StratoxTable.js';
//import { ingressComponent } from '../views/jviews/ingress.js';

Responder.init({
    lang: "en",
    template: {
        cache: false,
        directory: "../../../resources/views/jviews/",
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
        500: "500 Internal Server Error, try agin later",
        503: "503 Service Unavailable"
    },
    responder: {
        setup: function(config) {
            Stratox.setComponent("modal", StratoxModal);
            Stratox.setComponent("form", StratoxForm);
            Stratox.setComponent("table", StratoxTable);
            //Stratox.setComponent("ingress", ingressComponent);
        },
        ready: function (data) {
            // The documnet is ready
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
