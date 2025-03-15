<div x-data="ProductRating({{ $data ?? 'product' }})" class="product-rating">
    <div class="back-stars">
        <i class="las la-star"></i>
        <i class="las la-star"></i>
        <i class="las la-star"></i>
        <i class="las la-star"></i>
        <i class="las la-star"></i>

        <div x-cloak class="front-stars" :style="{ width: ratingPercent + '%' }">
            <i class="las la-star"></i>
            <i class="las la-star"></i>
            <i class="las la-star"></i>
            <i class="las la-star"></i>
            <i class="las la-star"></i>
        </div>
    </div>

    @if(isset($product))
        @php
            $reviewCount = $product->reviews->count();
        @endphp
        <span class="reviews"> {{ $reviewCount === 1 ? "$reviewCount review" : "$reviewCount reviews" }}</span>
        <span class="reviews"> | {{ $product->getSoldCount() }} sold</span>
    @endif
</div>
