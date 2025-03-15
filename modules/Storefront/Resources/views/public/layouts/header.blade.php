<header x-ref="header" x-data="Header" class="header-wrap">
    <div class="header-wrap-inner"
        :class="{
            sticky: isStickyHeader,
            show: isShowingStickyHeader
        }">
        <div class="container">
            <div class="d-flex flex-nowrap justify-content-between position-relative">
                <div class="header-column-left align-items-center">
                    <div class="sidebar-menu-icon-wrap" @click="$store.layout.openSidebarMenu()">
                        <div class="sidebar-menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="150px" height="150px">
                                <path
                                    d="M 3 9 A 1.0001 1.0001 0 1 0 3 11 L 47 11 A 1.0001 1.0001 0 1 0 47 9 L 3 9 z M 3 24 A 1.0001 1.0001 0 1 0 3 26 L 47 26 A 1.0001 1.0001 0 1 0 47 24 L 3 24 z M 3 39 A 1.0001 1.0001 0 1 0 3 41 L 47 41 A 1.0001 1.0001 0 1 0 47 39 L 3 39 z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <a href="{{ route('home') }}" class="header-logo">
                        @if (is_null($logo))
                            <p>{{ setting('store_name') }}</p>
                        @else
                            <img src="{{ $logo }}" alt="Logo">
                        @endif
                    </a>
                </div>

                @include('storefront::public.layouts.header.header_search')




                <div class="header-column-right d-flex">
                    {{-- <div class="header-column-right-item header-localization">
                        <div class="icon-wrap" @click="$store.layout.openLocalizationMenu()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#292D32" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M7.99998 3H8.99998C7.04998 8.84 7.04998 15.16 8.99998 21H7.99998" stroke="#292D32" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M15 3C16.95 8.84 16.95 15.16 15 21" stroke="#292D32" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M3 16V15C8.84 16.95 15.16 16.95 21 15V16" stroke="#292D32" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M3 8.99998C8.84 7.04998 15.16 7.04998 21 8.99998" stroke="#292D32" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                    </div> --}}



                    <div class="header-column-right-item header-localization">
                        @auth
                            <a title="Dashboard" class="sm-login-icon" href="{{ route('account.dashboard.index') }}">
                                <i class="las la-user"></i>
                            </a>
                        @else
                            {{-- <a title="Sign Up/In" class="sm-login-icon"  href="{{ route('login') }}" >
                                <i class="las la-user-circle la-2x pr-color" ></i>
                            </a> --}}
                            <a type="button" title="Sign Up/In" class="sm-login-icon" data-bs-toggle="modal"
                                data-bs-target="#static_login_modal">
                                <i class="las la-user-circle la-2x pr-color"></i>
                            </a>
                        @endauth
                    </div>




                    <a href="#flash-sale-section" class="pr-color header-compare header-column-right-item">
                        <span class="flash-text">DEALS</span>
                        {{-- <span class="flash-icon-container pr-bg">
                                <i class="las la-fire flash-icon "></i>
                            </span> --}}
                    </a>



                    <div class="pr-bg skinType header-compare mx-2">
                        <a href="#" class=" text-light">
                            Explore Skin
                        </a>
                    </div>


                    <div class=" header-compare mx-2">
                        @auth
                            <a class="pr-color" href="{{ route('account.dashboard.index') }}">
                                <i class="las la-user"></i>

                                {{ trans('storefront::layouts.account') }}
                            </a>
                        @else
                            <div>

                                {{-- <a href="{{ route('login') }}"><i class="las la-user-circle la-2x pr-color" ></i></a>
                            <a href="{{ route('login') }}" style="text-align: center; display: inline-block;">
                                {!! trans('storefront::layouts.login_register_modify') !!}
                            </a> --}}
                                <a type="button" style="text-align: center; display: inline-block;" data-bs-toggle="modal"
                                    data-bs-target="#static_login_modal">
                                    <i class="las la-user-circle la-2x pr-color"></i>
                                </a>
                                <a type="button" style="text-align: center; display: inline-block;" data-bs-toggle="modal"
                                    data-bs-target="#static_login_modal">
                                    {!! trans('storefront::layouts.login_register_modify') !!}
                                </a>
                            </div>


                        @endauth
                    </div>

                    <a href="{{ route('compare.index') }}" class="header-column-right-item header-compare"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip"
                        data-bs-title="Compare">
                        <div class="icon-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M3.58008 5.15991H17.4201C19.0801 5.15991 20.4201 6.49991 20.4201 8.15991V11.4799"
                                    stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M6.74008 2L3.58008 5.15997L6.74008 8.32001" stroke="#292D32" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M20.4201 18.84H6.58008C4.92008 18.84 3.58008 17.5 3.58008 15.84V12.52"
                                    stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M17.26 21.9999L20.42 18.84L17.26 15.6799" stroke="#292D32" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <div class="count" style="background-color: #e33b80 !important;"
                                x-text="$store.state.compareCount">{{ count($compareList) }}</div>
                        </div>
                    </a>

                    <a href="{{ route('account.wishlist.index') }}" class="header-column-right-item header-wishlist"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip"
                        data-bs-title="Wishlist">
                        <div class="icon-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M12.62 20.81C12.28 20.93 11.72 20.93 11.38 20.81C8.48 19.82 2 15.69 2 8.68998C2 5.59998 4.49 3.09998 7.56 3.09998C9.38 3.09998 10.99 3.97998 12 5.33998C13.01 3.97998 14.63 3.09998 16.44 3.09998C19.51 3.09998 22 5.59998 22 8.68998C22 15.69 15.52 19.82 12.62 20.81Z"
                                    stroke="#292D32" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>

                            <div class="count" style="background-color: #e33b80 !important;"
                                x-text="$store.state.wishlistCount">{{ count($wishlist) }}</div>
                        </div>
                    </a>

                    <a href="{{ route('cart.index') }}" class="header-column-right-item header-cart"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip"
                        data-bs-title="Cart" @click="$store.layout.openSidebarCart($event)">
                        <div class="icon-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 16 16" fill="none">
                                <g clip-path="url(#clip0_2055_61)">
                                    <path
                                        d="M1.3335 1.33325H2.20427C2.36828 1.33325 2.45029 1.33325 2.51628 1.36341C2.57444 1.38999 2.62373 1.43274 2.65826 1.48655C2.69745 1.54761 2.70905 1.6288 2.73225 1.79116L3.04778 3.99992M3.04778 3.99992L3.74904 9.15419C3.83803 9.80827 3.88253 10.1353 4.0389 10.3815C4.17668 10.5984 4.37422 10.7709 4.60773 10.8782C4.87274 10.9999 5.20279 10.9999 5.8629 10.9999H11.5682C12.1965 10.9999 12.5107 10.9999 12.7675 10.8869C12.9939 10.7872 13.1881 10.6265 13.3283 10.4227C13.4875 10.1917 13.5462 9.88303 13.6638 9.26576L14.5462 4.63305C14.5876 4.41579 14.6083 4.30716 14.5783 4.22225C14.552 4.14777 14.5001 4.08504 14.4319 4.04526C14.3541 3.99992 14.2435 3.99992 14.0223 3.99992H3.04778ZM6.66683 13.9999C6.66683 14.3681 6.36835 14.6666 6.00016 14.6666C5.63197 14.6666 5.3335 14.3681 5.3335 13.9999C5.3335 13.6317 5.63197 13.3333 6.00016 13.3333C6.36835 13.3333 6.66683 13.6317 6.66683 13.9999ZM12.0002 13.9999C12.0002 14.3681 11.7017 14.6666 11.3335 14.6666C10.9653 14.6666 10.6668 14.3681 10.6668 13.9999C10.6668 13.6317 10.9653 13.3333 11.3335 13.3333C11.7017 13.3333 12.0002 13.6317 12.0002 13.9999Z"
                                        stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                </g>
                                <defs>
                                    <clipPath id="clip0_2055_61">
                                        <rect width="16" height="16" fill="white"></rect>
                                    </clipPath>
                                </defs>
                            </svg>

                            <div class="count" style="background-color: #e33b80 !important;"
                                x-text="$store.state.cartQuantity">
                                {{ $cart->toArray()['quantity'] }}
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<style>
    .special-price {
        color: #d63384 !important;
    }
    .title{
        font-weight: bold;
    }
    body strong
    {
        font-weight: bold !important;
    }
    .custom-page-content
    h6,
    .h6,
    h5,
    .h5,
    h4,
    .h4,
    h3,
    .h3,
    h2,
    .h2,
    h1,
    .h1 {
        /*line-height: normal !important; !* Force override *!*/
        /*margin: 0px 7px 0px 7px;*/
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .custom-page-content
    {
        padding-left: 15%;
        padding-right: 15%;
        text-align: justify;
    }
    .tab-content .custom-page-content
    {
        padding: 2%;
        text-align: justify;
    }
    .product-details-tab .reviews {
        padding-top: 0px;
        margin-top: 0%;
    }
    .ltr .custom-page-content ul, .ltr .custom-page-content ol {
        padding-left: 22px;
    }
    .product-badge .badge.badge-danger {
        background: rgb(222, 0, 92);
    }
    .footer {
        padding-top: 70px;
    }
</style>
@include('storefront::public.auth.login_modal')

<script>
    //  tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
            tooltipTriggerEl));
    });
</script>
