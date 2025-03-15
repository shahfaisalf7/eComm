<section class="my-12 flex flex-wrap justify-between items-center gap-6">
    <!-- Text Section -->
    <div class="space-y-3 w-full md:w-auto text-center md:text-left">
        <h2 class="font-bold text-xl md:text-2xl">Top Categories At Once</h2>
        <p class="font-normal max-w-[300px] mx-auto md:mx-0">
            You can choose your products from here. Get ultra-level experience shopping your favorite skincare
            products.
        </p>
    </div>
    <!-- Categories Section -->
    <div class="flex flex-wrap justify-center md:justify-start items-center gap-4">
        <!-- Category Item -->
        @foreach ($featuredCategories['categories'] as $key => $tab)
            <div class="flex flex-col justify-center items-center w-[100px]">
                @if ($tab['logo']->path)
                    <img class="border-2 w-[100px] border-[#E33B80] rounded-full" src="{{ $tab['logo']->path }}"
                        alt="Category logo" loading="lazy">
                @else
                    <img src="{{ asset('build/assets/image-placeholder.png') }}" class="image-placeholder"
                        alt="Category logo" loading="lazy" />
                @endif
                <p class="font-semibold text-center mt-2">{{ $tab['name'] }}</p>
            </div>
        @endforeach
    </div>
</section>
