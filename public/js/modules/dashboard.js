/**
 * Dashboard functionality
 */
(function($) {
    'use strict';

    const Dashboard = {
        init: function() {
            $('.flexcore-logout').on('click', this.handleLogout);
            // Load profile data when dashboard initializes
            this.loadProfileData();
        },

        loadProfileData: function() {
            const messageDiv = $('#dashboard-message');
            
            $.ajax({
                url: flexcoreServerAjax.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'flexcore_get_profile',
                    nonce: flexcoreServerAjax.profileNonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // Update profile display with fresh data
                        const data = response.data;
                        $('.profile-name').text(data.name || '');
                        $('.profile-email').text(data.email || '');
                        $('.profile-mobile').text(data.mobileNumber || '');
                        $('.profile-dob').text(data.dateOfBirth || '');
                        $('.profile-gender').text(data.gender || '');
                        $('.profile-race').text(data.race || '');
                        $('.profile-marital-status').text(data.maritalStatus || '');
                        $('.profile-citizenship').text(data.citizenship || '');
                    } else {
                        messageDiv.removeClass('success').addClass('error')
                            .html(flexcoreServerAjax.i18n.loadFailed)
                            .show();
                    }
                },
                error: function() {
                    messageDiv.removeClass('success').addClass('error')
                        .html(flexcoreServerAjax.i18n.errorOccurred)
                        .show();
                }
            });
        },

        handleLogout: function(e) {
            e.preventDefault();
            const btn = $(this);

            // if (!confirm(flexcoreServerAjax.i18n.logoutConfirm)) {
            //     return;
            // }
            
            btn.prop('disabled', true).addClass('loading');
            const messageDiv = $('#dashboard-message');

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
        Dashboard.init();
    });

})(jQuery);
