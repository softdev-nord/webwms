;(function ($) {
    /**
     * @class EventEmitter
     * @constructor
     */
    function EventEmitter() {
        const webWms = this;

        /**
         * @private
         * @property _events
         * @type {Object}
         */
        webWms._events = {};
    }

    EventEmitter.prototype = {
        constructor: EventEmitter,
        name: 'EventEmitter',

        /**
         * @public
         * @chainable
         * @method on
         * @param {String} eventName
         * @param {Function} callback
         * @param {*} context
         * @returns {EventEmitter}
         */
        on: function (eventName, callback, context) {
            const webWms = this,
                events = webWms._events || (webWms._events = {}),
                event = events[eventName] || (events[eventName] = []);

            event.push({
                callback: callback,
                context: context || webWms
            });

            return webWms;
        },

        /**
         * @public
         * @chainable
         * @method once
         * @param {String} eventName
         * @param {Function} callback
         * @param {*} context
         * @returns {EventEmitter}
         */
        once: function (eventName, callback, context) {
            const webWms = this,
                once = function () {
                    webWms.off(eventName, once, context);
                    callback.apply(webWms, arguments);
                };

            return webWms.on(eventName, once, context);
        },

        /**
         * @public
         * @chainable
         * @method off
         * @param {String} eventName
         * @param {Function} callback
         * @param {*} context
         * @returns {EventEmitter}
         */
        off: function (eventName, callback, context) {
            var webWms = this,
                events = webWms._events || (webWms._events = {}),
                eventNames = eventName ? [eventName] : Object.keys(events),
                eventList,
                event,
                name,
                len,
                i, j;

            for (i = 0, len = eventNames.length; i < len; i++) {
                name = eventNames[i];
                eventList = events[name];

                /**
                 * Return instead of continue because only the one passed
                 * event name can be wrong / not available.
                 */
                if (!eventList) {
                    return webWms;
                }

                if (!callback && !context) {
                    eventList.length = 0;
                    delete events[name];
                    continue;
                }

                for (j = eventList.length - 1; j >= 0; j--) {
                    event = eventList[j];

                    // Check if the callback and the context (if passed) is the same
                    if ((callback && callback !== event.callback) || (context && context !== event.context)) {
                        continue;
                    }

                    eventList.splice(j, 1);
                }
            }

            return webWms;
        },

        /**
         * @public
         * @chainable
         * @method trigger
         * @param {String} eventName
         * @returns {EventEmitter}
         */
        trigger: function (eventName) {
            var webWms = this,
                events = webWms._events || (webWms._events = {}),
                eventList = events[eventName],
                event,
                args,
                a1, a2, a3,
                len, i;

            if (!eventList) {
                return webWms;
            }

            args = Array.prototype.slice.call(arguments, 1);
            len = eventList.length;
            i = -1;

            if (args.length <= 3) {
                a1 = args[0];
                a2 = args[1];
                a3 = args[2];
            }

            /**
             * Using switch to improve the performance of listener calls
             * .call() has a much greater performance than .apply() on
             * many parameters.
             */
            switch (args.length) {
                case 0:
                    while (++i < len) (event = eventList[i]).callback.call(event.context);
                    return webWms;
                case 1:
                    while (++i < len) (event = eventList[i]).callback.call(event.context, a1);
                    return webWms;
                case 2:
                    while (++i < len) (event = eventList[i]).callback.call(event.context, a1, a2);
                    return webWms;
                case 3:
                    while (++i < len) (event = eventList[i]).callback.call(event.context, a1, a2, a3);
                    return webWms;
                default:
                    while (++i < len) (event = eventList[i]).callback.apply(event.context, args);
                    return webWms;
            }
        },

        /**
         * @public
         * @method destroy
         */
        destroy: function () {
            this.off();
        }
    };

    window.PluginLoader = $.extend(Object.create(EventEmitter.prototype), {
        /**
         * @public
         * @chainable
         * @method addPlugin
         * @param {String} selector
         * @param {String} pluginName
         * @returns {PluginLoader}
         */
        addPlugin: function (selector, pluginName) {
            const webWms = this,
                $el = $(selector);

            if ($el.length > 0) {
                $('<script>').on('load',
                        function() {
                            console.log( 'Successfully loaded '+ pluginName +'' );
                        }
                    )
                    .prop( 'src', '/assets/js/webWmsCoreJs/jquery.'+ pluginName +'.js' )
                    .each(
                        function() {
                            // We used jQuery to construct and configure the Script Element,
                            // but we're appending it using a vanilla DOM method.
                            document.body.appendChild( this );
                        }
                    );
            }

            return webWms;
        },
    })
})(jQuery);
