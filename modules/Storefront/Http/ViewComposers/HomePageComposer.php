<?php

namespace Modules\Storefront\Http\ViewComposers;

use Illuminate\View\View;
use Modules\Storefront\Banner;
use Modules\Storefront\Feature;
use Modules\Brand\Entities\Brand;
use Illuminate\Support\Collection;
use Modules\Blog\Entities\BlogPost;
use Modules\Slider\Entities\Slider;
use Modules\FlashSale\Entities\FlashSale;
use Illuminate\Support\Facades\Cache;
use Modules\Category\Entities\Category;

class HomePageComposer
{
    public function compose($view)
    {
        $view->with([
            'slider' => Slider::findWithSlides(setting('storefront_slider')),
            'sliderBanners' => Banner::getSliderBanners(),
            'features' => Feature::all(),
            'featuredCategories' => $this->featuredCategoriesSection(),
            'threeColumnFullWidthBanners' => $this->threeColumnFullWidthBanners(),
            'productTabsOne' => $this->productTabsOne(),
            'topBrands' => $this->topBrands(),
            'flashSale' => $this->flashSale(),
            'twoColumnBanners' => $this->twoColumnBanners(),
            'gridProducts' => $this->gridProducts(),
            'threeColumnBanners' => $this->threeColumnBanners(),
            'productTabsTwo' => $this->productTabsTwo(),
            'oneColumnBanner' => $this->oneColumnBanner(),
            'blog' => $this->blog(),
        ]);
    }

    public function sections()
    {
        $final_data = [
            'slider' => Slider::findWithSlides(setting('storefront_slider')),
            'sliderBanners' => Banner::getSliderBanners(),
            'features' => Feature::all(),
            'featuredCategories' => $this->featuredCategoriesSection(),
            'threeColumnFullWidthBanners' => $this->threeColumnFullWidthBanners(),
            'productTabsOne' => $this->productTabsOneApi(),
            'topBrands' => $this->topBrands(),
            'flashSale' => $this->flashSale(),
            'twoColumnBanners' => $this->twoColumnBanners(),
            'gridProducts' => $this->gridProducts(),
            'threeColumnBanners' => $this->threeColumnBanners(),
            'productTabsTwo' => $this->productTabsTwoApi(),
            'oneColumnBanner' => $this->oneColumnBanner(),
            'blog' => $this->blog(),
        ];
        return responseWithData(trans('Sections informations.'), $final_data);
    }

