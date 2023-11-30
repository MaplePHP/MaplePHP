(function formDataModule(global, definition)
{
 // non-exporting module magic dance
    'use strict';

    var
        amd = 'amd',
        exports = 'exports'; // keeps the method names for CommonJS / AMD from being compiled to single character variable

    if (typeof define === 'function' && define[amd]) {
        define(function definer()
        {
            return definition(global);
        });
    } else if (typeof module === 'function' && module[exports]) {
        module[exports] = definition(global);
    } else {
        definition(global);
    }
}(this, function formDataPartialPolyfill(global)
{
 // partial polyfill
    'use strict';

    var
        formDataPrototype,
        math = Math,
        method,
        methods,
        xhrSend,
        xmlHttpRequestPrototype;

    function has(key)
    {
        return this._data.hasOwnProperty(key);
    }

    function append(key, value)
    {
        var
            self = this;

        if (!has.call(self, key)) {
            self._data[key] = [];
        }

        self._data[key].push(value);
    }

    function deleteFn(key)
    {
        delete this._data[key];
    }

    function getAll(key)
    {
        return this._data[key] || null;
    }

    function get(key)
    {
        var
            values = getAll.call(this, key);

        return values ? values[0] : null;
    }

    function set(key, value)
    {
        this._data[key] = [value];
    }

    function createBoundary()
    {
 // for XHR
        var
            random = math.random,
            salt = (random() * math.pow(10, ((random() * 12) | 0) + 1)),
            hash = (random() * salt).toString(36);

        return '----------------FormData-' + hash;
    }

    function parseContents(children)
    {
        var
            child,
            counter,
            counter2,
            length,
            length2,
            name,
            option,
            self = this;

        for (counter = 0, length = children.length; counter < length; counter += 1) {
            child = children[counter];
            name = child.name || child.id;
            if (!name || child.disabled) {
                continue;
            }

            switch (child.type) {
                case 'checkbox':
                    if (child.checked) {
                        self.append(name, child.value || 'on');
                    }

                break;

                case 'image': // x/y coordinates or origin if missing
                    self.append(name + '.x', child.x || 0);
                    self.append(name + '.y', child.y || 0);

                break;

                case 'radio':
                    if (child.checked) {
                        self.set(name, child.value); // using .set as only one can be valid (uses last one if more discovered)
                    }

                break;

                case 'select-one':
                    if (child.selectedIndex !== -1) {
                        self.append(name, child.options[child.selectedIndex].value);
                    }

                break;

                case 'select-multiple':
                    for (counter2 = 0, length2 = child.options.length; counter2 < length2; counter2 += 1) {
                        option = child.options[counter2];
                        if (option.selected) {
                            self.append(name, option.value);
                        }
                    }

                break;

                case 'file':
                case 'reset':
                case 'submit':
                break;

                default: // hidden, text, textarea, password
                    self.append(name, child.value);
            }
        }
    }

    function toString()
    {
        var
            self = this,
            body = [],
            data = self._data,
            key,
            prefix = '--';

        for (key in data) {
            if (data.hasOwnProperty(key)) {
                body.push(prefix + self._boundary); // boundaries are prefixed with '--'
                // only form fields for now, files can wait / probably can't be done
                body.push('Content-Disposition: form-data; name="' + key + '"\r\n'); // two linebreaks between definition and content
                body.push(data[key]);
            }
        }

        if (body.length) {
            return body.join('\r\n') + '\r\n' + prefix + self._boundary + prefix; // form content ends with '--'
        }

        return '';
    }

    /**
     * [FormData description]
     * @contructor
     * @param {?HTMLForm} form HTML <form> element to populate the object (optional)
     */
    function FormData(form)
    {
        var
            self = this;

        if (!(self instanceof FormData)) {
            return new FormData(form);
        }

        if (form && (!form.tagName || form.tagName !== 'FORM')) { // not a form
            return;
        }

        self._boundary = createBoundary();
        self._data = {};

        if (!form) { // nothing to parse, we're done here
            return;
        }

        parseContents.call(self, form.children);
    }

    function send(data)
    {
        var
            self = this;

        if (data instanceof FormData) {
            self.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + data._boundary);

            return xhrSend.call(self, data.toString());
        }

        return xhrSend.call(self, data || null);
    }

    if (!!global.FormData) { // nothing to do...
        return;
    }

    xmlHttpRequestPrototype = global.XMLHttpRequest.prototype;
    xhrSend = xmlHttpRequestPrototype.send;
    xmlHttpRequestPrototype.send = send;

    methods = {
        append: append,
        get: get,
        getAll: getAll,
        has: has,
        set: set,
        toString: toString
    };

    formDataPrototype = FormData.prototype;
    for (method in methods) {
        if (methods.hasOwnProperty(method)) {
            formDataPrototype[method] = methods[method];
        }
    }

    formDataPrototype['delete'] = deleteFn;

    global.FormData = FormData;
}));