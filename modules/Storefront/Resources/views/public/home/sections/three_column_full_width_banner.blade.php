<section
    class="banner-wrap three-column-full-width-banner"
    style="background-image: url({{ $threeColumnFullWidthBanners['background']->image->path }})"
>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-9 order-1 order-md-1">
                <a
                    href="{{ $threeColumnFullWidthBanners['banner_1']->call_to_action_url }}"
                    class="banner"
                    target="{{ $threeColumnFullWidthBanners['banner_1']->open_in_new_window ? '_blank' : '_self' }}"
                >
                    <img src="{{ $threeColumnFullWidthBanners['banner_1']->image->path }}" alt="Banner" loading="lazy" />
                </a>
            </div>

            <div class="col-md-10 col-18 order-3 order-md-2">
                <a
                    href="{{ $threeColumnFullWidthBanners['banner_2']->call_to_action_url }}"
                    class="banner"
                    target="{{ $threeColumnFullWidthBanners['banner_2']->open_in_new_window ? '_blank' : '_self' }}"
                >
                    <img src="{{ $threeColumnFullWidthBanners['banner_2']->image->path }}" alt="Banner" loading="lazy" />
                </a>
            </div>

            <div class="col-md-4 col-9 order-2 order-md-3">
                <a
                    href="{{ $threeColumnFullWidthBanners['banner_3']->call_to_action_url }}"
                    class="banner"
                    style="margin: 0"
                    target="{{ $threeColumnFullWidthBanners['banner_3']->open_in_new_window ? '_blank' : '_self' }}"
                >
                    <img src="{{ $threeColumnFullWidthBanners['banner_3']->image->path }}" alt="Banner" loading="lazy" />
                </a>
            </div>
        </div>
    </div>
</section>
