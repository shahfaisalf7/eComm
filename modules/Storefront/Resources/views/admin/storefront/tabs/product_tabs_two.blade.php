<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox('storefront_product_tabs_2_section_enabled', trans('storefront::attributes.section_status'), trans('storefront::storefront.form.enable_product_tabs_two_section'), $errors, $settings) }}
        {{ Form::text('translatable[storefront_product_tabs_2_section_title]', trans('storefront::attributes.title'), $errors, $settings) }}

        <div class="clearfix"></div>

        <div class="box-content clearfix">
            <p class="section-title">{{ trans('storefront::storefront.form.tab_1') }}</p>

            {{ Form::text('translatable[storefront_product_tabs_2_section_tab_1_title]', trans('storefront::attributes.title'), $errors, $settings) }}

            @include('storefront::admin.storefront.tabs.partials.products', [
                'fieldNamePrefix' => 'storefront_product_tabs_2_section_tab_1',
                'products' => $tabOneProducts,
            ])
        </div>

        <div class="box-content clearfix">
            <p class="section-title">{{ trans('storefront::storefront.form.tab_2') }}</p>

            {{ Form::text('translatable[storefront_product_tabs_2_section_tab_2_title]', trans('storefront::attributes.title'), $errors, $settings) }}

            @include('storefront::admin.storefront.tabs.partials.products', [
                'fieldNamePrefix' => 'storefront_product_tabs_2_section_tab_2',
                'products' => $tabTwoProducts,
            ])
        </div>

        <div class="box-content clearfix">
            <p class="section-title">{{ trans('storefront::storefront.form.tab_3') }}</p>

            {{ Form::text('translatable[storefront_product_tabs_2_section_tab_3_title]', trans('storefront::attributes.title'), $errors, $settings) }}

            @include('storefront::admin.storefront.tabs.partials.products', [
                'fieldNamePrefix' => 'storefront_product_tabs_2_section_tab_3',
                'products' => $tabThreeProducts,
            ])
        </div>

        <div class="box-content clearfix">
            <p class="section-title">{{ trans('storefront::storefront.form.tab_4') }}</p>

            {{ Form::text('translatable[storefront_product_tabs_2_section_tab_4_title]', trans('storefront::attributes.title'), $errors, $settings) }}

            @include('storefront::admin.storefront.tabs.partials.products', [
                'fieldNamePrefix' => 'storefront_product_tabs_2_section_tab_4',
                'products' => $tabFourProducts,
            ])
        </div>
    </div>
</div>

@include('admin::partials.selectize_remote')
