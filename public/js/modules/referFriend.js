(function($) {
    'use strict';

    const ReferFriend = {
        friendIndex: 1,

        init: function() {
            this.cacheElements();
            this.bindEvents();
        },

        cacheElements: function() {
            this.form = $('.flexcore-refer-friend');
            this.friendsContainer = $('.hd-friends-container');
            this.addFriendBtn = $('.flexcore-add-row');
            this.messageDiv = $('.refer-message');
            this.submitBtn = this.form.find('input[type="submit"]');
            this.nonce = $('#flexcore_refer_nonce').val();
        },

        bindEvents: function() {
            this.form.on('submit', this.handleSubmit.bind(this));
            this.addFriendBtn.on('click', this.addFriendRow.bind(this));
        },

        addFriendRow: function(e) {
            e.preventDefault();

            const newRow = $(`
                <div class="row hd-refer-friend-row flexcore-refer-friend-row">
                    <div class="hd-col-6">
                        <div class="hd-form-group">
                            <label class="hd-label">Friend’s Name<span>*</span></label>
                            <input class="hd-formfild" name="flexcore_friend_data[${this.friendIndex}][name]" type="text" required />
                        </div>
                    </div>
                    <div class="hd-col-6">
                        <div class="hd-form-group">
                            <label class="hd-label">Friend’s Email<span>*</span></label>
                            <input class="hd-formfild" name="flexcore_friend_data[${this.friendIndex}][email]" type="email" required />
                        </div>
                    </div>
                </div>
            `);

            this.friendsContainer.append(newRow);
            this.friendIndex++;
        },

        validateEmail: function(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        validateForm: function() {
            const name = this.form.find('input[name="referral_name"]').val().trim();
            if (!name) {
                this.showMessage('Please enter your preferred name.', 'error');
                return false;
            }

            let isValid = true;
            const friendRows = this.friendsContainer.find('.flexcore-refer-friend-row');

            // Validate each friend name and email
            friendRows.each((index, row) => {
                const friendName = $(row).find('input[type="text"]').val().trim();
                const friendEmail = $(row).find('input[type="email"]').val().trim();

                if (!friendName) {
                    this.showMessage(`Friend #${index + 1}: Name is required.`, 'error');
                    isValid = false;
                    return false; // break each loop
                }
                if (!friendEmail) {
                    this.showMessage(`Friend #${index + 1}: Email is required.`, 'error');
                    isValid = false;
                    return false;
                }
                if (!this.validateEmail(friendEmail)) {
                    this.showMessage(`Friend #${index + 1}: Email is invalid.`, 'error');
                    isValid = false;
                    return false;
                }
            });

            // Check if terms checkbox is checked
            if (!$('#refercheck').is(':checked')) {
                this.showMessage('You must confirm that your friends have agreed to share their emails.', 'error');
                return false;
            }

            return isValid;
        },

        showMessage: function(message, type = 'success') {
            this.messageDiv
                .removeClass('error success')
                .addClass(type)
                .html(message)
                .show();
        },

        clearMessage: function() {
            this.messageDiv.hide().html('');
        },

        handleSubmit: function(e) {
            e.preventDefault();

            if (!this.validateForm()) {
                return;
            }

            this.clearMessage();
            this.submitBtn.prop('disabled', true).val('Sending...');

            // Serialize form data
            const formData = this.form.serializeArray();

            // Add nonce and action manually
            formData.push({ name: 'action', value: 'flexcore_refer_friend' });
            formData.push({ name: 'nonce', value: this.nonce });

            $.ajax({
                url: flexcoreServerAjax.ajaxUrl,
                method: 'POST',
                data: $.param(formData),
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        this.showMessage(response.data.message || 'Referral sent successfully!', 'success');
                        this.form[0].reset();
                        this.friendsContainer.find('.flexcore-refer-friend-row').not(':first').remove();
                        this.friendIndex = 1;
                    } else {
                        this.showMessage(response.data.message || 'Failed to send referral.', 'error');
                    }
                },
                error: () => {
                    this.showMessage('An error occurred. Please try again later.', 'error');
                },
                complete: () => {
                    this.submitBtn.prop('disabled', false).val('Refer my friends now');
                }
            });
        }
    };

    $(document).ready(function() {
        ReferFriend.init();
    });

})(jQuery);
