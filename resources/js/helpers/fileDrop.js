import { wa as $ } from '../waDOM.js';

export const fileDrop = {
    init: function (settings) {
        fileDrop.config = {
            container: {},
            enter: function (e, elm) {
            },
            over: function (e, elm) {
            },
            leave: function (e, elm) {
            },
            drop: function (e, elm) {
            }
        };

        // Use WA DOM istead of jQuery
        $.extend(fileDrop.config, settings);
        return this;

    }, setup: function () {
        fileDrop.config.container.on('drop', function (e) {
            fileDrop.drop(e);
        });

        fileDrop.config.container.on('dragenter', function (e) {
            fileDrop.dragenter(e);
        });

        fileDrop.config.container.on('dragover', function (e) {
            fileDrop.dragover(e);
        });

        fileDrop.config.container.on('dragleave', function (e) {
            fileDrop.dragleave(e);
        });
        
    }, drop: function (e) {
        e.preventDefault();
        var dt = e.dataTransfer || (e.originalEvent && e.originalEvent.dataTransfer);
        fileDrop.config.container.removeClass("enter over");
        fileDrop.config.drop(e, dt, fileDrop.config.container);

    }, dragenter: function (e) {
        e.preventDefault();
        fileDrop.config.container.addClass("enter");
        fileDrop.config.enter(e, fileDrop.config.container);

    }, dragover: function (e) {
        e.preventDefault();
        fileDrop.config.container.addClass("over");
        fileDrop.config.over(e, fileDrop.config.container);

    }, dragleave: function (e) {
        e.preventDefault();
        fileDrop.config.container.removeClass("enter over");
        fileDrop.config.leave(e, fileDrop.config.container);
    }
};