<div class="modal top fade" id="email_change_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content w-100 shadow-lg">
            <button type="button" class="btn-close" style="position: absolute; top: 0px; right: 12px; z-index: 1050;"
                id="modal-close-btn" data-bs-dismiss="modal" aria-label="Close"><i
                    class="las la-times-circle la-lg pr-color"></i>
            </button>
            <div class="modal-body p-5">
                <div>
                    <form id="email_change_otp_form" action="{{ route('account-email-update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="otp" id="otp-email">
                        <input type="hidden" name="phone" value="{{ $account->phone }}">
                        <p class="my-3 text-center text-muted"><strong>{{ __('Change Email') }}</strong></p>
                        <div class="container mt-3">
                            <label for="pervious_email">Previous Email</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted" id="email-icon">
                                    <i class="las la-envelope"></i>
                                </span>
                                <input type="email" name="previous_email" class="form-control rounded-end"
                                    aria-label="Email" aria-describedby="email-icon" id="previous_email"
                                    value="{{ $account->email }}" disabled />
                            </div>

                            <label for="email">New Email</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted" id="email-icon">
                                    <i class="las la-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control rounded-end"
                                    placeholder="Enter your email" aria-label="Email" aria-describedby="email-icon"
                                    id="email" value="" />
                                <div class="fv-plugins-message-container invalid-feedback"
                                    id="new-email-invalid-feedback">
                                </div>
                            </div>

                            <label for="phone">Phone</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted" id="phone-icon">
                                    <i class="las la-mobile"></i>
                                </span>
                                <input type="tel" class="form-control rounded-end"
                                    placeholder="Enter your phone number" aria-label="Phone"
                                    aria-describedby="phone-icon" maxlength="10" value="{{ $account->phone }}"
                                    disabled />
                            </div>
                            <!-- Countdown Timer -->
                            <div id="otp-countdown-timer-email" class="text-center float-end d-none">
                                <span id="otp-timer-text">{{ __('Time remaining: ') }}</span>
                                <span id="otp-timer-email"></span>
                            </div>
                            <br>
                            <div class="form-outline mb-2 d-none" id="enter-otp-container-email">
                                <div class="d-flex justify-content-between">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <input type="text" maxlength="1"
                                            class="form-control otp-input-email rounded-0 border-gray-300 text-center fs-6 {{ $i == 1 ? 'rounded-start' : '' }} {{ $i == 6 ? 'rounded-end' : '' }}"
                                            id="otpInputEmail{{ $i }}" autocomplete="off" required>
                                    @endfor
                                </div>
                                <div class="fv-plugins-message-container invalid-feedback"
                                    id="otp-invalid-feedback-email">
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary w-100" id="request-otp-button-email"
                                    data-loading="">{{ __('Send code via SMS') }}</button>

                                <button type="submit" class="btn btn-primary w-100 d-none" id="save-change-submit-btn"
                                    data-loading="">{{ __('Save and Change') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    $(document).ready(function() {
        $(document).on("input", ".otp-input-email", function(e) {
            var selector = $(this);
            var otp = selector.val();
            if (!/\d/.test(otp)) {
                selector.val('');
                return;
            }
            if (otp && selector.next('.otp-input-email').length) {
                selector.next('.otp-input-email').focus();
            }
        });

        $('.otp-input-email').first().on('paste', function(e) {
            const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            const pastedData = clipboardData.getData('Text');
            if (!/^\d{6}$/.test(pastedData)) {
                return;
            }
            e.preventDefault();
            $('.otp-input-email').each(function(index) {
                $(this).val(pastedData[index] || '');
            });
            $('.otp-input-email').filter(function() {
                return !this.value;
            }).first().focus();
        });

        $(document).on("keydown", ".otp-input-email", function(e) {
            var selector = $(this);
            if (e.key === 'Backspace' && !selector.val() && selector.prev('.otp-input-email').length) {
                selector.prev('.otp-input-email').focus();
            }
        });

        $(document).off("focus", "#request-otp-button-email").on("focus", "#request-otp-button-email", function(
            e) {
            e.preventDefault();
            $('#new-email-invalid-feedback').text('Please enter a valid email.');
            $('#email').removeClass('is-invalid');
        });

        $(document).off("click", "#request-otp-button-email").on("click", "#request-otp-button-email", function(
            e) {
            e.preventDefault();
            var selector = $(this);
            var phone = $("#phone").val().trim();
            var email = $("#email").val().trim();
            var email_pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (email_pattern.test(email)) {
                selector.addClass("btn-loading");
                requestOtp(selector, email);
            } else {
                $('#new-email-invalid-feedback').text('Please enter a valid email.');
                $('#email').addClass('is-invalid');
            }
        });

        function requestOtp(selector, email) {
            $.ajax({
                url: "{{ route('request-otp-code') }}",
                type: "POST",
                contentType: "application/json",
                headers: {
                    "X-CSRF-Token": "{{ csrf_token() }}",
                },
                data: JSON.stringify({
                    otp_phone: "{{ $account->phone }}",
                    email: email,
                }),
                success: function(response) {
                    selector.removeClass("btn-loading");
                    selector.addClass("d-none");
                    $("#enter-otp-container-email").removeClass("d-none");
                    $("#save-change-submit-btn").removeClass("d-none");
                    $('#new-email-invalid-feedback').text("");
                    $('#email').removeClass('is-invalid');
                    startOtpCountdown(5);
                },
                error: function(xhr, status, error) {
                    selector.removeClass("btn-loading");
                    $('#new-email-invalid-feedback').text(xhr.responseJSON?.errors.email[0] ||
                        'Failed to process the request.');
                    $('#email').addClass('is-invalid');
                }
            });
        }

        $('#email_change_otp_form').on('submit', function(e) {
            e.preventDefault();
            var submit_btn = $("#save-change-submit-btn");
            var otp = '';
            for (var i = 1; i <= 6; i++) {
                otp += $('#otpInputEmail' + i).val();
            }
            if (otp.length < 6) {
                toastr.error('Please enter a valid OTP.');
                return;
            }
            $("#otp-email").val(otp);
            submit_btn.addClass("btn-loading");
            $.ajax({
                url: $('#email_change_otp_form').attr('action'),
                type: 'POST',
                data: $('#email_change_otp_form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status == "success") {
                        window.location.reload();
                    } else {
                        submit_btn.removeClass("btn-loading");
                        $('#otp-invalid-feedback-email').text(response.message);
                        $('.otp-input-email').addClass('is-invalid');
                    }
                },
                error: function(xhr) {
                    submit_btn.removeClass("btn-loading");
                    $('#otp-invalid-feedback-email').text(xhr.responseJSON?.message ||
                        'Failed to process the request.');
                    $('.otp-input-email').addClass('is-invalid');
                }
            });
        });

        // for countdown
        let countdownInterval;

        function startOtpCountdown(durationInMinutes) {
            $('#otp-countdown-timer-email').removeClass('d-none');
            let remainingTime = durationInMinutes * 60;

            function updateTimerDisplay() {
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;
                $('#otp-timer-email').text(
                    `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`);
                remainingTime--;
                if (remainingTime < 0) {
                    clearInterval(countdownInterval);
                    $('#otp-countdown-timer-email').addClass('d-none');
                    $("#request-otp-button-email").removeClass("d-none");
                    $("#enter-otp-container-email").addClass("d-none");
                    $("#save-change-submit-btn").addClass("d-none");
                }
            }
            updateTimerDisplay();
            countdownInterval = setInterval(updateTimerDisplay, 1000);
        }
    });
</script>
