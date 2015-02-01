/**
 * Created by damian on 26/01/15.
 */
ko.yii = ko.yii || {};
ko.yii.validation = function(messages) {
    ko.validation.rules['required'] = {
        validator: function (value, options) {
            var valid = false;
            if (options.requiredValue === undefined) {
                var isString = typeof value == 'string' || value instanceof String;
                if (options.strict && value !== undefined || !options.strict && !this.isEmpty(isString ? $.trim(value) : value)) {
                    valid = true;
                }
            } else if (!options.strict && value == options.requiredValue || options.strict && value === options.requiredValue) {
                valid = true;
            }
            if (options.message != undefined) {
                this.message = options.message || this.message;
            }
            return valid;
        },
        message: messages.required.default,
        isEmpty: function (value) {
            return value === null || value === undefined || value == [] || value === '';
        }
    };
    ko.validation.rules['string'] = {
        validator: function (value, options) {
            if (options.skipOnEmpty && this.isEmpty(value)) {
                return true;
            }

            if (typeof value !== 'string') {
                this.message = options.message || this.message;
                return false;
            }

            if (options.min !== undefined && value.length < options.min) {
                this.message = options.tooShort || messages.string.tooShort;
                return false;
            }
            if (options.max !== undefined && value.length > options.max) {
                this.message = options.tooLong || messages.string.tooLong;
                return false;
            }
            if (options.is !== undefined && value.length != options.is) {
                this.message = options.notEqual || messages.string.notEqual;
                return false;
            }
            return true;
        },
        message: messages.string.default,
        isEmpty: function (value) {
            return value === null || value === undefined || value == [] || value === '';
        }
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

            if (options.skipOnEmpty && this.isEmpty(value)) {
                return true;
            }

            if ((typeof value === 'string' && !value.match(options.pattern))) {
                this.message = options.message;
                return false;
            }

            if (options.min !== undefined && value < options.min) {
                this.message = options.tooSmall;
                return false;
            }
            if (options.max !== undefined && value > options.max) {
                this.message = options.tooBig;
                return false;
            }
            return true;
        },
        message: messages.integer.default,
        isEmpty: function (value) {
            return value === null || value === undefined || value == [] || value === '';
        }
    };
    ko.validation.rules['email'] = {
        validator: function (value, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

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

            if (!valid || !(value.match(options.pattern) || (options.allowName && value.match(options.fullPattern)))) {
                this.message = options.message || this.message;
                return false;
            }
            return true;
        },
        message: messages.email.default,
        isEmpty: function (value) {
            return value === null || value === undefined || value == [] || value === '';
        }
    };

    ko.validation.rules['boolean'] = {
        validator: function (value, options) {
            if (options.skipOnEmpty && this.isEmpty(value)) {
                return true;
            }
            var valid = !options.strict && (value == options.trueValue || value == options.falseValue)
                || options.strict && (value === options.trueValue || value === options.falseValue);

            this.message = options.message || this.message;
            return valid;
        },
        message: messages.boolean.default,
        isEmpty: function (value) {
            return value === null || value === undefined || value == [] || value === '';
        }
    };

    ko.validation.registerExtenders();

};