<!-- Mobile Bottom Navigation Menu -->
<nav class="mobile-bottom-nav">
    <div class="nav-box">
        <ul class="nav-container">
            <li class="nav__item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="nav__item-link">
                    <div class="nav__item-icon">
                        <ion-icon name="home-outline"></ion-icon>
                    </div>
                    <span class="nav__item-text">Home</span>
                </a>
            </li>
            <li class="nav__item {{ request()->is('products') ? 'active' : '' }}">
                <a href="{{ url('/products') }}" class="nav__item-link">
                    <div class="nav__item-icon">
                        <ion-icon name="bag-handle-outline"></ion-icon>
                    </div>
                    <span class="nav__item-text">Shop</span>
                </a>
            </li>
            <li class="nav__item {{ request()->is('categories') ? 'active' : '' }}">
                <a href="{{ url('/categories') }}" class="nav__item-link">
                    <div class="nav__item-icon">
                        <ion-icon name="apps-outline"></ion-icon>
                    </div>
                    <span class="nav__item-text">Categories</span>
                </a>
            </li>
{{--            <li class="nav__item {{ request()->is('cart') ? 'active' : '' }}">--}}
{{--                <a href="{{ url('/cart') }}" class="nav__item-link">--}}
{{--                    <div class="nav__item-icon">--}}
{{--                        <ion-icon name="cart-outline"></ion-icon>--}}
{{--                    </div>--}}
{{--                    <span class="nav__item-text">Cart</span>--}}
{{--                </a>--}}
{{--            </li>--}}
            @if (auth()->check())
                <li class="nav__item {{ request()->is('account/wishlist') ? 'active' : '' }}">
                    <a href="{{ url('/account/wishlist') }}" class="nav__item-link">
                        <div class="nav__item-icon">
                            <ion-icon name="heart-outline"></ion-icon>
                        </div>
                        <span class="nav__item-text">wishlist</span>
                    </a>
                </li>
                <li class="nav__item {{ request()->is('account/profile') ? 'active' : '' }}">
                    <a href="{{ url('/account/profile') }}" class="nav__item-link">
                        <div class="nav__item-icon">
                            <ion-icon name="person-outline"></ion-icon>
                        </div>
                        <span class="nav__item-text">Profile</span>
                    </a>
                </li>

            @else
                <li class="nav__item {{ request()->is('#flash-sale-section') ? 'active' : '' }}">
                    <a href="{{ url('/#flash-sale-section') }}" class="nav__item-link">
                        <div class="nav__item-icon">
                            <ion-icon name="flower-outline"></ion-icon>
                        </div>
                        <span class="nav__item-text">Deals</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>

<!-- CSS for Mobile Bottom Navigation -->
<style>
    /*@import url("https://fonts.googleapis.com/css2?family=Varela+Round&display=swap");*/

    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        width: 100%;
        /*padding: 24px;*/
        max-width: 500px;
        margin: 0 auto;
        left: 0;
        right: 0;
        display: none; /* Hidden by default */
        z-index: 1000;
    }

    .mobile-bottom-nav .nav-box {
        display: flex;
        padding: 8px;
        background-color: #fff;
        box-shadow: 0px 0px 16px 0px #4444;
        border-radius: 8px;
    }

    .mobile-bottom-nav .nav-container {
        display: flex;
        width: 100%;
        list-style: none;
        justify-content: space-around;
    }

    .mobile-bottom-nav .nav__item {
        display: flex;
        position: relative;
        padding: 2px;
    }

    .mobile-bottom-nav .nav__item.active .nav__item-icon {
        margin-top: -26px;
        box-shadow: 0px 0px 16px 0px #4444;
        color: #e33b80;
        font-weight: bold;
    }

    .mobile-bottom-nav .nav__item.active .nav__item-text {
        transform: scale(1);
        color: #e33b80;
        font-weight: bold;
    }

    .mobile-bottom-nav .nav__item-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #2f3046;
        text-decoration: none;
    }

    .mobile-bottom-nav .nav__item-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6em;
        background-color: #fff;
        border-radius: 50%;
        height: 46px;
        width: 46px;
        transition: margin-top 250ms ease-in-out, box-shadow 250ms ease-in-out;
    }

    .mobile-bottom-nav .nav__item-text {
        position: absolute;
        bottom: 0;
        transform: scale(0);
        transition: transform 250ms ease-in-out;
    }

    /* Show only on smaller devices (mobile) */
    @media (max-width: 767px) {
        .mobile-bottom-nav {
            display: block;
        }

        .wrapper {
            padding-bottom: 80px; /* Prevent overlap with content */
        }
        #chatIconContainer
        {
            bottom: 90px;
        }
    }

    /* Apply font to toolbar only */
    .mobile-bottom-nav * {
        font-family: "Varela Round", sans-serif;
        -webkit-tap-highlight-color: #0000;
    }
</style>

<!-- JS for Mobile Bottom Navigation -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const list = document.querySelectorAll(".nav__item");
        list.forEach((item) => {
            item.addEventListener("click", () => {
                list.forEach((item) => item.classList.remove("active"));
                item.classList.add("active");
            });
        });
    });
</script>
