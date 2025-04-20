<div class="modal top fade" id="static_login_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content w-100 shadow-lg">
            <button type="button" class="btn-close" style="position: absolute; top: 0px; right: 12px; z-index: 1050;"
                id="modal-close-btn" data-bs-dismiss="modal" aria-label="Close"><i
                    class="las la-times-circle la-lg pr-color"></i>
            </button>
            <div class="modal-body p-5">
                <div class="text-center d-flex justify-content-center">
                    <img src="{{ asset('Floramom/images/login_icon.webp') }}" alt="avatar"
                        class="rounded-circle position-absolute top-0 start-50 translate-middle"
                        style="height: 120px !important" />
                </div>
                <div>
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs justify-content-between" id="loginTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="password-login-tab" data-bs-toggle="tab"
                                data-bs-target="#password-login" type="button" role="tab"
                                aria-controls="password-login" aria-selected="true">Password</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="phone-tab" data-bs-toggle="tab" data-bs-target="#phone-login"
                                type="button" role="tab" aria-controls="phone-login" aria-selected="false">Phone
                                Number</button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-4" id="loginTabContent">
                        <!-- Password Login Tab -->
                        <div class="tab-pane fade show active" id="password-login" role="tabpanel"
                            aria-labelledby="password-login-tab">
                            <form id="password_signin_form" method="POST" action="{{ route('user-login') }}">
                                @csrf
                                <p class="my-3 text-center text-muted"><strong>{{ __('Sign In') }}</strong></p>
                                <div class="container mt-3">
                                    <!-- Email/Phone Input -->
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="email-phone-icon">
                                            <!-- New Icon (User Icon as an example) -->
                                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20"
                                                height="20" viewBox="0 0 20 20" fill="none">
                                                <path
                                                    d="M10 10C12.3012 10 14.1667 8.13452 14.1667 5.83333C14.1667 3.53214 12.3012 1.66667 10 1.66667C7.69881 1.66667 5.83333 3.53214 5.83333 5.83333C5.83333 8.13452 7.69881 10 10 10Z"
                                                    stroke="#A0AEC0" stroke-width="1.5" stroke-miterlimit="10"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M2.91667 18.3333C2.91667 15.4167 6.25 13.75 10 13.75C13.75 13.75 17.0833 15.4167 17.0833 18.3333"
                                                    stroke="#A0AEC0" stroke-width="1.5" stroke-miterlimit="10"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <input type="text" name="email_phone" class="form-control"
                                            placeholder="Please enter your Phone Number or Email"
                                            aria-label="email_phone" aria-describedby="email-phone-icon"
                                            id="email_phone" required />
                                    </div>

                                    <!-- Password Input -->
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="password-icon">
                                            <!-- Password Icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                fill="none" viewBox="0 0 20 20">
                                                <path
                                                    d="M14.167 9.16667H5.83334C4.23334 9.16667 3.33334 9.16667 3.33334 10.8333V15.8333C3.33334 17.5 4.23334 17.5 5.83334 17.5H14.167C15.7667 17.5 16.6667 17.5 16.6667 15.8333V10.8333C16.6667 9.16667 15.7667 9.16667 14.167 9.16667Z"
                                                    stroke="#A0AEC0" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M10 13.3333V14.1667" stroke="#A0AEC0" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M7.5 7.08333V5.83333C7.5 4.34167 8.675 3.16667 10 3.16667C11.325 3.16667 12.5 4.34167 12.5 5.83333V7.08333"
                                                    stroke="#A0AEC0" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <input type="password" name="password" class="form-control rounded-end"
                                            placeholder="Please enter your password" aria-label="Password"
                                            aria-describedby="password-icon" id="password" required />
                                        <div class="fv-plugins-message-container invalid-feedback"
                                            id="password-invalid-feedback"></div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mb-3">
                                        <button type="submit" id="password-signin-btn"
                                            class="btn btn-primary w-100">{{ __('Sign In') }}</button>
                                    </div>
                                    <span>{{ __("Don't have an account?") }}
                                        <p type="button" title="Sign Up" class="pr-color" data-bs-toggle="modal"
                                            data-bs-target="#static_register_modal">
                                            Sign Up</i>
                                        </p>
                                    </span>
                                </div>
                            </form>
                        </div>

                        <!-- Phone Login Tab -->
                        <div class="tab-pane fade" id="phone-login" role="tabpanel" aria-labelledby="phone-tab">
                            <form id="phone_otp_form" method="POST" action="{{ route('verify-otp') }}">
                                <input type="hidden" name="otp" id="otp">
                                @csrf
                                <p class="my-3 text-center text-muted"><strong>{{ __('Sign In with Phone') }}</strong>
                                </p>
                                <div class="container mt-3">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text text-muted" id="phone-icon">
                                            +88
                                        </span>
                                        <input type="tel" name="phone" class="form-control rounded-end"
                                            placeholder="Enter your phone number" aria-label="Phone"
                                            aria-describedby="phone-icon" id="phone" maxlength="11" />
                                        <div class="fv-plugins-message-container invalid-feedback"
                                            id="phone-invalid-feedback"></div>
                                    </div>
                                    <!-- Countdown Timer -->
                                    <div id="otp-countdown-timer" class="text-center float-end d-none">
                                        <span id="otp-timer-text">{{ __('Time remaining: ') }}</span>
                                        <span id="otp-timer"></span>
                                    </div>
                                    <br>
                                    <div class="form-outline mb-2 d-none" id="enter-otp-container">
                                        <div class="d-flex justify-content-between">
                                            @for ($i = 1; $i <= 6; $i++)
                                                <input type="text" maxlength="1"
                                                    class="form-control otp-input rounded-0 border-gray-300 text-center fs-6 {{ $i == 1 ? 'rounded-start' : '' }} {{ $i == 6 ? 'rounded-end' : '' }}"
                                                    id="otpInput{{ $i }}" autocomplete="off" required>
                                            @endfor
                                        </div>
                                        <div class="fv-plugins-message-container invalid-feedback"
                                            id="otp-invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary w-100" id="request-otp-button"
                                            data-loading="">{{ __('Send code via SMS') }}</button>

                                        <button type="submit" class="btn btn-primary w-100 d-none"
                                            id="sign-in-submit-btn" data-loading="">{{ __('Sign In') }}</button>
                                    </div>
                                    <span>{{ __("Don't have an account?") }}
                                        <p type="button" title="Sign Up" class="pr-color" data-bs-toggle="modal"
                                            data-bs-target="#static_register_modal">
                                            Sign Up</i>
                                        </p>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('storefront::public.auth.register_modal')

