<div class="row">
    <div class="col-md-8">
        {{ Form::text('translatable[storefront_welcome_text]', trans('storefront::attributes.storefront_welcome_text'), $errors, $settings) }}
        {{ Form::text('translatable[storefront_discount_text]', trans('storefront::attributes.storefront_discount_text'), $errors, $settings) }}

        {{ Form::select('storefront_display_font', trans('storefront::attributes.storefront_display_font'), $errors, $display_fonts, $settings) }}

        {{ Form::select('storefront_theme_color', trans('storefront::attributes.storefront_theme_color'), $errors, trans('storefront::themes'), $settings) }}

        <div class="{{ old('storefront_theme_color', array_get($settings, 'storefront_theme_color')) === 'custom_color' ? '' : 'hide' }}"
            id="custom-theme-color">
            {{ Form::color('storefront_custom_theme_color', trans('storefront::attributes.storefront_custom_theme_color'), $errors, $settings) }}
        </div>

        {{ Form::select('storefront_mail_theme_color', trans('storefront::attributes.storefront_mail_theme_color'), $errors, trans('storefront::themes'), $settings) }}

        <div class="{{ old('storefront_mail_theme_color', array_get($settings, 'storefront_mail_theme_color')) === 'custom_color' ? '' : 'hide' }}"
            id="custom-mail-theme-color">
            {{ Form::color('storefront_custom_mail_theme_color', trans('storefront::attributes.storefront_custom_mail_theme_color'), $errors, $settings) }}
        </div>

        {{ Form::select('storefront_slider', trans('storefront::attributes.storefront_slider'), $errors, $sliders, $settings) }}
        {{ Form::select('storefront_terms_page', trans('storefront::attributes.storefront_terms_page'), $errors, $pages, $settings) }}
        {{ Form::select('storefront_privacy_page', trans('storefront::attributes.storefront_privacy_page'), $errors, $pages, $settings) }}
        {{ Form::textarea('translatable[storefront_address]', trans('storefront::attributes.storefront_address'), $errors, $settings, ['rows' => 5]) }}
        {{ Form::checkbox('storefront_most_searched_keywords_enabled', trans('storefront::attributes.storefront_most_searched_keywords'), trans('storefront::storefront.form.enable_most_searched_keywords'), $errors, $settings) }}
    </div>
</div>
