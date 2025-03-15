<template v-else-if="section === 'seo'">
    <div class="box-header">
        <p>{{ trans('product::products.group.seo') }}</p>

        <div class="drag-handle">
            <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
            <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
        </div>
    </div>

    <div class="box-body">
        <div class="form-group">
            <label for="slug" class="col-sm-12 control-label text-left">
                {{ trans('product::attributes.slug') }}
                <span v-if="route().current('admin.products.edit')" class="text-red">*</span>
            </label>

            <div class="col-sm-12">
                <input type="text" name="slug" id="slug" class="form-control" @change="setProductSlug($event.target.value)" v-model="form.slug">

                <span class="help-block text-red" v-if="errors.has('slug')" v-text="errors.get('slug')"></span>
            </div>
        </div>

        <div class="form-group">
            <label for="meta-title" class="col-sm-12 control-label text-left">
                {{ trans('meta::attributes.meta_title') }}
            </label>

            <div class="col-sm-12">
                <input type="text" name="meta.meta_title" id="meta-title" class="form-control" v-model="form.meta.meta_title">

                <span class="help-block text-red" v-if="errors.has('meta.meta_title')" v-text="errors.get('meta.meta_title')"></span>
            </div>
        </div>

        <div class="form-group">
            <label for="meta-description" class="col-sm-12 control-label text-left">
                {{ trans('meta::attributes.meta_description') }}
            </label>

            <div class="col-sm-12">
                <textarea name="meta.meta_description" rows="6" cols="10" id="meta-description" class="form-control" v-model="form.meta.meta_description"></textarea>

                <span class="help-block text-red" v-if="errors.has('meta.meta_description')" v-text="errors.get('meta.meta_description')"></span>
            </div>
        </div>
    </div>
</template>
<script>
    $(document).ready(function() {
        console.log('jQuery loaded:', typeof $ !== 'undefined' ? 'Yes' : 'No');
        console.log('Description exists:', $('#description').length ? 'Yes' : 'No');
        console.log('Meta description exists:', $('textarea[name="meta.meta_description"]').length ? 'Yes' : 'No');

        $('#description').on('input', function() {
            console.log('Description input triggered');
            var tempDiv = $('<div>').html($(this).val());
            var plainText = tempDiv.text().replace(/\u00A0/g, ' ');
            var metaText = plainText.substring(0, 150);
            console.log('Plain text:', metaText);
            var metaDesc = $('textarea[name="meta.meta_description"]');
            metaDesc.val(metaText).trigger('input');
            // Sync Vue with name-based key
            if (window.vueInstance && window.vueInstance.form && window.vueInstance.form.meta) {
                console.log('Syncing Vue form.meta.meta_description');
                Vue.set(window.vueInstance.form.meta, 'meta_description', metaText);
            }
        });
    });
</script>
