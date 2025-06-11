define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract'
], function ($, _, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            cols: 15,
            rows: 2,
            template: 'ui/form/field',
            elementTmpl: 'Cytracon_ConditionalLogic/form/element/logic'
        }
    });
});