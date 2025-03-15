<style>
    .cart-icon {
        position: fixed;
        top: 40%;
        right: 0;
        transform: translateY(-50%);
        width: 55px;
        background-color: white;
        text-align: center;
        border-radius: 8px 0 0 8px;
        box-shadow: 0 4px 6px rgb(49 49 49 / 100%);
        cursor: pointer;
        z-index: 999;
    }

    .cart-icon .icon {
        font-size: 24px;
        margin-top: 10px;
    }

    .cart-icon .details {
        font-size: 10px;
        margin: 5px 0;
        font-weight: bold;
    }

    .cart-icon .price {
        background-color: #e33b80;
        color: white;
        font-size: 12px;
        padding: 5px;
        border-radius: 0 0 0 8px;
        font-weight: bold;
        transition: transform 0.3s ease, color 0.3s ease;
    }
</style>

<!-- Compact Cart Icon -->
<a href="{{ route('cart.index') }}" class="cart-icon" @click="$store.layout.openSidebarCart($event)">
    {{-- <div class="icon">🛍️</div> --}}
    <img class="icon" width="50%"   src="{{ asset('build/assets/cart-icon.png') }}" alt="Cart Icon">

    <div class="details pr-color">
        <span x-text="$store.state.cartQuantity">{{ $cart->toArray()['quantity'] }}</span>&nbsp;ITEMS
    </div>
    {{-- <div class="price" x-text="formatCurrency($store.state.cartSubTotal)"></div> --}}
    <div class="price" x-text="$store.state.cartSubTotal">1600</div>
</a>
