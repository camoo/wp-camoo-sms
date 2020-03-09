var ScheduledMessage=(function($){
	"use strict";
	var me = {
		initialized: false,
		initialize: function (){

			if (me.initialized === true) {
				return;
			}
			me.registerEvents();
			me.initialized = true;
		},


		/**
		 *@return {void}
		 */
		registerEvents: function() {

			$('#inv-steps').on('click', function(){
				me.manageSteps();
			});

		}


	};

	return {
		'initialize' : me.initialize
	};
})(jQuery);


$(function(){
	if($('#rs-invoice-index').length === 0 && $('#no-rs-free-inv').length === 0) {
		ScheduledMessage.initialize();
	}
});
