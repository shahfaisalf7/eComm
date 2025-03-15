@extends('floramom::layouts.master')
@section('content')
    <main>
        <!-- Banner Section -->
        @if (setting('storefront_features_section_enabled'))
            @include('floramom::layouts.home.sections.home_features')
        @endif
        <!-- Top Category Section -->
        @if (setting('storefront_featured_categories_section_enabled'))
            @include('floramom::layouts.home.sections.featured_categories')
        @endif
        <!--  -->
        <div class="container mx-auto px-4">
            <div
                class="header-section bg-[#FFF1F1] min-h-[217px] mb-20 flex relative justify-center items-center flex-wrap gap-10 py-8 px-4 2xl:px-16">
                <div
                    class="product-container flex justify-center items-center gap-4 xl:gap-8 2xl:gap-10 w-full flex-wrap relative">
                    <!-- First Row -->
                    <div class="product-item flex flex-col items-center">
                        <div class="bg-white w-[138px] h-[136px] rounded-[37px] relative flex justify-center items-center">
                            <img src="{{ asset('Floramom/images/section/1.png') }}"
                                class="absolute -top-[22%] left-[22%] max-w-full" alt="Product 1">
                        </div>
                        <h1 class="text-sm font-medium mt-2">Product Name 1</h1>
                    </div>
                    <div class="product-item flex flex-col items-center">
                        <div class="bg-white w-[138px] h-[136px] rounded-[37px] relative flex justify-center items-center">
                            <img src="{{ asset('Floramom/images/section/2.png') }}"
                                class="absolute -top-[22%] left-[22%] max-w-full" alt="Product 2">
                        </div>
                        <h1 class="text-sm font-medium mt-2">Product Name 2</h1>
                    </div>
                    <div class="product-item flex flex-col items-center z-50">
                        <div class="bg-white w-[138px] h-[136px] rounded-[37px] relative flex justify-center items-center">
                            <img src="{{ asset('Floramom/images/section/3.png') }}"
                                class="absolute -top-[12%] left-[13%] max-w-full" alt="Product 3">
                        </div>
                        <h1 class="text-sm font-medium mt-2">Product Name 3</h1>
                    </div>

                    <!-- Floating Product -->
                    <div class="floating-product product-item  z-50 md:mt-0 mt-4">
                        <div class="flex flex-col items-center">
                            <div
                                class="bg-white w-[138px] h-[136px] rounded-[37px] relative flex justify-center items-center">
                                <img src="{{ asset('Floramom/images/section/8.png') }}"
                                    class="absolute -top-[16%] left-[22%] max-w-full" alt="Product 4">
                            </div>
                            <h1 class="text-sm font-medium mt-2">Product Name 4</h1>
                        </div>
                    </div>

                    <!-- Second Row -->
                    <div class="product-item flex flex-col items-center z-50">
                        <div class="bg-white w-[138px] h-[136px] rounded-[37px] relative flex justify-center items-center">
                            <img src="{{ asset('Floramom/images/section/5.png') }}"
                                class="absolute -top-[12%] left-[25%] max-w-full" alt="Product 5">
                        </div>
                        <h1 class="text-sm font-medium mt-2">Product Name 5</h1>
                    </div>
                    <div class="product-item flex flex-col items-center z-50">
                        <div class="bg-white w-[138px] h-[136px] rounded-[37px] relative flex justify-center items-center">
                            <img src="{{ asset('Floramom/images/section/6.png') }}"
                                class="absolute -top-[16%] left-[30%] max-w-full" alt="Product 6">
                        </div>
                        <h1 class="text-sm font-medium mt-2">Product Name 6</h1>
                    </div>
                    <div class="product-item flex flex-col items-center">
                        <div class="bg-white w-[138px] h-[136px] rounded-[37px] relative flex justify-center items-center">
                            <img src="{{ asset('Floramom/images/section/7.png') }}"
                                class="absolute -top-[16%] left-[22%] max-w-full" alt="Product 7">
                        </div>
                        <h1 class="text-sm font-medium mt-2">Product Name 7</h1>
                    </div>
                    <div class="product-item flex flex-col items-center">
                        <div class="bg-white w-[138px] h-[136px] rounded-[37px] relative flex justify-center items-center">
                            <img src="{{ asset('Floramom/images/section/4.png') }}"
                                class="absolute -top-[12%] left-[7%] max-w-full" alt="Product 8">
                        </div>
                        <h1 class="text-sm font-medium mt-2">Product Name 8</h1>
                    </div>
                </div>

                <!-- Decorative Background Element -->
                <div class="rotated-bg bg-[#FFF1F1] w-20 h-20 z-0 absolute -top-2 rotate-45 left-[35%]"></div>
            </div>
        </div>


        <!-- Space Section -->
        <section class="flex flex-wrap justify-center items-center gap-5 bg-[#FFF6F6] p-6 sm:p-8">
            <div class="h-[200px] sm:h-[260px] bg-[#FFC2C2] w-full sm:w-[calc(50%-10px)]"></div>
            <div class="h-[200px] sm:h-[260px] bg-[#FFC2C2] w-full sm:w-[calc(50%-10px)]"></div>
        </section>
        <!--New Arrival Product Section -->
        <section class="my-12">
            <h1 class="font-bold text-2xl mb-3">New Arrivals</h1>
            <!-- Navigation Section -->
            <div class="border-b-2 flex justify-between items-center flex-wrap gap-3">
                <div class="flex justify-between items-center gap-5 flex-wrap">
                    <p class="text-blue-700 border-b-2 border-blue-700 p-2">Features</p>
                    <p>Special</p>
                    <p>New Arrivals</p>
                    <p>Latest</p>
                </div>
                <div>
                    <p><span class="text-gray-400 pr-4">&lt; Prev </span> Next &gt;</p>
                </div>
            </div>
            <!-- Products Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mt-6">
                <!-- Product Card -->
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product1.png') }}" alt="Product 1">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Sun Screen</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Repeat similar structure for other products -->
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product2.png') }}" alt="Product 2">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Face Wash</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product3.png') }}" alt="Product 3">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Shower Gel</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product4.png') }}" alt="Product 4">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Baby Shower</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product5.png') }}" alt="Product 5">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Face Cream</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Most Popular Product Section -->
        <section class="my-12">
            <h1 class="font-bold text-2xl mb-3">Most Demanding</h1>
            <!-- Navigation Section -->
            <div class="border-b-2 flex justify-between items-center flex-wrap gap-3">
                <div class="flex justify-between items-center gap-5 flex-wrap">
                    <p class="text-blue-700 border-b-2 border-blue-700 p-2">Features</p>
                    <p>Special</p>
                    <p>New Arrivals</p>
                    <p>Latest</p>
                </div>
                <div>
                    <p><span class="text-gray-400 pr-4">&lt; Prev </span> Next &gt;</p>
                </div>
            </div>

            <!-- Product Cards Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mt-6">
                <!-- Product Card -->
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product6.png') }}" alt="Product 1">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Sun Screen</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Repeat similar structure for other products -->
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product7.png') }}" alt="Product 2">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Face Wash</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product8.png') }}" alt="Product 3">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Shower Gel</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product4.png') }}" alt="Product 4">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Baby Shower</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col overflow-hidden bg-gray-100 border rounded-lg shadow-lg">
                    <div class="flex justify-center items-center bg-cover">
                        <img src="{{ asset('Floramom/images/Products/Product5.png') }}" alt="Product 5">
                    </div>
                    <div class="p-4">
                        <h1 class="text-xl font-semibold text-gray-700">Face Cream</h1>
                        <div class="flex gap-1 mt-2 items-center">
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-star text-gray-400 text-xs"></i>
                            <p class="text-xs text-gray-400 font-medium pl-2">0 reviews</p>
                        </div>
                        <div class="flex justify-between mt-3 items-center">
                            <h1 class="text-base font-bold text-gray-500">$ 7.20</h1>
                            <button
                                class="px-2 py-1 text-xs font-bold text-blue-700 transition-colors duration-300 transform bg-blue-100 rounded hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-700">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
