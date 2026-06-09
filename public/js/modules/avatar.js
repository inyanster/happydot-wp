(function($) {
    'use strict';

    const Avatar = {
        profileData: window.FlexcoreProfile?.profileData,
        currentAvatar: null,
        avatarIdInput: null,

        init: function() {
            this.currentAvatar = $('#current-avatar');
            this.avatarIdInput = $('#avatarId');

            // Set initial avatar
            if (this.profileData?.metaData?.avatarId && this.currentAvatar.length) {
                const avatarId = this.profileData.metaData.avatarId;
                this.currentAvatar.attr('src', `http://localhost/happydot/wp-content/uploads/avatar/${avatarId}.png`);
                this.avatarIdInput.val(avatarId);
            }

            // Bind events
            $('#edit-avatar-btn').on('click', this.openModal.bind(this));
            $('#close-avatar-modal').on('click', this.closeModal.bind(this));
            $('.avatar-option').on('click', this.selectAvatar.bind(this));
            $('#avatar-form').on('submit', this.handleSubmit.bind(this));
        },

        openModal: function() {
            // Show modal
            $('#avatar-modal').show();
            // Prevent background scrolling
            $('body').addClass('modal-open');
        },

        closeModal: function() {
            // Hide modal
            $('#avatar-modal').hide();
            // Enable background scrolling
            $('body').removeClass('modal-open');
        },

        selectAvatar: function(e) {
            const img = $(e.currentTarget);
            const selectedId = img.data('id');
            const selectedSrc = img.attr('src');
            
            this.currentAvatar.attr('src', selectedSrc); // Preview
            this.avatarIdInput.val(selectedId); // Store ID
        },

        handleSubmit: function(e) {
            e.preventDefault();
            const form = $(e.currentTarget);
            const submitBtn = form.find('button[type="submit"]');
            const messageBox = $('#avatar-update-message');
            const avatarId = this.avatarIdInput.val();

            submitBtn.prop('disabled', true);
            messageBox.hide();

            $.ajax({
                url: flexcoreServerAjax.ajaxUrl,
                type: 'POST', // WordPress requires POST for admin-ajax.php
                data: {
                    action: 'flexcore_change_avatar',
                    avatarId: avatarId,
                    nonce: $('#change_avatar_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        messageBox.removeClass('error').addClass('success')
                            .text(response.data?.message || 'Avatar updated!')
                            .show();
                    } else {
                        messageBox.removeClass('success').addClass('error')
                            .text(response.data?.message || 'Update failed.')
                            .show();
                    }
                },
                error: function() {
                    messageBox.removeClass('success').addClass('error')
                        .text(flexcoreServerAjax.i18n?.errorOccurred || 'An error occurred. Please try again.')
                        .show();
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    $('#avatar-modal').hide();
                    $('body').removeClass('modal-open');
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        Avatar.init();
    });

})(jQuery);
