<style>
    :root {
        --base-font-family: "{{ setting('storefront_display_font', 'Poppins') }}", sans-serif;
        --color-primary: {{ tinycolor($themeColor->toString())->toHexString() }};
        --color-primary-hover: {{ tinycolor($themeColor->toString())->darken(8)->toString() }};
        --color-primary-alpha-10: {{ tinycolor($themeColor->toString())->setAlpha(0.10)->toString() }};
        --color-primary-alpha-12: {{ tinycolor($themeColor->toString())->setAlpha(0.12)->toString() }};
        --color-primary-alpha-15: {{ tinycolor($themeColor->toString())->setAlpha(0.15)->toString() }};
        --color-primary-alpha-30: {{ tinycolor($themeColor->toString())->setAlpha(0.3)->toString() }};
        --color-primary-alpha-80: {{ tinycolor($themeColor->toString())->setAlpha(0.8)->toString() }};
    }
</style>
