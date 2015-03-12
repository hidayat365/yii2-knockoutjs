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

    ko.bindingHandlers.hidden = {
        update: function(element, valueAccessor, allBindingsAccessor) {
            var value = ko.utils.unwrapObservable(valueAccessor());
            ko.bindingHandlers.visible.update(element, function() { return!value; });
        }
    };
}
