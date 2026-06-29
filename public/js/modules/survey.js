(function($) {
    'use strict';
    // console.log('Survey module loaded');
    
    const survey = {
        init: function() {
            this.bindEvents();
        },
        bindEvents: function() {
            $('#take_survey').on('click', this.handleSubmit);
        },
        handleSubmit: function(e) {
            e.preventDefault();
           
            

            $.ajax({
                 url: flexcoreServerAjax.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'flexcore_get_survey',
                    nonce: flexcoreServerAjax.surveyNonce
                },
                success: function(response) {
                    let surveyUrl= response.data.data;
                    window.location.href = surveyUrl
                },
                error: function() {
                    alert(flexcoreServerAjax.i18n.errorOccurred);
                }
            });
        }
    };
     survey.init();
})(jQuery);
