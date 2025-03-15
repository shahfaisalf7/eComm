<style>
    .suggestions-box .category,
    .suggestions-box .product {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
    }

    .suggestions-box .category:hover,
    .suggestions-box .product:hover {
        background-color: #f9f9f9;
    }

    .suggestions-box .category {
        font-weight: bold;
    }

    .suggestions-box img {
        width: 50px;
        height: 50px;
        margin-right: 10px;
    }

    .suggestions-box .product-info {
        display: inline-block;
        vertical-align: middle;
    }
</style>

<nav class="flex flex-col lg:flex-row justify-between items-center my-5 space-y-4 lg:space-y-0">
    <!-- Logo -->
    <a href="{{ route('home') }}">
        <img class="w-[100px] lg:w-[120px]" src="{{ asset('Floramom/images/flora-mo-logo.png') }}" alt="Flora Mo Logo">
    </a>

    <!-- Search Bar -->
    <div class="flex items-center p-2 px-4 rounded-full w-full lg:w-[500px] border-2 relative">
        <i class="fa-solid fa-bars text-gray-500"></i> &nbsp;&nbsp;
        <input class="outline-none w-full text-sm" type="search" name="search"
            placeholder="Search With AI or Image. EX: Facewash" oninput="handleSearchInput(event)" />

        <!-- Suggestions box -->
        <div id="suggestions-box"
            class="suggestions-box absolute top-full left-0 mt-0 w-full max-h-[300px] bg-white border rounded-lg shadow-md z-10 hidden overflow-auto">
            <!-- Suggestion items go here -->
        </div>

        <div class="flex gap-3 ml-2">
            <img src="{{ asset('Floramom/images/icons/Vector (1).png') }}" alt="Icon 1" class="w-5 h-5">
            <img src="{{ asset('Floramom/images/icons/Vector.png') }}" alt="Icon 2">
        </div>
    </div>

    <!-- Action Items -->
    <div class="flex flex-wrap justify-center items-center gap-4">
        <h1 class="font-bold text-[#E33B80] text-sm">Deals</h1>
        <p class="bg-[#E33B80] p-2 text-white rounded-xl text-xs text-center">
            Know Your <br> Skin Type
        </p>
        <div class="flex items-center gap-2">
            <img src="{{ asset('Floramom/images/icons/2.Profile 1.png') }}" alt="Profile Icon" class="w-6 h-6">
            <div class="space-y-0 text-sm">
                <p>Welcome !!!</p>
                <p class="font-bold">Sign In / Up</p>
            </div>
        </div>
        <img src="{{ asset('Floramom/images/icons/3.Cart 2.png') }}" alt="Cart Icon" class="w-6 h-6">
        <img src="{{ asset('Floramom/images/icons/4.Wishlist 1.png') }}" alt="Wishlist Icon" class="w-6 h-6">
    </div>
</nav>

<script>
    function handleSearchInput(event) {
        Header.init("{{ csrf_token() }}", "#suggestions-box");
        Header.form.query = event.target.value;
        Header.fetchSuggestions();
    }
</script>
