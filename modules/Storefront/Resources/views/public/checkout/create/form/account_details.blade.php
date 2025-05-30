@guest
    <div class="account-details">
        <p class="section-title">{{ trans('storefront::checkout.account_details') }}</p>

        <div class="row">
            <div class="col-md-9">
                <div class="form-group">
                    <label for="email">
                        {{ trans('checkout::attributes.customer_email') }}<span>*</span>
                    </label>

                    <input
                        type="text"
                        name="customer_email"
                        id="customer-email"
                        class="form-control"
                        x-model="form.customer_email"
                    >

                    <template x-if="errors.has('customer_email')">
                        <span class="error-message" x-text="errors.get('customer_email')"></span>
                    </template>
                </div>
            </div>

            <div class="col-md-9">
                <div class="form-group">
                    <label for="phone">
                        {{ trans('checkout::attributes.customer_phone') }}<span>*</span>
                    </label>

                    <input
                        type="text"
                        name="customer_phone"
                        id="phone"
                        class="form-control"
                        x-model="form.customer_phone"
                    >

                    <template x-if="errors.has('customer_phone')">
                        <span class="error-message" x-text="errors.get('customer_phone')"></span>
                    </template>
                </div>
            </div>

            <div class="col-md-18">
                <div class="form-group create-an-account-label">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="create_an_account"
                            id="create-an-account"
                            x-model="form.create_an_account"
                        >

                        <label for="create-an-account" class="form-check-label">
                            {{ trans('checkout::attributes.create_an_account') }}
                        </label>
                    </div>
                </div>

                <div x-cloak x-show="form.create_an_account" class="create-an-account-form">
                    <span class="helper-text">
                        {{ trans('storefront::checkout.create_an_account_by_entering_the_information_below') }}
                    </span>

                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="password">
                                    {{ trans('checkout::attributes.password') }}<span>*</span>
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    x-model="form.password"
                                >

                                <template x-if="errors.has('billing.password')">
                                    <span class="error-message" x-text="errors.get('billing.password')"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <input type="hidden" name="customer_email" x-model="form.customer_email">
@endguest
