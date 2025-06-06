define([
    'jquery',
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function ($, DynamicRows) {
    'use strict';

    return DynamicRows.extend({

    	defaults: {
    		parseData: false
    	},

        initialize: function () {
            this._super();

            var self = this;
            $(document).on('click', '.bfb-conditional', function(event) {
            	if (!self.parseData) {
            		self.parseData = true;
            		self.recordData(self.recordDataCache);
            		self.reload();
            	}
            	
            });

            return this;
        },

        /**
         * Filtering data and calculates the quantity of pages
         *
         * @param {Array} data
         */
        parsePagesData: function (data) {
        	if (this.parseData) {
	            this._super(data);
	        }
        }

    })
});