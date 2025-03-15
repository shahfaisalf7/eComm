<section class="top-nav-wrap">
    <div class="container">
        <div class="top-nav">
            <div class="d-flex justify-content-between">
                <div class="top-nav-left d-none d-lg-block">
                    <span>{!! setting('storefront_welcome_text') !!}</span>
                </div>


                @if (setting('storefront_discount_text'))
                    <div class="top-nav-left d-none d-lg-block">
                        <span>{!! setting('storefront_discount_text') !!}</span>
                    </div>
                @endif


                <div class="top-nav-right d-none d-lg-block">
                    <a href="/membership-facilities" target="_blank" rel="noopener noreferrer"><span style="font-weight: 700"> <span class="pr-color">Floramom</span> <span style="color: black">Membership</span> </span></a>

                </div>

                <div class="top-nav-right">
                    <ul class="list-inline top-nav-right-list">
                        <li>
                            <a href="{{ route('contact.create') }}">
                                <i class="las la-envelope la-lg"></i>

                                {{ trans('storefront::layouts.contact') }}
                            </a>
                        </li>

                        <li class="d-flex">
                            @if (social_links()->isNotEmpty())
                                @foreach (social_links() as $icon => $socialLink)
                                    @if ($icon === 'lab la-facebook')
                                        <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                            class="pr-color"><i class="lab la-facebook la-lg"></i></a>
                                    @elseif($icon === 'lab la-twitter')
                                        <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                            class="pr-color">
                                            {{-- <i class="lab la-twitter la-lg"></i> --}}
                                            <svg class="twitter-icon" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 30 30" width="30px" height="30px">
                                                <path
                                                    d="M26.37,26l-8.795-12.822l0.015,0.012L25.52,4h-2.65l-6.46,7.48L11.28,4H4.33l8.211,11.971L12.54,15.97L3.88,26h2.65 l7.182-8.322L19.42,26H26.37z M10.23,6l12.34,18h-2.1L8.12,6H10.23z" />
                                            </svg>
                                        </a>
                                    @elseif($icon === 'lab la-instagram')
                                        <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                            class="pr-color"><i class="lab la-instagram la-lg"></i></a>
                                    @elseif($icon === 'lab la-youtube')
                                        <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                            class="pr-color"><i class="lab la-youtube la-lg"></i></a>
                                    @endif
                                @endforeach
                            @endif
                        </li>

                        @if (is_multilingual())
                            <div
                                x-data="{
                                    open: false,
                                    selected: '{{ locale_get_display_language(locale()) }}'
                                }"
                                class="dropdown custom-dropdown"
                                :class="{ active: open }"
                                @click.away="open = false"
                            >
                                <div
                                    class="btn btn-secondary dropdown-toggle"
                                    :class="{ active: open }"
                                    @click="open = !open"
                                >
                                    <i class="las la-language la-lg"></i>

                                    {{ locale_get_display_language(locale()) }}

                                    <i class="las la-angle-down"></i>
                                </div>

                                <ul
                                    x-cloak
                                    x-show="open"
                                    x-transition
                                    class="dropdown-menu"
                                    :class="{ active: open }"
                                >
                                    <div class="dropdown-menu-scroll">
                                        @foreach (supported_locales() as $locale => $language)
                                            @if (locale_get_display_language(locale()) !== $language['name'])
                                                <li
                                                    class="dropdown-item"
                                                    @click="
                                                        open = false;
                                                        selected = '{{ $locale }}';
                                                        location = '{{ localized_url($locale) }}'
                                                    "
                                                >
                                                    {{ $language['name'] }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </div>
                                </ul>
                            </div>
                        @endif

                        {{-- @if (is_multi_currency())
                            <div
                                x-data="{
                                    open: false,
                                    selected: '{{ currency() }}'
                                }"
                                class="dropdown custom-dropdown"
                                :class="{ active: open }"
                                @click.away="open = false"
                            >
                                <div
                                    class="btn btn-secondary dropdown-toggle"
                                    :class="{ active: open }"
                                    @click="open = !open"
                                >
                                    <i class="las la-money-bill"></i>

                                    {{ currency() }}

                                    <i class="las la-angle-down"></i>
                                </div>

                                <ul
                                    x-cloak
                                    x-show="open"
                                    x-transition
                                    class="dropdown-menu"
                                    :class="{ active: open }"
                                >
                                    <div class="dropdown-menu-scroll">
                                        @foreach (setting('supported_currencies') as $currency)
                                            @if (currency() !== $currency)
                                                <li
                                                    class="dropdown-item"
                                                    @click="
                                                        open = false;
                                                        selected = '{{ $currency }}';
                                                        location = '{{ route('current_currency.store', ['code' => $currency]) }}'
                                                    "
                                                >
                                                    {{ $currency }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </div>
                                </ul>
                            </div>
                        @endif

                        @auth
                            <li class="top-nav-account">
                                <a href="{{ route('account.dashboard.index') }}">
                                    <i class="las la-user"></i>

                                    {{ trans('storefront::layouts.account') }}
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('login') }}">
                                    <i class="las la-sign-in-alt"></i>

                                    {{ trans('storefront::layouts.login_register') }}
                                </a>
                            </li>
                        @endauth --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
