<div class="modal top fade" id="static_register_modal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true"
    data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content w-100 shadow-lg">
            <button type="button" class="btn-close" style="position: absolute; top: 0px; right: 12px; z-index: 1050;"
                id="modal-close-btn" data-bs-dismiss="modal" aria-label="Close"><i
                    class="las la-times-circle la-lg pr-color"></i>
            </button>
            <div class="modal-body p-5">
                <div class="text-center d-flex justify-content-center">
                    <img src="{{ asset('Floramom/images/register_icon.png') }}" alt="avatar"
                        class="rounded-circle position-absolute top-0 start-50 translate-middle"
                        style="height: 120px !important" />
                </div>
                <div>
                    <!-- Tab Content -->
                    <div class="mt-4">
                        <form id="register_phone_otp_form" method="POST" action="{{ route('verify-otp') }}">
                            <input type="hidden" name="otp" id="register_otp">
                            @csrf
                            <p class="my-3 text-center text-muted"><strong>{{ __('Sign Up with Phone') }}</strong>
                            </p>
                            <div class="container mt-3">
                                <div class="input-group mb-2">
                                    <span class="input-group-text text-muted" id="phone-icon">
                                        +88
                                    </span>
                                    <input type="tel" name="phone" class="form-control rounded-end"
                                        placeholder="Enter your phone number" aria-label="Phone"
                                        aria-describedby="phone-icon" id="register-phone" maxlength="11" />
                                    <div class="fv-plugins-message-container invalid-feedback text-center"
                                        id="register-phone-invalid-feedback"></div>
                                </div>
                                <!-- Countdown Timer -->
                                <div id="register-otp-countdown-timer" class="text-center float-end d-none">
                                    <span id="register-otp-timer-text">{{ __('Time remaining: ') }}</span>
                                    <span id="register-otp-timer"></span>
                                </div>
                                <br>

                                <div class="form-outline mb-2 d-none" id="register-enter-otp-container">
                                    <div class="d-flex justify-content-between">
                                        @for ($i = 1; $i <= 6; $i++)
                                            <input type="text" maxlength="1"
                                                class="form-control register-otp-input rounded-0 border-gray-300 text-center fs-6 {{ $i == 1 ? 'rounded-start' : '' }} {{ $i == 6 ? 'rounded-end' : '' }}"
                                                id="registerOtpInput{{ $i }}" autocomplete="off" required>
                                        @endfor
                                    </div>
                                    <div class="fv-plugins-message-container invalid-feedback text-center"
                                        id="register-otp-invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary w-100" id="register-request-otp-button"
                                        data-loading="">{{ __('Send code via SMS') }}</button>

                                    <button type="submit" class="btn btn-primary w-100 d-none" id="sign-up-submit-btn"
                                        data-loading="">{{ __('Sign Up') }}</button>
                                </div>
                                <span>{{ __('Already have an account?') }}
                                    <a type="button" title="Sign In" class="pr-color" data-bs-toggle="modal"
                                        data-bs-target="#static_login_modal">
                                        Sign In</i>
                                    </a>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script type="module">
        $(document).on("input", ".register-otp-input", function(e) {
            var selector = $(this);
            var otp = selector.val();
            if (!/\d/.test(otp)) {
                selector.val('');
                return;
            }
            if (otp && selector.next('.register-otp-input').length) {
                selector.next('.register-otp-input').focus();
            }
        });

        $('.register-otp-input').first().on('paste', function(e) {
            const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            const pastedData = clipboardData.getData('Text');
            if (!/^\d{6}$/.test(pastedData)) {
                return;
            }
            e.preventDefault();
            $('.register-otp-input').each(function(index) {
                $(this).val(pastedData[index] || '');
            });
            $('.register-otp-input').filter(function() {
                return !this.value;
            }).first().focus();
        });

        $(document).on("keydown", ".register-otp-input", function(e) {
            var selector = $(this);
            if (e.key === 'Backspace' && !selector.val() && selector.prev('.register-otp-input').length) {
                selector.prev('.register-otp-input').focus();
            }
        });

        $(document).off("focus", "#register-request-otp-button").on("focus", "#register-request-otp-button", function(e) {
            e.preventDefault();
            $('#register-phone-invalid-feedback').text('');
            $('#register-phone').removeClass('is-invalid');
        });

        $(document).off("click", "#register-request-otp-button").on("click", "#register-request-otp-button", function(e) {
            e.preventDefault();
            var selector = $(this);
            var phone = $("#register-phone").val().trim();
            var phone_pattern = /^01\d{9}$/;

            if (phone_pattern.test(phone)) {
                selector.addClass("btn-loading");
                requestOtp(phone, selector);
            } else {
                $('#register-phone-invalid-feedback').text('Please enter a valid Bangladeshi phone number.');
                $('#register-phone').addClass('is-invalid');
            }
        });

        function requestOtp(phone, selector) {
            $.ajax({
                url: "{{ route('request-otp') }}",
                type: "POST",
                contentType: "application/json",
                headers: {
                    "X-CSRF-Token": "{{ csrf_token() }}",
                },
                data: JSON.stringify({
                    phone: phone,
                    type: 'register'
                }),
                success: function(response) {
                    if (response.status == 'success') {
                        selector.removeClass("btn-loading");
                        $("#register-phone").prop("readonly", true);
                        selector.addClass("d-none");
                        $("#register-enter-otp-container").removeClass("d-none");
                        $("#sign-up-submit-btn").removeClass("d-none");
                        startOtpCountdown(5);
                    } else {
                        selector.removeClass("btn-loading");
                        $('#register-phone-invalid-feedback').text(response.message);
                        $('#register-phone').addClass('is-invalid');
                    }
                },
                error: function(xhr, status, error) {
                    selector.removeClass("btn-loading");
                    $('#register-phone-invalid-feedback').text(xhr.responseJSON.message);
                    $('#register-phone').addClass('is-invalid');
                    console.error("Error requesting OTP:", xhr.responseText || error);
                }
            });
        }

        $('#register_phone_otp_form').on('submit', function(e) {
            e.preventDefault();
            var submit_btn = $("#sign-up-submit-btn");
            var otp = '';
            for (var i = 1; i <= 6; i++) {
                otp += $('#registerOtpInput' + i).val();
            }
            if (otp.length < 6) {
                toastr.error('Please enter a valid OTP.');
                return;
            }
            $("#register_otp").val(otp);
            submit_btn.addClass("btn-loading");
            $.ajax({
                url: $('#register_phone_otp_form').attr('action'),
                type: 'POST',
                data: $('#register_phone_otp_form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status == "success") {
                        window.location.href = response.redirectUrl || '/';
                    } else {
                        submit_btn.removeClass("btn-loading");
                        $('#register-otp-invalid-feedback').text(response.message);
                        $('.register-otp-input').addClass('is-invalid');
                    }
                },
                error: function(xhr) {
                    submit_btn.removeClass("btn-loading");
                    toastr.error(xhr.responseJSON?.message || 'Failed to process the request.');
                }
            });
        });

        // for countdown
        let countdownInterval;

        function startOtpCountdown(durationInMinutes) {
            $('#register-otp-countdown-timer').removeClass('d-none');
            let remainingTime = durationInMinutes * 60;

            function updateTimerDisplay() {
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;
                $('#register-otp-timer').text(`${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`);
                remainingTime--;
                if (remainingTime < 0) {
                    clearInterval(countdownInterval);
                    $('#register-otp-countdown-timer').addClass('d-none');
                    $("#phone").prop("readonly", false);
                    $("#request-otp-button").removeClass("d-none");
                    $("#register-enter-otp-container").addClass("d-none");
                    $("#sign-in-submit-btn").addClass("d-none");
                }
            }
            updateTimerDisplay();
            countdownInterval = setInterval(updateTimerDisplay, 1000);
        }
    </script>
@endpush
