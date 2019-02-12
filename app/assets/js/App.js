/* eslint-disable */
/**
 * @class App  Base utilize class that privides helper functions main Applictaion
 */
const App = (function () {
    /** @type {object} breakpoints */
    let breakpoints = {
        sm: 544, // Small screen / phone
        md: 768, // Medium screen / tablet
        lg: 1024, // Large screen / desktop
        xl: 1200, // Extra large screen / wide desktop
    };

    return {
        /**
         * Class main initialized.
         * @param {object} options
         * @return null
         */
        init(options) {
            if (options && options.breakpoints) {
                breakpoints = options.breakpoints;
            }
        },

        /**
         * Polyfill for forEach from ES6
         * @param array
         * @param callback
         * @param scope
         */
        forEach(array, callback, scope) {
            for (let i = 0; i < array.length; i++) {
                callback.call(scope, array[i], i);
            }
        },

        /**
         * Polyfill for Object.values ES8
         * @param obj
         * @returns {Array}
         */
        objectValues(obj) {
            const res = [];
            for (const i in obj) {
                if (obj.hasOwnProperty(i)) {
                    res.push(obj[i]);
                }
            }
            return res;
        },

        imageLoading(img) {
            img.parentElement.classList.add('loaded');
        },

        /**
         * Checks whether current device is mobile touch.
         * @returns {boolean}
         */
        isMobileDevice() {
            return (this.getViewPort().width < this.getBreakpoint('lg'));
        },

        /**
         * Checks whether current device is desktop.
         * @returns {boolean}
         */
        isDesktopDevice() {
            return !App.isMobileDevice();
        },

        /**
         * Gets browser window viewport size
         * @return {object}
         */
        getViewPort() {
            let e = window;


            let a = 'inner';
            if (!('innerWidth' in window)) {
                a = 'client';
                e = document.documentElement || document.body;
            }

            return {
                width: e[`${a}Width`],
                height: e[`${a}Height`],
            };
        },

        /**
         * Gets window width for give breakpoint mode.
         * @param {string} mode Responsive mode name(e.g: xl, lg, md, sm)
         * @returns {number}
         */
        getBreakpoint(mode) {
            return breakpoints[mode];
        },

        /**
         * Set Cookie
         * @param name
         * @param value
         * @param options
         */
        setCookie(name, value, options) {
            options = options || {};

            let expires = options.expires;

            if (typeof expires === 'number' && expires) {
                const d = new Date();
                d.setTime(d.getTime() + expires * 1000);
                expires = options.expires = d;
            }
            if (expires && expires.toUTCString) {
                options.expires = expires.toUTCString();
            }

            value = encodeURIComponent(value);

            let updatedCookie = `${name}=${value}`;

            for (const propName in options) {
                updatedCookie += `; ${propName}`;
                const propValue = options[propName];
                if (propValue !== true) {
                    updatedCookie += `=${propValue}`;
                }
            }

            document.cookie = updatedCookie;
        },

        /**
         * Delete Cookie
         * @param name
         */
        deleteCookie(name) {
            App.setCookie(name, '', {
                expires: -1,
            });
        },

        /**
         * Get cookie
         * @param name
         * @returns {string | undefined}
         */
        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length == 2) return parts.pop().split(';').shift();
        },

        /**
         * Че посоны, jQuery?
         */
        get(query) {
            let el;

            if (query === document) {
                return document;
            }

            if (query && query.nodeType === 1) {
                return query;
            }

            if (el = document.getElementById(query)) {
                return el;
            } if (el = document.getElementsByTagName(query)) {
                return el[0];
            } if (el = document.getElementsByClassName(query)) {
                return el[0];
            }
            return null;
        },

        getByClass(query) {
            let el;

            if (el = document.getElementsByClassName(query)) {
                return el[0];
            }
            return null;
        },

        /**
         * Checks whether the element has given classes
         * @returns {boolean}
         * @param parent
         * @param query
         */
        find(parent, query) {
            parent = App.get(parent);
            if (parent) {
                return parent.querySelector(query);
            }
        },

        findAll(parent, query) {
            parent = App.get(parent);
            if (parent) {
                return parent.querySelectorAll(query);
            }
        },

        timer(button = null) {
            const t = setInterval(() => {
                function f(x) { return (x / 100).toFixed(2).substr(2); }

                const o = document.getElementById('timer');


                const w = 60;


                const y = o.innerHTML.split(':');


                let v = y[0] * w + (y[1] - 1);


                let s = v % w;


                let m = (v - s) / w;
                if (s < 0) {
                    v = o.getAttribute('long').split(':');
                    m = v[0];
                    s = v[1];
                }
                o.innerHTML = [f(m), f(s)].join(':');

                if (v === 0) {
                    clearInterval(t);
                    if (button) {
                        button.disabled = false;
                        button.firstElementChild.innerHTML = 'Прислать СМС';
                        button.setAttribute('id', '');
                    }
                    alertify.notify('Время истекло', 'success');
                }
            }, 1000);
        },

        /*!
         * Apply a CSS animation to an element
         * (c) 2018 Chris Ferdinandi, MIT License, https://gomakethings.com
         * @param  {Node}    elem      The element to animate
         * @param  {String}  animation The type of animation to apply
         * @param  {Boolean} hide      If true, apply the [hidden] attribute after the animation is done
         */
        animate(elem, animation, hide) {
            // If there's no element or animation, do nothing
            if (!elem || !animation) return;

            // Remove the [hidden] attribute
            elem.removeAttribute('hidden');

            // Apply the animation
            elem.classList.add(animation);

            // Detect when the animation ends
            elem.addEventListener('animationend', function endAnimation(event) {
                // Remove the animation class
                elem.classList.remove(animation);

                // If the element should be hidden, hide it
                if (hide) {
                    elem.setAttribute('hidden', 'true');
                }

                // Remove this event listener
                elem.removeEventListener('animationend', endAnimation, false);
            }, false);
        },
    };
}());

export default App;