@push('scripts')
    <script type="module">
        $(document).on("input", ".otp-input", function(e) {
            var selector = $(this);
            var otp = selector.val();
            if (!/\d/.test(otp)) {
                selector.val('');
                return;
            }
            if (otp && selector.next('.otp-input').length) {
                selector.next('.otp-input').focus();
            }
        });

        $('.otp-input').first().on('paste', function(e) {
            const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            const pastedData = clipboardData.getData('Text');
            if (!/^\d{6}$/.test(pastedData)) {
                return;
            }
            e.preventDefault();
            $('.otp-input').each(function(index) {
                $(this).val(pastedData[index] || '');
            });
            $('.otp-input').filter(function() {
                return !this.value;
            }).first().focus();
        });

        $(document).on("keydown", ".otp-input", function(e) {
            var selector = $(this);
            if (e.key === 'Backspace' && !selector.val() && selector.prev('.otp-input').length) {
                selector.prev('.otp-input').focus();
            }
        });

        $(document).off("focus", "#request-otp-button").on("focus", "#request-otp-button", function(e) {
            e.preventDefault();
            $('#phone-invalid-feedback').text('');
            $('#phone').removeClass('is-invalid');
        });

        $(document).off("click", "#request-otp-button").on("click", "#request-otp-button", function(e) {
            e.preventDefault();
            var selector = $(this);
            var phone = $("#phone").val().trim();
            var phone_pattern = /^01\d{9}$/;
            if (phone_pattern.test(phone)) {
                selector.addClass("btn-loading");
                requestOtp(phone, selector);
            } else {
                $('#phone-invalid-feedback').text('Please enter a valid Bangladeshi phone number.');
                $('#phone').addClass('is-invalid');
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
                    phone: phone
                }),
                success: function(response) {
                    selector.removeClass("btn-loading");
                    $("#phone").prop("readonly", true);
                    selector.addClass("d-none");
                    $("#enter-otp-container").removeClass("d-none");
                    $("#sign-in-submit-btn").removeClass("d-none");
                    startOtpCountdown(5);
                },
                error: function(xhr, status, error) {
                    selector.removeClass("btn-loading");
                    console.error("Error requesting OTP:", xhr.responseText || error);
                }
            });
        }

        $('#phone_otp_form').on('submit', function(e) {
            e.preventDefault();
            var submit_btn = $("#sign-in-submit-btn");
            var otp = '';
            for (var i = 1; i <= 6; i++) {
                otp += $('#otpInput' + i).val();
            }
            if (otp.length < 6) {
                toastr.error('Please enter a valid OTP.');
                return;
            }
            $("#otp").val(otp);
            submit_btn.addClass("btn-loading");
            $.ajax({
                url: $('#phone_otp_form').attr('action'),
                type: 'POST',
                data: $('#phone_otp_form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status == "success") {
                        window.location.href = response.redirectUrl || '/';
                    } else {
                        submit_btn.removeClass("btn-loading");
                        $('#otp-invalid-feedback').text(response.message);
                        $('.otp-input').addClass('is-invalid');
                    }
                },
                error: function(xhr) {
                    submit_btn.removeClass("btn-loading");
                        $('#otp-invalid-feedback').text(xhr.responseJSON?.message);
                        $('.otp-input').addClass('is-invalid');
                }
            });
        });

        $('#password_signin_form').on('submit', function(e) {
            e.preventDefault();
            var submit_btn = $("#password-signin-btn");
            submit_btn.addClass("btn-loading");
            $.ajax({
                url: $('#password_signin_form').attr('action'),
                type: 'POST',
                data: $('#password_signin_form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status == "success") {
                        window.location.href = response.redirectUrl || '/';
                    } else {
                        submit_btn.removeClass("btn-loading");
                        $('#password-invalid-feedback').text(response.message);
                        $('#password').addClass('is-invalid');
                        $('#email_phone').addClass('is-invalid');
                    }
                },
                error: function(xhr) {
                    submit_btn.removeClass("btn-loading");
                    $('#password-invalid-feedback').text(xhr.responseJSON?.message);
                    $('#password').addClass('is-invalid');
                    $('#email_phone').addClass('is-invalid');
                }
            });
        });

        // for countdown
        let countdownInterval;

        function startOtpCountdown(durationInMinutes) {
            $('#otp-countdown-timer').removeClass('d-none');
            let remainingTime = durationInMinutes * 60;

            function updateTimerDisplay() {
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;
                $('#otp-timer').text(`${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`);
                remainingTime--;
                if (remainingTime < 0) {
                    clearInterval(countdownInterval);
                    $('#otp-countdown-timer').addClass('d-none');
                    $("#phone").prop("readonly", false);
                    $("#request-otp-button").removeClass("d-none");
                    $("#enter-otp-container").addClass("d-none");
                    $("#sign-in-submit-btn").addClass("d-none");
                }
            }
            updateTimerDisplay();
            countdownInterval = setInterval(updateTimerDisplay, 1000);
        }
    </script>
@endpush
