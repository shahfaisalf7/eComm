<?php

namespace Modules\Category\Entities;

use TypiCMS\NestableTrait;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Carbon;
use Modules\Media\Entities\File;
use Modules\Support\Eloquent\Model;
use Modules\Media\Eloquent\HasMedia;
use Illuminate\Support\Facades\Cache;
use Modules\Product\Entities\Product;
use Modules\Support\Eloquent\Sluggable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Modules\Support\Eloquent\Translatable;
use Modules\Meta\Eloquent\HasMetaData;

class Category extends Model implements Sitemapable
{
    use Translatable, Sluggable, HasMedia, NestableTrait, HasMetaData;

    protected $with = ['translations'];
    protected $fillable = ['parent_id', 'slug', 'position', 'is_searchable', 'is_active'];
    protected $hidden = ['translations'];
    protected $casts = [
        'is_searchable' => 'boolean',
        'is_active' => 'boolean',
    ];
    protected $translatedAttributes = ['name'];
    protected $slugAttribute = 'name';

    public function metaData()
    {
        return $this->morphMany(\Modules\Meta\Entities\MetaData::class, 'entity');
    }

    public static function findBySlug($slug)
    {
        return static::with(['files', 'metaData'])->where('slug', $slug)->firstOrNew([]);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return static::with(['files', 'metaData'])->where('slug', $value)->firstOrFail();
    }

    public static function tree()
    {
        return Cache::tags('categories')
            ->rememberForever(md5('categories.tree:' . locale()), function () {
                return static::with(['files', 'metaData'])
                    ->orderByRaw('-position DESC')
                    ->get()
                    ->nest();
            });
    }

    public static function treeList()
    {
        return Cache::tags('categories')->rememberForever(md5('categories.tree_list:' . locale()), function () {
            return static::orderByRaw('-position DESC')
                ->get()
                ->nest()
                ->setIndent('¦–– ')
                ->listsFlattened('name');
        });
    }

    public static function searchable()
    {
        return Cache::tags('categories')
            ->rememberForever(md5('categories.searchable:' . locale()), function () {
                return static::where('is_searchable', true)
                    ->get()
                    ->map(function ($category) {
                        return [
                            'slug' => $category->slug,
                            'name' => $category->name,
                        ];
                    });
            });
    }

    protected static function booted()
    {
        static::addActiveGlobalScope();
    }

    public function isRoot()
    {
        return $this->exists && is_null($this->parent_id);
    }

    public function url()
    {
        return route('categories.products.index', ['category' => $this->slug]);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function getLogoAttribute()
    {
        return $this->files->where('pivot.zone', 'logo')->first() ?: new File;
    }

    public function getBannerAttribute()
    {
        return $this->files->where('pivot.zone', 'banner')->first() ?: new File;
    }

    public function toArray()
    {
        $attributes = parent::toArray();

        if ($this->relationLoaded('files')) {
            $attributes += [
                'logo' => [
                    'id' => $this->logo->id,
                    'path' => $this->logo->path,
                    'exists' => $this->logo->exists,
                ],
                'banner' => [
                    'id' => $this->banner->id,
                    'path' => $this->banner->path,
                    'exists' => $this->banner->exists,
                ],
            ];
        }

        if ($this->relationLoaded('metaData')) {
            $attributes['metaData'] = $this->metaData->map(function ($meta) {
                return [
                    'key' => $meta->key ?? 'meta_' . strtolower(str_replace(' ', '_', $meta->meta_title)),
                    'value' => $meta->value ?? $meta->meta_title ?? $meta->meta_description,
                ];
            })->all();
        }

        return $attributes;
    }

    public function toSitemapTag(): Url|string|array
    {
        return Url::create(route('categories.products.index', $this->slug))
            ->setLastModificationDate(Carbon::create($this->updated_at))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.1);
    }
}