    public function mobileSections()
    {
        $data = [
            ['module_type' => 'Slider', 'title' => '', 'data' => Slider::findWithSlides(setting('storefront_slider'))],
            ['module_type' => 'Banner', 'title' => '', 'data' => Banner::getSliderBanners()],
//            ['module_type' => 'Feature', 'title' => '', 'data' => Feature::all()],
            ['module_type' => 'Category', 'title' => 'Top Category', 'data' => $this->featuredCategoriesSection()['categories'] ?? []],
            ['module_type' => 'Banner', 'title' => 'Three Column Full Width', 'data' => $this->threeColumnFullWidthBanners() ?? []],
            ['module_type' => 'Highlighted_Products', 'title' => 'Highlighted Products', 'data' => $this->all_products() ?? []],
            ['module_type' => 'Brand', 'title' => 'Top Brands', 'data' => $this->topBrands() ?? []],
//            ['module_type' => 'Product', 'title' => 'Flash Sale', 'data' => $this->flashSale() ?? []],
//            ['module_type' => 'Banner', 'title' => 'Two Column', 'data' => $this->twoColumnBanners() ?? []],
//            ['module_type' => 'Product', 'title' => 'Grid Products', 'data' => $this->gridProducts() ?? []],
//            ['module_type' => 'Banner', 'title' => 'Three Column', 'data' => $this->threeColumnBanners() ?? []],
            ['module_type' => 'Sub Products', 'title' => 'Product Tabs Two', 'data' => $this->productTabsTwoApi() ?? []],
//            ['module_type' => 'Banner', 'title' => 'One Column', 'data' => $this->oneColumnBanner() ?? []],
            //           ['module_type' => 'Blog', 'title' => '', 'data' => $this->blog()['blogPosts'] ?? []],
        ];

        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => array_filter($data, fn($section) => !is_null($section['data'])),
        ]);
    }

    private function featuredCategoriesSection()
    {
        if (!setting('storefront_featured_categories_section_enabled')) {
            return [];
        }

        return [
            'title' => setting('storefront_featured_categories_section_title'),
            'subtitle' => setting('storefront_featured_categories_section_subtitle'),
            'categories' => $this->getFeaturedCategories(),
        ];
    }

    private function getFeaturedCategories()
    {
        $categoryIds = Collection::times(6, function ($number) {
            if (!is_null(setting("storefront_featured_categories_section_category_{$number}_product_type"))) {
                return setting("storefront_featured_categories_section_category_{$number}_category_id");
            }
        })->filter();

        return Category::with('files')
            ->whereIn('id', $categoryIds)
            ->when($categoryIds->isNotEmpty(), function ($query) use ($categoryIds) {
                $query->orderByRaw("FIELD(id, {$categoryIds->filter()->implode(',')})");
            })
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'logo' => $category->logo,
                    'slug' => $category->slug,
                ];
            });
    }

    private function threeColumnFullWidthBanners()
    {
        if (setting('storefront_three_column_full_width_banners_enabled')) {
            return Banner::getThreeColumnFullWidthBanners();
        }
    }

    private function productTabsOne()
    {
        if (!setting('storefront_product_tabs_1_section_enabled')) {
            return;
        }

        return Collection::times(4, function ($number) {
            if (!is_null(setting("storefront_product_tabs_1_section_tab_{$number}_product_type"))) {
                return setting("storefront_product_tabs_1_section_tab_{$number}_title");
            }
        })->filter();
    }

    private function productTabsOneApi()
    {
        if (!setting('storefront_product_tabs_1_section_enabled')) {
            return;
        }

        return Collection::times(4, function ($number) {
            if (!is_null(setting("storefront_product_tabs_1_section_tab_{$number}_product_type"))) {
                return [
                    'title' => setting("storefront_product_tabs_1_section_tab_{$number}_title"),
                    'path' => route("storefront.tab_products.index", [
                        'sectionNumber' => 1,
                        'tabNumber' => $number,
                    ]),
                ];
            }
        })->filter();
    }

    private function topBrands()
    {
        if (!setting('storefront_top_brands_section_enabled')) {
            return collect();
        }

        $topBrandIds = setting('storefront_top_brands', []);

        return Cache::rememberForever(md5('storefront_top_brands:' . serialize($topBrandIds)), function () use ($topBrandIds) {
            return Brand::with('files')
                ->whereIn('id', $topBrandIds)
                ->when(!empty($topBrandIds), function ($query) use ($topBrandIds) {
                    $topBrandIdsString = collect($topBrandIds)->filter()->implode(',');
                    $query->orderByRaw("FIELD(id, {$topBrandIdsString})");
                })
                ->get()
                ->map(function (Brand $brand) {
                    return [
                        'url' => $brand->url(),
                        'logo' => $brand->getLogoAttribute(),
                    ];
                });
        });
    }

    private function flashSale()
    {
        return [
            'title' => setting('storefront_flash_sale_title'),
            'vertical_products_1_title' => setting('storefront_vertical_products_1_title'),
            'vertical_products_2_title' => setting('storefront_vertical_products_2_title'),
            'vertical_products_3_title' => setting('storefront_vertical_products_3_title'),
        ];
    }

    private function all_products()
    {
        return [
            'title' => 'all_products',
            'path' => '/api/v1/products?query=&brand=&category=&tag=&fromPrice=0&toPrice=6749&sort=latest&perPage=20&page=1',

        ];
    }

    private function twoColumnBanners()
    {
        if (setting('storefront_two_column_banners_enabled')) {
            return Banner::getTwoColumnBanners();
        }
    }

    private function gridProducts()
    {
        if (!setting('storefront_product_grid_section_enabled')) {
            return;
        }

        return Collection::times(4, function ($number) {
            if (!is_null(setting("storefront_product_grid_section_tab_{$number}_product_type"))) {
                return setting("storefront_product_grid_section_tab_{$number}_title");
            }
        })->filter();
    }

    private function threeColumnBanners()
    {
        if (setting('storefront_three_column_banners_enabled')) {
            return Banner::getThreeColumnBanners();
        }
    }

    private function productTabsTwo()
    {
        if (!setting('storefront_product_tabs_2_section_enabled')) {
            return;
        }

        $tabs = Collection::times(4, function ($number) {
            if (!is_null(setting("storefront_product_tabs_2_section_tab_{$number}_product_type"))) {
                return setting("storefront_product_tabs_2_section_tab_{$number}_title");
            }
        })->filter();

        return [
            'title' => setting('storefront_product_tabs_2_section_title'),
            'tabs' => $tabs,
        ];
    }

    private function productTabsTwoApi()
    {
        if (!setting('storefront_product_tabs_2_section_enabled')) {
            return;
        }

        $tabs = Collection::times(4, function ($number) {
            if (!is_null(setting("storefront_product_tabs_2_section_tab_{$number}_product_type"))) {
                return [
                    'title' => setting("storefront_product_tabs_2_section_tab_{$number}_title"),
                    'path' => route("storefront.tab_products.index", [
                        'sectionNumber' => 2,
                        'tabNumber' => $number,
                    ]),
                ];
            }
        })->filter();

        return [
            'title' => setting('storefront_product_tabs_2_section_title'),
            'tabs' => $tabs,
        ];
    }

    private function oneColumnBanner()
    {
        if (setting('storefront_one_column_banner_enabled')) {
            return Banner::getOneColumnBanner();
        }
    }

    private function blog()
    {
        if (setting('storefront_blogs_section_enabled')) {
            $blogPosts = BlogPost::published()
                ->latest()
                ->take(setting('storefront_recent_blogs') ?? 10)
                ->get();

            return [
                'title' => setting('storefront_blogs_section_title'),
                'blogPosts' => $blogPosts,
            ];
        }
    }
}
