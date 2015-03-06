/**
 * Created by damian on 1/03/15.
 */
if (typeof ko !== 'undefined') {
    ko.bindingHandlers.element = {
        init: function (element, valueAccessor, allBindingsAccessor) {
            var target = valueAccessor();
            target(element);
        }
    };
}
