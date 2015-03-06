/**
 * Created by damian on 26/01/15.
 */
ko.yii = ko.yii || {};
ko.yii.validation = function(messages) {

    var self = this;
    self.messages = messages;

    self.isEmpty = function (value) {
        return value === null || value === undefined || value == [] || value === '';
    };

    self.addAttribute = function(message, options) {
        if (typeof options.attribute != 'undefined') {
            var matches;
            var pattern = /\{([^,]+), plural, one\{([^}]+)\} other\{(([^}]+))\}\}/;
            if (matches = message.match(pattern)) {
                var replacement = matches[2];
                if (typeof options[matches[1]] != 'undefined' && options[matches[1]] > 1) {
                    replacement = matches[3];
                }
                message = message.replace(pattern, replacement);
            }
            message = message.replace(/\{max(, number)?\}/, options.max | 0);
            message = message.replace(/\{min(, number)?\}/, options.min | 0);
            return message.replace(/\{attribute\}/, options.attribute);
        }
        return message;
    };

    ko.validation.rules['required'] = {
        validator: function (value, options) {
            var valid = false;
            if (options.requiredValue === undefined) {
                var isString = typeof value == 'string' || value instanceof String;
                if (options.strict && value !== undefined || !options.strict && !self.isEmpty(isString ? $.trim(value) : value)) {
                    valid = true;
                }
            } else if (!options.strict && value == options.requiredValue || options.strict && value === options.requiredValue) {
                valid = true;
            }
            this.message = options.message || self.messages.required.default;
            this.message = self.addAttribute(this.message, options);

            return valid;
        },
        message: messages.required.default
    };

    ko.validation.rules['string'] = {
        validator: function (value, options) {

            var defaultOptions = {
                skipOnEmpty: true
            };
            options = ko.utils.extend(defaultOptions, options);

            if (options.skipOnEmpty && self.isEmpty(value)) {
                return true;
            }

            if (typeof value !== 'string') {
                this.message = options.message || self.messages.string.default;
                this.message = self.addAttribute(this.message, options);
                return false;
            }

            if (options.min !== undefined && value.length < options.min) {
                this.message = options.tooShort || self.messages.string.tooShort;
                this.message = self.addAttribute(this.message, options);
                return false;
            }
            if (options.max !== undefined && value.length > options.max) {
                this.message = options.tooLong || self.messages.string.tooLong;
                this.message = self.addAttribute(this.message, options);
                return false;
            }
            if (options.is !== undefined && value.length != options.is) {
                this.message = options.notEqual || self.messages.string.notEqual;
                this.message = self.addAttribute(this.message, options);
                return false;
            }

            return true;
        },
        message: self.messages.string.default
    };
    ko.validation.rules['integer'] = {

        validator: function (value, options) {

            var integerPattern = /^\s*[+-]?\d+\s*$/;
            var numberPattern = /^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/;
            var integerMessage = messages.integer.integerOnly;
            var numberMessage = messages.integer.default;

            var defaults = {
                'integerOnly': false,
                'pattern': options.integerOnly ? integerPattern : numberPattern,
                'tooBig': messages.integer.tooBig,
                'tooSmall': messages.integer.tooSmall,
                'message': options.integerOnly ? integerMessage : numberMessage
            };

            options = $.extend(defaults, options);

            if (options.skipOnEmpty && self.isEmpty(value)) {
                return true;
            }

            if ((typeof value === 'string' && !value.match(options.pattern))) {
                this.message = options.message;
                this.message = self.addAttribute(self.messages.integer.default, options);
                return false;
            }

            if (options.min !== undefined && value < options.min) {
                this.message = options.tooSmall || self.messages.integer.tooSmall;
                this.message = self.addAttribute(this.message, options);
                return false;
            }
            if (options.max !== undefined && value > options.max) {
                this.message = options.tooBig || self.messages.integer.tooBig;
                this.message = self.addAttribute(this.message, options);
                return false;
            }


            return true;
        },
        message: self.messages.integer.default
    };
    ko.validation.rules['email'] = {
        validator: function (value, options) {

            if (options.skipOnEmpty && self.isEmpty(value)) {
                return;
            }

            var pattern = /^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/;
            var fullPattern = /^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/;

            var valid = true;

            if (options.enableIDN) {
                var regexp = /^(.*<?)(.*)@(.*)(>?)$/,
                    matches = regexp.exec(value);
                if (matches === null) {
                    valid = false;
                } else {
                    value = matches[1] + punycode.toASCII(matches[2]) + '@' + punycode.toASCII(matches[3]) + matches[4];
                }
            }

            if (!valid || !(value.match(pattern) || (options.allowName && value.match(fullPattern)))) {
                this.message = options.message || self.messages.email.default;
                this.message = self.addAttribute(this.message, options);
                valid = false;
            }
            return valid;
        },
        message: self.messages.email.default
    };

    ko.validation.rules['boolean'] = {
        validator: function (value, options) {
            if (options.skipOnEmpty && self.isEmpty(value)) {
                return true;
            }
            var valid = !options.strict && (value == options.trueValue || value == options.falseValue)
                || options.strict && (value === options.trueValue || value === options.falseValue);

            this.message = options.message || self.messages.boolean.default;
            this.message = self.addAttribute(this.message, options);
            return valid;
        },
        message: self.messages.boolean.default
    };

    ko.validation.registerExtenders();

};