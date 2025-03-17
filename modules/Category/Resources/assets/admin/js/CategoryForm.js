import tinyMCE from "@admin/js/wysiwyg"; // Match ProductMixin.js
import CategoryTree from './CategoryTree';

export default class {
    constructor() {
        console.log('CategoryForm Initialized - Timestamp:', Date.now());
        let tree = $('.category-tree');
        new CategoryTree(this, tree);
        this.collapseAll(tree);
        this.expandAll(tree);
        this.addRootCategory();
        this.addSubCategory();
        $('#category-form').on('submit', this.submit);
        window.admin.removeSubmitButtonOffsetOn('#image', '.category-details-tab li > a');
        this.initTinyMCE();
    }

    initTinyMCE() {
        this.textEditor = tinyMCE({
            setup: (editor) => {
                editor.on('change', () => {
                    editor.save(); // Sync content to textarea
                });
            }
        });
    }

    fetchCategory(id) {
        this.loading(true);
        $('.add-sub-category').removeClass('disabled');
        $.ajax({
            type: 'GET',
            url: route('admin.categories.show', id),
            success: (category) => {
                console.log('Fetch Success:', category);
                this.update(category);
                this.loading(false);
            },
            error: (xhr) => {
                error(xhr.responseJSON.message);
                this.loading(false);
            },
        });
    }

    update(category) {
        console.log('Update Called with:', category);
        console.log('Meta Title Input:', $('input[name="meta[meta_title]"]').length);
        console.log('Meta Desc Input:', $('textarea[name="meta[meta_description]"]').length);
        console.log('Meta Title Value:', category.meta_data?.[0]?.meta_title);
        console.log('Meta Desc Value:', category.meta_data?.[0]?.meta_description);

        window.form.removeErrors();
        $('.btn-delete').removeClass('hide');
        $('.form-group .help-block').remove();

        $('#confirmation-form').attr('action', route('admin.categories.destroy', category.id));
        $('#id-field').removeClass('hide');

        $('#id').val(category.id);
        $('#name').val(category.name);
        $('#slug').val(category.slug);
        $('input[name="meta[meta_title]"]').val(category.meta_data?.[0]?.meta_title || '');
        $('textarea[name="meta[meta_description]"]').val(category.meta_data?.[0]?.meta_description || '');
        $('#description').val(category.description || '');

        // Update TinyMCE content if initialized
        if (this.textEditor && this.textEditor.get('description')) {
            this.textEditor.get('description').setContent(category.description || '');
        }

        $('#is_searchable').prop('checked', category.is_searchable);
        $('#is_active').prop('checked', category.is_active);

        $('.logo .image-holder-wrapper').html(this.categoryImage('logo', category.logo));
        $('.banner .image-holder-wrapper').html(this.categoryImage('banner', category.banner));

        $('#category-form input[name="parent_id"]').remove();

        console.log('After Set - Meta Title:', $('input[name="meta[meta_title]"]').val());
        console.log('After Set - Meta Desc:', $('textarea[name="meta[meta_description]"]').val());
        console.log('After Set - Description:', $('#description').val());
    }

    categoryImage(fieldName, file) {
        if (!file.exists) return this.imagePlaceholder();
        return `
            <div class="image-holder">
                <img src="${file.path}">
                <button type="button" class="btn remove-image" data-input-name="files[${fieldName}]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M6.00098 17.9995L17.9999 6.00053" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.9999 17.9995L6.00098 6.00055" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <input type="hidden" name="files[${fieldName}]" value="${file.id}">
            </div>
        `;
    }

    clear() {
        $('#id-field').addClass('hide');
        $('#id').val('');
        $('#name').val('');
        $('#slug').val('');
        $('input[name="meta[meta_title]"]').val('');
        $('textarea[name="meta[meta_description]"]').val('');
        $('#description').val('');
        if (this.textEditor && this.textEditor.get('description')) {
            this.textEditor.get('description').setContent('');
        }
        $('#is_searchable').prop('checked', false);
        $('#is_active').prop('checked', false);
        $('.logo .image-holder-wrapper').html(this.imagePlaceholder());
        $('.banner .image-holder-wrapper').html(this.imagePlaceholder());
        $('.btn-delete').addClass('hide');
        $('.form-group .help-block').remove();
        $('#category-form input[name="parent_id"]').remove();
        $('.general-information-tab a').click();
    }

    imagePlaceholder() {
        return `
            <div class="image-holder placeholder">
                <i class="fa fa-picture-o"></i>
            </div>
        `;
    }

    loading(state) {
        if (state === true) {
            $('.overlay.loader').removeClass('hide');
        } else {
            $('.overlay.loader').addClass('hide');
        }
    }

    submit(e) {
        let selectedId = $('.category-tree').jstree('get_selected')[0];
        if (selectedId !== undefined) {
            window.form.appendHiddenInput('#category-form', '_method', 'PUT');
            $('#category-form').attr('action', route('admin.categories.update', selectedId));
        }
        e.currentTarget.submit();
    }

    collapseAll(tree) {
        $('.collapse-all').on('click', (e) => {
            e.preventDefault();
            tree.jstree('close_all');
        });
    }

    expandAll(tree) {
        $('.expand-all').on('click', (e) => {
            e.preventDefault();
            tree.jstree('open_all');
        });
    }

    addRootCategory() {
        $('.add-root-category').on('click', () => {
            this.loading(true);
            $('.add-sub-category').addClass('disabled');
            $('.category-tree').jstree('deselect_all');
            this.clear();
            setTimeout(this.loading, 150, false);
        });
    }

    addSubCategory() {
        $('.add-sub-category').on('click', () => {
            let selectedId = $('.category-tree').jstree('get_selected')[0];
            if (selectedId === undefined) return;

            this.clear();
            this.loading(true);
            window.form.appendHiddenInput('#category-form', 'parent_id', selectedId);
            setTimeout(this.loading, 150, false);
        });
    }
}
