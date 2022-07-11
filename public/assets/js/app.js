/*
(function ($) {
'use strict';
    $.fn.webWmsLoadJs = function (options) {
        const webWMS = this;
        const defaults = {};
        const jQueryFilesInclude = [
            "assets/js/jquery.article.js",
        ];

        const settings = $.extend({}, defaults, options);
        if (webWMS.length > 1) {
            webWMS.each(function () { $(webWMS).webWmsLoadJs(options) });
            return webWMS;
        }
        // private variables
        const privatevar1 = '';
        const privatevar2 = '';
        // private methods
        const myPrivateMethod = function () {
            // do something ...
        };
        // public methods
        webWMS.initialize = function () {
            webWMS.jQueryFilesInclude();
            return webWMS;
        };

        webWMS.jQueryFilesInclude = function () {
            $.each(jQueryFilesInclude, function (index, filename) {
                // house-keeping: if script is already exist do nothing
                if(document.getElementsByTagName('head')[0].innerHTML.toString().includes(filename + ".js")) {
                    alert("script is already exist in head tag!")
                } else {
                    // add the script
                    webWMS.loadScript("/" + filename);
                }
            });
        };

        webWMS.loadScript = function (filename) {
            const node = document.createElement('script'),
                  body = document.body;
            node.type = "text/javascript";
            node.src = filename;
            body.appendChild(node);
        }
        return webWMS.initialize();
    }
    $('#webWMS-Header').webWmsLoadJs();
})(jQuery);
*/
