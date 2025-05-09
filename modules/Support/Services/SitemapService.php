<?php

namespace Modules\Support\Services;

use Spatie\Sitemap\Sitemap;
use Modules\Page\Entities\Page;
use Spatie\Sitemap\SitemapIndex;
use Modules\Brand\Entities\Brand;
use Modules\Blog\Entities\BlogPost;
use Illuminate\Support\Facades\File;
use Modules\Product\Entities\Product;
use Modules\Category\Entities\Category;
use Modules\Blog\Entities\BlogCategory;

class SitemapService
{
    private array $sitemaps;


    public function __construct()
    {
        $this->sitemaps = [];
    }


    public function generate()
    {
        $path = public_path('sitemaps');
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
        File::cleanDirectory($path);

        $this->generateSitemaps();

        $sitemapIndex = SitemapIndex::create();

        foreach ($this->sitemaps as $sitemap) {
            $sitemapIndex->add($sitemap);
        }

        $sitemapIndex->writeToFile(public_path('sitemap.xml'));

        $this->updateRobotTxt();
    }


    public function generateSitemaps()
    {
        $this->generateForProducts();
        $this->generateForCategories();
        $this->generateForBrands();
        $this->generateForPages();
        $this->generateForBlogPosts();
        $this->generateForBlogCategories();
    }


    private function generateForProducts()
    {
        $counter = 1;

        Product::chunk('10000', function ($products) use (&$counter) {
            $outputDir = public_path('sitemaps');
            $sitemapName = 'sitemap-products-' . $counter++ . '.xml';

            Sitemap::create()
                ->add($products)
                ->writeToFile("$outputDir/$sitemapName");

            $this->sitemaps[] = url("sitemaps/$sitemapName");
        });
    }


    private function generateForCategories()
    {
        $counter = 1;

        Category::chunk('1000', function ($categories) use (&$counter) {
            $outputDir = public_path('sitemaps');
            $sitemapName = 'sitemap-categories-' . $counter++ . '.xml';

            Sitemap::create()
                ->add($categories)
                ->writeToFile("$outputDir/$sitemapName");

            $this->sitemaps[] = url("sitemaps/$sitemapName");
        });
    }


    private function generateForBrands()
    {
        $counter = 1;

        Brand::chunk('1000', function ($brands) use (&$counter) {
            $outputDir = public_path('sitemaps');
            $sitemapName = 'sitemap-brands-' . $counter++ . '.xml';

            Sitemap::create()
                ->add($brands)
                ->writeToFile("$outputDir/$sitemapName");

            $this->sitemaps[] = url("sitemaps/$sitemapName");
        });
    }


    private function generateForPages()
    {
        $counter = 1;

        Page::chunk('1000', function ($pages) use (&$counter) {
            $outputDir = public_path('sitemaps');
            $sitemapName = 'sitemap-pages-' . $counter++ . '.xml';

            Sitemap::create()
                ->add($pages)
                ->writeToFile("$outputDir/$sitemapName");

            $this->sitemaps[] = url("sitemaps/$sitemapName");
        });
    }


    private function generateForBlogPosts()
    {
        $counter = 1;

        BlogPost::chunk('1000', function ($blogPosts) use (&$counter) {
            $outputDir = public_path('sitemaps');
            $sitemapName = 'sitemap-blog-posts-' . $counter++ . '.xml';

            Sitemap::create()
                ->add($blogPosts)
                ->writeToFile("$outputDir/$sitemapName");

            $this->sitemaps[] = url("sitemaps/$sitemapName");
        });
    }


    private function generateForBlogCategories()
    {
        $counter = 1;

        BlogCategory::chunk('1000', function ($blogCategories) use (&$counter) {
            $outputDir = public_path('sitemaps');
            $sitemapName = 'sitemap_blog-categories-' . $counter++ . '.xml';

            Sitemap::create()
                ->add($blogCategories)
                ->writeToFile("$outputDir/$sitemapName");

            $this->sitemaps[] = url("sitemaps/$sitemapName");
        });
    }


    private function updateRobotTxt()
    {
        $path = public_path('robots.txt');

        // Check if file exists and is writable
        if (!file_exists($path)) {
            logger()->info("robots.txt doesn’t exist, creating new file");
            $robotTxt = '';
        } elseif (!is_writable($path)) {
            logger()->error("robots.txt is not writable at $path");
            return; // Exit if we can’t write
        } else {
            $robotTxt = file_get_contents($path);
        }

        $sitemapUrl = 'Sitemap: ' . url('sitemap.xml');
        logger()->info('Updating robots.txt with: ' . $sitemapUrl);

        if (strpos($robotTxt, 'Sitemap:')) {
            $robotTxt = preg_replace('/.*Sitemap:.*\n/', $sitemapUrl . "\n", $robotTxt);
        } else {
            $robotTxt .= "\n" . $sitemapUrl;
        }

        if (file_put_contents($path, $robotTxt) === false) {
            logger()->error("Failed to write to robots.txt at $path");
        } else {
            logger()->info("Successfully updated robots.txt");
        }
    }



}
