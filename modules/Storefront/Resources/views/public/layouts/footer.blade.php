<footer class="footer-wrap">
    <div class="container" style="background: #FFF9FB">
        <div class="footer mt-3">
            <div class="footer-top footer-top rounded px-2 ">
                <div class="row">
                    <div class="col-lg-3 col-md-8">
                        <div class="contact-us">
                            <p class="title"><strong>{{ trans('storefront::layouts.contact_us') }}</strong></p>

                            <ul class="list-inline contact-info" style="font-weight: 500;">
                                @if (setting('store_phone') && !setting('store_phone_hide'))
                                    <li>
                                        <i class="las la-phone"></i>

                                        <a href="tel:+1 206 555 0100" class="store-phone">
                                            <span>{{ substr(setting('store_phone'), 0, strlen(setting('store_phone')) / 2) }}</span>
                                            <span class="d-none">JUNK LOAD</span>
                                            <span>{{ substr(setting('store_phone'), strlen(setting('store_phone')) / 2) }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (setting('store_email') && !setting('store_email_hide'))
                                    <li>
                                        <i class="las la-envelope"></i>

                                        <a href="mailto:user@email.com" class="store-email">
                                            <span>{{ substr(setting('store_email'), 0, strlen(setting('store_email')) / 2) }}</span>
                                            <span class="d-none">JUNK LOAD</span>
                                            <span>{{ substr(setting('store_email'), strlen(setting('store_email')) / 2) }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (setting('storefront_address'))
                                    <li>
                                        <i class="las la-map"></i>

                                        <span>
                                            <pre style="font-weight: 500;">{{ setting('storefront_address') }}</pre>
                                        </span>
                                    </li>
                                @endif
                            </ul>

                            {{-- @if (social_links()->isNotEmpty())
                                <ul class="list-inline social-links">
                                    @foreach (social_links() as $icon => $socialLink)
                                        <li>
                                            <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                                target="_blank">
                                                @if ($icon === 'lab la-twitter')
                                                    <svg class="twitter-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 30 30" width="30px" height="30px">
                                                        <path
                                                            d="M26.37,26l-8.795-12.822l0.015,0.012L25.52,4h-2.65l-6.46,7.48L11.28,4H4.33l8.211,11.971L12.54,15.97L3.88,26h2.65 l7.182-8.322L19.42,26H26.37z M10.23,6l12.34,18h-2.1L8.12,6H10.23z" />
                                                    </svg>
                                                @else
                                                    <i class="{{ $icon }}"></i>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif --}}
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-5">
                        <div class="footer-links" style="font-weight: 500;">
                            <p class="title"><strong>{{ trans('storefront::layouts.my_account') }}</strong></p>

                            <ul class="list-inline">
                                <li>
                                    <a href="{{ route('account.dashboard.index') }}">
                                        {{ trans('storefront::account.pages.dashboard') }}
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('account.orders.index') }}">
                                        {{ trans('storefront::account.pages.my_orders') }}
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('account.reviews.index') }}">
                                        {{ trans('storefront::account.pages.my_reviews') }}
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('account.profile.edit') }}">
                                        {{ trans('storefront::account.pages.my_profile') }}
                                    </a>
                                </li>

                                {{-- @auth
                                    <li>
                                        <a href="{{ route('logout') }}">
                                            {{ trans('storefront::account.pages.logout') }}
                                        </a>
                                    </li>
                                @endauth --}}
                            </ul>
                        </div>
                    </div>

                    @if ($footerMenuOne->isNotEmpty())
                        <div class="col-lg-3 col-md-5">
                            <div class="footer-links">
                                <p class="title"><strong>{{ setting('storefront_footer_menu_one_title') }}</strong>
                                </p>

                                <ul class="list-inline" style="font-weight: 500;">
                                    @foreach ($footerMenuOne as $menuItem)
                                        <li>
                                            <i class="{{ $menuItem->icon }} pr-color"></i>
                                            <a href="{{ $menuItem->url() }}" target="{{ $menuItem->target }}">
                                                {{ $menuItem->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if ($footerMenuTwo->isNotEmpty())
                        <div class="col-lg-3 col-md-5">
                            <div class="footer-links">
                                <p class="title"><strong>{{ setting('storefront_footer_menu_two_title') }}</strong>
                                </p>

                                <ul class="list-inline" style="font-weight: 500;">
                                    @foreach ($footerMenuTwo as $menuItem)
                                        <li>
                                            <i class="{{ $menuItem->icon }} pr-color"></i>
                                            <a href="{{ $menuItem->url() }}" target="{{ $menuItem->target }}">
                                                {{ $menuItem->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="col-lg-3 col-md-5">
                        <div class="footer-links text-center">
                            {{-- <p class="title">{{ trans('storefront::layouts.we_accept') }}</p>
                            <ul class="list-inline d-flex justify-content-center gap-2">
                                <li>
                                    <img class="" style="width: 40px;" src="{{ asset('build/assets/bkash.png') }}" alt="Bkash">
                                </li>
                                <li>
                                    <img class=""  style="width: 40px;" src="{{ asset('build/assets/nagad.png') }}" alt="Nagad">
                                </li>
                            </ul> --}}
                            <p class="title"><strong>{{ trans('storefront::layouts.happily_accept') }}</strong> </p>
                            <ul class="list-inline">
                                <li>
                                    <img class="" style="width: 80px;" src="{{ asset('build/assets/cod.png') }}"
                                        alt="COD">
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-5">
                        <div class="footer-links text-center">
                            <p class="title"><strong>{{ trans('storefront::layouts.download_app') }}</strong></p>
                            <ul class="list-inline">
                                <li>
                                    <img class="" style="width: 220px;"
                                        src="{{ asset('build/assets/gplayastore.png') }}" alt="gPlay and aStore">
                                </li>
                            </ul>
                            {{-- <p class="title mt-2">{{ trans('storefront::layouts.member_of') }}</p>
                            <ul class="list-inline d-flex justify-content-center gap-2">
                                <li>
                                    <img class="" style="width: 80px;" src="{{ asset('build/assets/ecab.png') }}" alt="eCab">
                                </li>
                                <li>
                                    <img class=""  style="width: 80px;" src="{{ asset('build/assets/dbid.png') }}" alt="DBID">
                                </li>
                            </ul> --}}
                        </div>
                    </div>

                    {{-- @if ($footerTags->isNotEmpty())
                        <div class="col-lg-4 col-md-7">
                            <div class="footer-links footer-tags">
                                <p class="title">{{ trans('storefront::layouts.tags') }}</p>

                                <ul class="list-inline">
                                    @foreach ($footerTags as $footerTag)
                                        <li>
                                            <a href="{{ $footerTag->url() }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M4.16989 15.3L8.69989 19.83C10.5599 21.69 13.5799 21.69 15.4499 19.83L19.8399 15.44C21.6999 13.58 21.6999 10.56 19.8399 8.69005L15.2999 4.17005C14.3499 3.22005 13.0399 2.71005 11.6999 2.78005L6.69989 3.02005C4.69989 3.11005 3.10989 4.70005 3.00989 6.69005L2.76989 11.69C2.70989 13.04 3.21989 14.35 4.16989 15.3Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M9.5 12C10.8807 12 12 10.8807 12 9.5C12 8.11929 10.8807 7 9.5 7C8.11929 7 7 8.11929 7 9.5C7 10.8807 8.11929 12 9.5 12Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round"/>
                                                </svg>

                                                {{ $footerTag->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif --}}
                </div>

                <div class="mt-1">
                    <div class="row align-items-center">
                        <div class="col-md-9 col-sm-18">
                            <div class="footer-text">
                                @if (social_links()->isNotEmpty())
                                    <ul class="list-inline social-links">
                                        @foreach (social_links() as $icon => $socialLink)
                                            <li>
                                                <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                                    target="_blank">
                                                    @if ($icon === 'lab la-twitter')
                                                        <svg class="twitter-icon" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 30 30" width="30px" height="30px">
                                                            <path
                                                                d="M26.37,26l-8.795-12.822l0.015,0.012L25.52,4h-2.65l-6.46,7.48L11.28,4H4.33l8.211,11.971L12.54,15.97L3.88,26h2.65 l7.182-8.322L19.42,26H26.37z M10.23,6l12.34,18h-2.1L8.12,6H10.23z" />
                                                        </svg>
                                                    @else
                                                        <i class="{{ $icon }}"></i>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>



                        @if ($acceptedPaymentMethodsImage->exists)
                            <div class="col-md-9 col-sm-18">
                                <div class="footer-payment">
                                    <img src="{{ $acceptedPaymentMethodsImage->path }}" alt="Accepted payment methods"
                                        loading="lazy">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


        </div>
    </div>
    <div class="container">
        <div class="footer-bottom mt-0">
            <div class="row align-items-center justify-content-center">
                <div class="col-12 text-center">
                    <div class="footer-text ">
                        {!! $copyrightText !!}
                    </div>
                </div>

                {{-- @if ($acceptedPaymentMethodsImage->exists)
                    <div class="col-md-9 col-sm-18">
                        <div class="footer-payment">
                            <img src="{{ $acceptedPaymentMethodsImage->path }}" alt="Accepted payment methods"
                                loading="lazy">
                        </div>
                    </div>
                @endif --}}
            </div>
        </div>
    </div>

    @include('storefront::public.layouts.chat')

</footer>

@push('scripts')
    <script type="module">
        $('.store-phone').attr('href', `tel:{{ setting('store_phone') }}`);
        $('.store-email').attr('href', `mailto:{{ setting('store_email') }}`);
    </script>
@endpush
