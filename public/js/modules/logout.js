(function($) {
    'use strict';
          
console.log('Logout button clicked');
    const logout = {
        init: function() {
            $('.flexcore-logout').on('click', this.handleLogout);
            // Load profile data when dashboard initializes
            this.loadProfileData();
        },
        handleLogout: function(e) {
            e.preventDefault();
            const btn = $(this);
            // console.log('Logout button clicked');
          
            
            btn.prop('disabled', true).addClass('loading');
            const messageDiv = $('#logout-message');

            $.ajax({
                url: flexcoreServerAjax.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'flexcore_logout',
                    nonce: flexcoreServerAjax.logoutNonce
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.redirect) {
                            // console.log('Redirecting to:', response.data.redirect);
                            window.location.href = response.data.redirect;
                        }
                    } else {
                        messageDiv.removeClass('success').addClass('error')
                            .html(response.data.message || flexcoreServerAjax.i18n.errorOccurred)
                            .show();
                    }
                },
                error: function() {
                    messageDiv.removeClass('success').addClass('error')
                        .html(flexcoreServerAjax.i18n.errorOccurred)
                        .show();
                },
                complete: function() {
                    btn.prop('disabled', false).removeClass('loading');
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        logout.init();
    });
    
})(jQuery);
