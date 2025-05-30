@extends('storefront::public.auth.layout')

@section('title', trans('user::auth.reset_password'))

@section('content')
    <div class="bg-shape-layer"></div>

    <div class="bg-yellow-shape">
        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="125" viewBox="0 0 120 125" fill="none">
            <path
                d="M125.143 158.853C47.8652 155.266 13.5195 136.007 6.00644 126.826C-20.7831 33.0517 48.5091 3.20269 86.504 0C-0.433365 64.0537 221.74 163.337 125.143 158.853Z"
                fill="#FFAD00" />
        </svg>
    </div>

    <div class="bg-green-shape">
        <svg xmlns="http://www.w3.org/2000/svg" width="148" height="71" viewBox="0 0 148 71" fill="none">
            <g filter="url(#filter0_d_2414_189)">
                <path
                    d="M4.74654 103C0.139535 55.1211 14.6977 -30.3244 109.786 10.9251C204.875 52.1747 79.3801 89.4957 4.74654 103Z"
                    fill="#00BC65" />
            </g>
            <defs>
                <filter id="filter0_d_2414_189" x="0" y="0" width="148" height="111"
                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                    <feColorMatrix in="SourceAlpha" type="matrix"
                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                    <feOffset dy="4" />
                    <feGaussianBlur stdDeviation="2" />
                    <feComposite in2="hardAlpha" operator="out" />
                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2414_189" />
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2414_189" result="shape" />
                </filter>
            </defs>
        </svg>
    </div>

    <div class="auth-wrapper">
        <a href="{{ route('login') }}" class="back-to" title="{{ trans('user::auth.back_to_login') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M6.37992 3.95337L2.33325 8.00004L6.37992 12.0467" stroke="#0E1E3E" stroke-width="1.5"
                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M13.6668 8H2.44678" stroke="#0E1E3E" stroke-width="1.5" stroke-miterlimit="10"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>

        <div class="auth-left">
        </div>

        <div class="auth-right">
            <div class="bg-grid-shape"></div>

            <div class="bg-shape-layer"></div>

            <div class="bg-yellow-shape">
                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="125" viewBox="0 0 120 125" fill="none">
                    <path
                        d="M125.143 158.853C47.8652 155.266 13.5195 136.007 6.00644 126.826C-20.7831 33.0517 48.5091 3.20269 86.504 0C-0.433365 64.0537 221.74 163.337 125.143 158.853Z"
                        fill="#FFAD00" />
                </svg>
            </div>

            <div class="bg-green-shape">
                <svg xmlns="http://www.w3.org/2000/svg" width="148" height="71" viewBox="0 0 148 71" fill="none">
                    <g filter="url(#filter0_d_2414_189)">
                        <path
                            d="M4.74654 103C0.139535 55.1211 14.6977 -30.3244 109.786 10.9251C204.875 52.1747 79.3801 89.4957 4.74654 103Z"
                            fill="#00BC65" />
                    </g>
                    <defs>
                        <filter id="filter0_d_2414_189" x="0" y="0" width="148" height="111"
                            filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix"
                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2414_189" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2414_189" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </div>

            <div class="auth-form">
                <div class="auth-form-header">
                    <a href="{{ route('login') }}" class="back-to" title="{{ trans('user::auth.back_to_login') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6.37992 3.95337L2.33325 8.00004L6.37992 12.0467" stroke="#0E1E3E" stroke-width="1.5"
                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M13.6668 8H2.44678" stroke="#0E1E3E" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>

                    <div class="auth-form-header-content">
                        <div class="auth-form-header-top">
                            <a href="{{ route('home') }}" class="auth-form-header-logo">
                                @if (is_null($logo))
                                    <p>{{ setting('store_name') }}</p>
                                @else
                                    <img src="{{ $logo }}" alt="Logo">
                                @endif
                            </a>

                            @include('storefront::public.auth.partials.language_picker')
                        </div>

                        <h2>{{ trans('user::auth.create_new_password') }}</h2>

                        <p>{{ trans('user::auth.this_password_should_be_different') }}</p>
                    </div>
                </div>

                @include('user::admin.partials.notification')

                <form class="auth-form-body" method="POST" action="{{ route('reset.complete.post', [$user->email, $code]) }}">
                    @csrf

                    <div>
                        <div class="auth-form-body-top">
                            <a href="{{ route('home') }}" class="auth-form-header-logo">
                                @if (is_null($logo))
                                    <p>{{ setting('store_name') }}</p>
                                @else
                                    <img src="{{ $logo }}" alt="Logo">
                                @endif
                            </a>

                            @include('storefront::public.auth.partials.language_picker')
                        </div>

                        <div class="form-group">
                            <label
                                for="new_password"
                                class="input-label"
                            >
                                {{ trans('user::auth.password') }} <span>*</span>
                            </label>

                            <div
                                x-data="{ showPassword: false }"
                                class="input-group"
                            >
                                <div
                                    class="password-toggle-icon"
                                    :data-tooltip="showPassword ? '{{ trans('user::auth.hide_password') }}' : '{{ trans('user::auth.show_password') }}'"
                                    @click="showPassword = !showPassword"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none">
                                        <path
                                            d="M12.9833 9.99993C12.9833 11.6499 11.6499 12.9833 9.99993 12.9833C8.34993 12.9833 7.0166 11.6499 7.0166 9.99993C7.0166 8.34993 8.34993 7.0166 9.99993 7.0166C11.6499 7.0166 12.9833 8.34993 12.9833 9.99993Z"
                                            x-bind:stroke="showPassword ? '#0E1E3E' : '#A0AEC0'" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M9.99987 16.8918C12.9415 16.8918 15.6832 15.1584 17.5915 12.1584C18.3415 10.9834 18.3415 9.00843 17.5915 7.83343C15.6832 4.83343 12.9415 3.1001 9.99987 3.1001C7.0582 3.1001 4.31654 4.83343 2.4082 7.83343C1.6582 9.00843 1.6582 10.9834 2.4082 12.1584C4.31654 15.1584 7.0582 16.8918 9.99987 16.8918Z"
                                            x-bind:stroke="showPassword ? '#0E1E3E' : '#A0AEC0'" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>

                                    <div class="show-password" :class="showPassword ? 'animate-show' : ''"></div>
                                </div>

                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    name="new_password"
                                    placeholder="{{ trans('user::attributes.users.new_password') }}"
                                    class="form-control"
                                    autofocus
                                >

                                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 20 20" fill="none">
                                    <path
                                        d="M9.18301 16.25H6.24967C5.73301 16.25 5.27467 16.2333 4.86634 16.175C2.67467 15.9333 2.08301 14.9 2.08301 12.0833V7.91667C2.08301 5.1 2.67467 4.06667 4.86634 3.825C5.27467 3.76667 5.73301 3.75 6.24967 3.75H9.13301"
                                        stroke="#A0AEC0" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M12.5166 3.75H13.7499C14.2666 3.75 14.7249 3.76667 15.1333 3.825C17.3249 4.06667 17.9166 5.1 17.9166 7.91667V12.0833C17.9166 14.9 17.3249 15.9333 15.1333 16.175C14.7249 16.2333 14.2666 16.25 13.7499 16.25H12.5166"
                                        stroke="#A0AEC0" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M12.5 1.6665V18.3332" stroke="#A0AEC0" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M9.2451 10.0002H9.25258" stroke="#A0AEC0" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5.91209 10.0002H5.91957" stroke="#A0AEC0" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>

                            {!! $errors->first('new_password', '<span class="help-block text-red">:message</span>') !!}
                        </div>

                        <div class="form-group">
                            <label
                                for="new_password_confirmation"
                                class="input-label"
                            >
                                {{ trans('user::auth.confirm_password') }} <span>*</span>
                            </label>

                            <div
                                x-data="{ showPassword: false }"
                                class="input-group"
                            >
                                <div
                                    class="password-toggle-icon"
                                    :data-tooltip="showPassword ? '{{ trans('user::auth.hide_password') }}' : '{{ trans('user::auth.show_password') }}'"
                                    @click="showPassword = !showPassword"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none">
                                        <path
                                            d="M12.9833 9.99993C12.9833 11.6499 11.6499 12.9833 9.99993 12.9833C8.34993 12.9833 7.0166 11.6499 7.0166 9.99993C7.0166 8.34993 8.34993 7.0166 9.99993 7.0166C11.6499 7.0166 12.9833 8.34993 12.9833 9.99993Z"
                                            x-bind:stroke="showPassword ? '#0E1E3E' : '#A0AEC0'" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M9.99987 16.8918C12.9415 16.8918 15.6832 15.1584 17.5915 12.1584C18.3415 10.9834 18.3415 9.00843 17.5915 7.83343C15.6832 4.83343 12.9415 3.1001 9.99987 3.1001C7.0582 3.1001 4.31654 4.83343 2.4082 7.83343C1.6582 9.00843 1.6582 10.9834 2.4082 12.1584C4.31654 15.1584 7.0582 16.8918 9.99987 16.8918Z"
                                            x-bind:stroke="showPassword ? '#0E1E3E' : '#A0AEC0'" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>

                                    <div class="show-password" :class="showPassword ? 'animate-show' : ''"></div>
                                </div>

                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    name="new_password_confirmation"
                                    placeholder="{{ trans('user::attributes.users.confirm_new_password') }}"
                                    class="form-control"
                                >

                                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 20 20" fill="none">
                                    <path
                                        d="M9.18301 16.25H6.24967C5.73301 16.25 5.27467 16.2333 4.86634 16.175C2.67467 15.9333 2.08301 14.9 2.08301 12.0833V7.91667C2.08301 5.1 2.67467 4.06667 4.86634 3.825C5.27467 3.76667 5.73301 3.75 6.24967 3.75H9.13301"
                                        stroke="#A0AEC0" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M12.5166 3.75H13.7499C14.2666 3.75 14.7249 3.76667 15.1333 3.825C17.3249 4.06667 17.9166 5.1 17.9166 7.91667V12.0833C17.9166 14.9 17.3249 15.9333 15.1333 16.175C14.7249 16.2333 14.2666 16.25 13.7499 16.25H12.5166"
                                        stroke="#A0AEC0" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M12.5 1.6665V18.3332" stroke="#A0AEC0" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M9.2451 10.0002H9.25258" stroke="#A0AEC0" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5.91209 10.0002H5.91957" stroke="#A0AEC0" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>

                            {!! $errors->first('new_password_confirmation', '<span class="help-block text-red">:message</span>') !!}
                        </div>
                    </div>

                    <button
                        type="submit"
                        x-data="{ formSubmitting: false }"
                        :class="formSubmitting ? 'btn-loading' : ''"
                        class="btn btn-primary"
                        :disabled="formSubmitting"
                        @click="formSubmitting = true; $el.parentElement.submit()"
                    >
                        {{ trans('user::auth.reset_password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
