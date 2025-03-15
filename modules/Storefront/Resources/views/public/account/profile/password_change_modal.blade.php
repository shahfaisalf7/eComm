<div class="modal top fade" id="password_change_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content w-100 shadow-lg">
            <button type="button" class="btn-close" style="position: absolute; top: 0px; right: 12px; z-index: 1050;"
                id="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times-circle la-lg pr-color"></i>
            </button>
            <div class="modal-body p-5">
                <div>
                    <form id="password_change_otp_form" action="{{ route('account-password-update') }}" method="POST">
                        @csrf
                        {{-- <input type="hidden" name="otp" id="otp-password"> --}}
                        <input type="hidden" name="old_phone" value="{{ $account->phone }}">
                        <p class="my-3 text-center text-muted"><strong>{{ __('Change Password') }}</strong></p>
                        <div class="container mt-3">
                            <!-- New Password -->
                            <label for="new_password">New Password</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted" id="password-icon">
                                    <i class="las la-lock"></i>
                                </span>
                                <input type="password" name="new_password" class="password form-control rounded-end"
                                    aria-label="New Password" placeholder="Enter new password"
                                    aria-describedby="password-icon" id="new_password" minlength="8" required>
                                <div class="invalid-feedback" id="new-password-invalid-feedback"></div>
                            </div>

                            <!-- Confirm Password -->
                            <label for="confirm_password">Confirm Password</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted" id="confirm-password-icon">
                                    <i class="las la-lock"></i>
                                </span>
                                <input type="password" name="confirm_password" class="password form-control rounded-end"
                                    placeholder="Confirm new password" aria-label="Confirm Password"
                                    aria-describedby="confirm-password-icon" id="confirm_password" minlength="8"
                                    required>
                                <div class="invalid-feedback" id="confirm-password-invalid-feedback"></div>
                            </div>

                            <!-- Phone -->
                            {{-- <label for="phone">Phone</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted" id="phone-icon">
                                    <i class="las la-mobile"></i>
                                </span>
                                <input type="tel" class="form-control rounded-end"
                                    placeholder="Enter your phone number" aria-label="Phone"
                                    aria-describedby="phone-icon" maxlength="10" value="{{ $account->phone }}" disabled>
                                <div class="invalid-feedback" id="phone-invalid-feedback"></div>
                            </div> --}}

                            <!-- OTP Countdown Timer -->
                            {{-- <div id="otp-countdown-timer-password" class="text-center float-end d-none">
                                <span id="otp-timer-text">{{ __('Time remaining: ') }}</span>
                                <span id="otp-timer-password"></span>
                            </div>
                            <br> --}}

                            <!-- OTP Input -->
                            {{-- <div class="form-outline mb-2 d-none" id="enter-otp-container-password">
                                <div class="d-flex justify-content-between">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <input type="text" maxlength="1"
                                            class="form-control otp-input-password rounded-0 border-gray-300 text-center fs-6 {{ $i == 1 ? 'rounded-start' : '' }} {{ $i == 6 ? 'rounded-end' : '' }}"
                                            id="otpInputPassword{{ $i }}" autocomplete="off" required>
                                    @endfor
                                </div>
                                <div class="invalid-feedback" id="otp-invalid-feedback-password"></div>
                            </div> --}}

                            <!-- Buttons -->
                            <div class="mb-3">
                                {{-- <button type="button" class="btn btn-primary w-100" id="request-otp-button-password">
                                    {{ __('Send code via SMS') }}
                                </button> --}}
                                <button type="submit" class="btn btn-primary w-100"
                                    id="save-change-password-submit-btn">
                                    {{ __('Save and Change') }}
                                </button>
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
        const otpInputs = $(".otp-input-password");
        const requestOtpButton = $("#request-otp-button-password");
        const saveChangeButton = $("#save-change-password-submit-btn");
        const otpTimerContainer = $("#otp-countdown-timer-password");
        const otpTimer = $("#otp-timer-password");
        const passwordChangeOtpForm = $("#password_change_otp_form");
        const otpInvalidFeedbackPassword = $("#otp-invalid-feedback-password");
        const confirmPasswordInvalidFeedback = $("#confirm-password-invalid-feedback");

        // Handle OTP Input Navigation
        otpInputs.on("input", function() {
            const input = $(this);
            if (!/^\d$/.test(input.val())) {
                input.val('');
                return;
            }
            if (input.val() && input.next(".otp-input-password").length) {
                input.next(".otp-input-password").focus();
            }
        });

        // OTP Pasting
        otpInputs.first().on("paste", function(e) {
            const pastedData = (e.originalEvent.clipboardData || window.clipboardData).getData("Text");
            if (!/^\d{6}$/.test(pastedData)) return;
            e.preventDefault();
            otpInputs.each((index, el) => {
                $(el).val(pastedData[index] || '');
            });
        });

        // OTP Backspace Navigation
        otpInputs.on("keydown", function(e) {
            if (e.key === "Backspace" && !$(this).val()) {
                $(this).prev(".otp-input-password").focus();
            }
        });

        // Request OTP
        requestOtpButton.on("click", function(e) {
            e.preventDefault();
            const selector = $(this);
            const newPassword = $("#new_password").val();
            const confirmPassword = $("#confirm_password").val();

            if (newPassword.length < 8) {
                $('#new-password-invalid-feedback').text(
                    'Password must be at least 8 characters long.');
                $('#new_password').addClass('is-invalid');
                return;
            }

            if (newPassword !== confirmPassword) {
                $("#new-password-invalid-feedback").addClass("");
                $("#new_password").removeClass("is-invalid");
                $('#confirm-password-invalid-feedback').text('Passwords do not match.');
                $('#confirm_password').addClass('is-invalid');
                return;
            }

            $("#new-password-invalid-feedback").addClass("");
            $("#new_password").removeClass("is-invalid");
            $("#confirm-password-invalid-feedback").addClass("");
            $("#confirm_password").removeClass("is-invalid");

            selector.addClass("btn-loading");
            requestOtp(selector);
        });

        // Handle OTP Countdown
        let countdownInterval;

        function startOtpCountdown(minutes) {
            otpTimerContainer.removeClass("d-none");
            let remainingTime = minutes * 60;
            countdownInterval = setInterval(() => {
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;
                otpTimer.text(
                    `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`);
                remainingTime--;
                if (remainingTime < 0) {
                    clearInterval(countdownInterval);
                    otpTimerContainer.addClass("d-none");
                }
            }, 1000);
        }

        function requestOtp(selector) {
            $.ajax({
                url: "{{ route('request-otp-code') }}",
                type: "POST",
                contentType: "application/json",
                headers: {
                    "X-CSRF-Token": "{{ csrf_token() }}",
                },
                data: JSON.stringify({
                    phone: "{{ $account->phone }}",
                    otp_phone: "{{ $account->phone }}",
                }),
                success: function(response) {
                    selector.removeClass("btn-loading");
                    selector.addClass("d-none");
                    $("#enter-otp-container-password").removeClass("d-none");
                    $("#save-change-password-submit-btn").removeClass("d-none");
                    startOtpCountdown(5);
                },
                error: function(xhr, status, error) {
                    selector.removeClass("btn-loading");
                }
            });
        }

        passwordChangeOtpForm.on('submit', function(e) {
            e.preventDefault();
            var submit_btn = $("#save-change-password-submit-btn");
            // var otp = '';
            // for (var i = 1; i <= 6; i++) {
            //     otp += $('#otpInputPassword' + i).val();
            // }
            // if (otp.length < 6) {
            //     toastr.error('Please enter a valid OTP.');
            //     return;
            // }
            // $("#otp-password").val(otp);
            submit_btn.addClass("btn-loading");
            $.ajax({
                url: passwordChangeOtpForm.attr('action'),
                type: 'POST',
                data: passwordChangeOtpForm.serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status == "success") {
                        window.location.reload();
                    } else {
                        submit_btn.removeClass("btn-loading");
                        confirmPasswordInvalidFeedback.text(response.message);
                        $(".password").addClass('is-invalid');
                    }
                },
                error: function(xhr) {
                    submit_btn.removeClass("btn-loading");
                    confirmPasswordInvalidFeedback.text(xhr.responseJSON?.message);
                    $(".password").addClass('is-invalid');
                }
            });
        });
    });
</script>
