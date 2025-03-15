<header class="p-4">
    <!-- Top Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center text-sm space-y-2 md:space-y-0">
        <!-- Free Delivery Section -->
        <div class="flex justify-start items-center gap-4">
            <img src="{{ asset('Floramom/images/icons/1.Delivery-icon 1.png') }}" alt="Free Delivery Icon" class="w-6 h-6">
            <p class="font-bold text-center">
                To Get Free Delivery Order Up to <span class="text-[#E33B80]">1500</span>
            </p>
        </div>

        <!-- Offer Section -->
        <div class="flex justify-center items-center">
            <p class="font-bold text-center">
                Get <span class="text-[#E33B80]">20%</span> OFF Ordering From Flora Mom Apps
            </p>
        </div>

        <!-- Contact and Social Links -->
        <div class="flex flex-wrap justify-between items-center gap-3 text-center">
            <p class="font-bold">
                <span class="text-[#E33B80]">Floramom</span> Membership
            </p>
            <a href="#" class="underline">Contact Us</a>
            <div class="flex gap-4">
                <img src="{{ asset('Floramom/images/icons/facebook-logo 1.png') }}" alt="Facebook Logo" class="w-5 h-5">
                <img src="{{ asset('Floramom/images/icons/youtube 1.png') }}" alt="YouTube Logo" class="w-5 h-5">
                <img src="{{ asset('Floramom/images/icons/instagram 1.png') }}" alt="Instagram Logo" class="w-5 h-5">
            </div>
        </div>
    </div>
    <!-- Navigation Bar -->
    @include('floramom::layouts.header.nabbar')
</header>
