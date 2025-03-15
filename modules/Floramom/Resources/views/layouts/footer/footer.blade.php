{{-- @include('storefront::public.layouts.footer') --}}
<footer class="bg-white border-t border-gray-200 py-10">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
            <div>
                <p class="text-lg font-semibold mb-4">{{ trans('storefront::layouts.contact_us') }}</p>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center space-x-2">
                        <i class="fa-solid fa-phone"></i>
                        <span><a href="tel:+8801907888076" class="hover:text-pink-600 font-bold">+8801907888076</a></span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fas fa-envelope"></i>
                        <span><a href="mailto:hello@htmlpointer.com"
                                class="hover:text-pink-600 font-bold">hello@htmlpointer.com</a></span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><a href="#" class="hover:text-pink-600 font-bold">Banasree , Dhaka - 1219, Bangladesh</a></span>
                    </li>
                </ul>

                <div class="flex space-x-4 mt-4">
                    @if (social_links()->isNotEmpty())
                        @foreach (social_links() as $icon => $socialLink)
                            @if ($icon === 'lab la-facebook')
                                <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                    class="text-gray-500 hover:text-blue-800"><i class="fab fa-facebook"></i></a>
                            @elseif($icon === 'lab la-twitter')
                                <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                    class="text-gray-500 hover:text-dark-800"><i class="fa-brands fa-x-twitter"></i></a>
                            @elseif($icon === 'lab la-instagram')
                                <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                    class="text-gray-500 hover:text-pink-800"><i class="fab fa-instagram"></i></a>
                            @elseif($icon === 'lab la-youtube')
                                <a href="{{ $socialLink }}" title="{{ social_link_name($icon) }}"
                                    class="text-gray-500 hover:text-red-800"><i class="fab fa-youtube"></i></a>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <div>
                <p class="text-lg font-semibold mb-4">{{ trans('storefront::layouts.my_account') }}</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('account.dashboard.index') }}"
                            class="hover:text-pink-600 font-bold ">{{ trans('storefront::account.pages.dashboard') }}</a>
                    </li>
                    <li><a href="{{ route('account.orders.index') }}"
                            class="hover:text-pink-600 font-bold ">{{ trans('storefront::account.pages.my_orders') }}</a>
                    </li>
                    <li><a href="{{ route('account.reviews.index') }}"
                            class="hover:text-pink-600 font-bold ">{{ trans('storefront::account.pages.my_reviews') }}</a>
                    </li>
                    <li><a href="{{ route('account.profile.edit') }}"
                            class="hover:text-pink-600 font-bold ">{{ trans('storefront::account.pages.my_profile') }}</a>
                    </li>
                </ul>
            </div>

            @if ($footerMenuOne->isNotEmpty())
                <div>
                    <p class="text-lg font-semibold mb-4">{{ setting('storefront_footer_menu_one_title') }}</p>
                    <ul class="space-y-2 text-sm">
                        @foreach ($footerMenuOne as $menuItem)
                            <li><a href="{{ $menuItem->url() }}" target="{{ $menuItem->target }}"
                                    class="hover:text-pink-600 font-bold ">{{ $menuItem->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($footerMenuTwo->isNotEmpty())
            @endif
            <div>
                <p class="text-lg font-semibold mb-4">Information</p>
                <ul class="space-y-2 text-sm">
                    @foreach ($footerMenuTwo as $menuItem)
                        <li><a href="{{ $menuItem->url() }}" target="{{ $menuItem->target }}"
                                class="hover:text-pink-600 font-bold ">{{ $menuItem->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            @if ($footerTags->isNotEmpty())
                <div>
                    <p class="text-lg font-semibold mb-4">Tags</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($footerTags as $footerTag)
                            <a href="{{ $footerTag->url() }}" class="bg-gray-100 text-gray-600 hover:bg-pink-600 hover:text-white text-sm px-3 py-1 rounded-lg border"><i class="fa fa-tag" aria-hidden="true"></i>{{ $footerTag->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 mt-8 pt-6">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-500 font-bold">
                    Copyright © <span class="font-semibold text-pink-500">Floramom</span> 2024. All rights reserved.
                </p>
                <div class="flex items-center">
                    @if ($acceptedPaymentMethodsImage->exists)
                        <img src="{{ $acceptedPaymentMethodsImage->path }}" alt="Accepted payment methods"
                            class="w-15" loading="lazy">
                    @endif
                </div>
            </div>
        </div>

    </div>
</footer>
