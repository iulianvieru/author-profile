jQuery(document).ready(function($) {
    function handleUpload(buttonId, previewId, inputId, removeId) {
        var frame;
        $(buttonId).on('click', function(e) {
            e.preventDefault();
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: capMetaBox.selectTitle,
                button: { text: capMetaBox.selectButton },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $(inputId).val(attachment.id);
                $(previewId).html('<img src="' + attachment.url + '" style="max-width:100%; border-radius:6px;" />');
                $(removeId).show();
            });
            frame.open();
        });

        $(removeId).on('click', function(e) {
            e.preventDefault();
            $(inputId).val('');
            $(previewId).html('');
            $(this).hide();
        });
    }

    handleUpload('#cap-upload-desktop', '#cap-blog-header-desktop-preview', '#cap_blog_header_image_desktop', '#cap-remove-desktop');
    handleUpload('#cap-upload-mobile', '#cap-blog-header-mobile-preview', '#cap_blog_header_image_mobile', '#cap-remove-mobile');
});
