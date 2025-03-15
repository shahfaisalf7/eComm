<div class="modal top fade" id="phone_change_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content w-100 shadow-lg">
            <button type="button" class="btn-close" style="position: absolute; top: 0px; right: 12px; z-index: 1050;"
                id="modal-close-btn" data-bs-dismiss="modal" aria-label="Close"><i
                    class="las la-times-circle la-lg pr-color"></i>
            </button>
            <div class="modal-body p-5">
                <div>
                    <form id="phone_change_otp_form" action="{{ route('account-phone-update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="otp" id="otp-phone">
                        <input type="hidden" name="old_phone" value="{{ $account->phone }}">
                        <p class="my-3 text-center text-muted"><strong>{{ __('Change Phone') }}</strong></p>
                        <div class="container mt-3">
                            <label for="pervious_phone">Previous Phone</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted" id="phone-icon">
                                    <i class="las la-mobile"></i>
                                </span>
                                <input type="tel" name="previous_phone" class="form-control rounded-end"
                                    aria-label="Phone" aria-describedby="phone-icon" id="previous_phone"
                                    value="{{ $account->phone }}" maxlength="10" disabled />
                            </div>

                            <label for="phone">New Phone</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted" id="phone-icon">
                                    +88
                                </span>
                                <input type="tel" name="phone" class="form-control rounded-end"
                                    placeholder="Enter your phone" aria-label="Phone" aria-describedby="phone-icon"
                                    id="new_phone" value="" maxlength="11" />
                                <div class="fv-plugins-message-container invalid-feedback"
                                    id="new-phone-invalid-feedback">
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
                                <div class="fv-plugins-message-container invalid-feedback" id="phone-invalid-feedback">
                                </div>
                            </div>
                            <!-- Countdown Timer -->
                            <div id="otp-countdown-timer-phone" class="text-center float-end d-none">
                                <span id="otp-timer-text">{{ __('Time remaining: ') }}</span>
                                <span id="otp-timer-phone"></span>
                            </div>
                            <br>
                            <div class="form-outline mb-2 d-none" id="enter-otp-container-phone">
                                <div class="d-flex justify-content-between">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <input type="text" maxlength="1"
                                            class="form-control otp-input-phone rounded-0 border-gray-300 text-center fs-6 {{ $i == 1 ? 'rounded-start' : '' }} {{ $i == 6 ? 'rounded-end' : '' }}"
                                            id="otpInputPhone{{ $i }}" autocomplete="off" required>
                                    @endfor
                                </div>
                                <div class="fv-plugins-message-container invalid-feedback"
                                    id="otp-invalid-feedback-phone">
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary w-100" id="request-otp-button-phone"
                                    data-loading="">{{ __('Send code via SMS') }}</button>

                                <button type="submit" class="btn btn-primary w-100 d-none"
                                    id="save-change-phone-submit-btn"
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
        $(document).on("input", ".otp-input-phone", function(e) {
            var selector = $(this);
            var otp = selector.val();
            if (!/\d/.test(otp)) {
                selector.val('');
                return;
            }
            if (otp && selector.next('.otp-input-phone').length) {
                selector.next('.otp-input-phone').focus();
            }
        });

        $('.otp-input-phone').first().on('paste', function(e) {
            const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            const pastedData = clipboardData.getData('Text');
            if (!/^\d{6}$/.test(pastedData)) {
                return;
            }
            e.preventDefault();
            $('.otp-input-phone').each(function(index) {
                $(this).val(pastedData[index] || '');
            });
            $('.otp-input-phone').filter(function() {
                return !this.value;
            }).first().focus();
        });

        $(document).on("keydown", ".otp-input-phone", function(e) {
            var selector = $(this);
            if (e.key === 'Backspace' && !selector.val() && selector.prev('.otp-input-phone').length) {
                selector.prev('.otp-input-phone').focus();
            }
        });

        $(document).off("focus", "#request-otp-button-phone").on("focus", "#request-otp-button-phone", function(
            e) {
            e.preventDefault();
            $('#new-phone-invalid-feedback').text('');
            $('#new_phone').removeClass('is-invalid');
        });

        $(document).off("click", "#request-otp-button-phone").on("click", "#request-otp-button-phone", function(
            e) {
            e.preventDefault();
            var selector = $(this);
            var phone = $("#new_phone").val().trim();
            var phone_pattern = /^01\d{9}$/;
            if (phone_pattern.test(phone)) {
                selector.addClass("btn-loading");
                requestOtp(selector, phone);
            } else {
                $('#new-phone-invalid-feedback').text('Please enter a valid phone number.');
                $('#new_phone').addClass('is-invalid');
            }
        });

        function requestOtp(selector, phone) {
            $.ajax({
                url: "{{ route('request-otp-code') }}",
                type: "POST",
                contentType: "application/json",
                headers: {
                    "X-CSRF-Token": "{{ csrf_token() }}",
                },
                data: JSON.stringify({
                    otp_phone: "{{ $account->phone }}",
                    phone: phone,
                }),
                success: function(response) {
                    selector.removeClass("btn-loading");
                    selector.addClass("d-none");
                    $("#enter-otp-container-phone").removeClass("d-none");
                    $("#save-change-phone-submit-btn").removeClass("d-none");
                    $('#new-phone-invalid-feedback').text("");
                    $('#new_phone').removeClass('is-invalid');
                    startOtpCountdown(5);
                },
                error: function(xhr, status, error) {
                    selector.removeClass("btn-loading");

                    $('#new-phone-invalid-feedback').text(xhr.responseJSON?.errors.phone[0] ||
                        'Failed to process the request.');
                    $('#new_phone').addClass('is-invalid');
                }
            });
        }

        $('#phone_change_otp_form').on('submit', function(e) {
            e.preventDefault();
            var submit_btn = $("#save-change-phone-submit-btn");
            var otp = '';
            for (var i = 1; i <= 6; i++) {
                otp += $('#otpInputPhone' + i).val();
            }
            if (otp.length < 6) {
                toastr.error('Please enter a valid OTP.');
                return;
            }
            $("#otp-phone").val(otp);
            submit_btn.addClass("btn-loading");
            $.ajax({
                url: $('#phone_change_otp_form').attr('action'),
                type: 'POST',
                data: $('#phone_change_otp_form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status == "success") {
                        window.location.reload();
                    } else {
                        submit_btn.removeClass("btn-loading");
                        $('#otp-invalid-feedback-phone').text(response.message);
                        $('.otp-input-phone').addClass('is-invalid');
                    }
                },
                error: function(xhr) {
                    submit_btn.removeClass("btn-loading");
                    $('#otp-invalid-feedback-phone').text(xhr.responseJSON?.message ||
                        'Failed to process the request.');
                    $('.otp-input-phone').addClass('is-invalid');
                }
            });
        });

        // for countdown
        let countdownInterval;

        function startOtpCountdown(durationInMinutes) {
            $('#otp-countdown-timer-phone').removeClass('d-none');
            let remainingTime = durationInMinutes * 60;

            function updateTimerDisplay() {
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;
                $('#otp-timer-phone').text(
                    `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`);
                remainingTime--;
                if (remainingTime < 0) {
                    clearInterval(countdownInterval);
                    $('#otp-countdown-timer-phone').addClass('d-none');
                    $("#request-otp-button-phone").removeClass("d-none");
                    $("#enter-otp-container-phone").addClass("d-none");
                    $("#save-change-phone-submit-btn").addClass("d-none");
                }
            }
            updateTimerDisplay();
            countdownInterval = setInterval(updateTimerDisplay, 1000);
        }
    });
</script>
