jQuery(document).ready(function($) {
    var mediaUploader;

    $('#upload_additional_product_image').on('click', function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media({
            title: 'Select Product Image',
            button: { text: 'Use this image' },
            multiple: false
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#_additional_product_image').val(attachment.id);
            $('#additional_product_image_preview').attr('src', attachment.url).show();
            $('#remove_additional_product_image').show();
        });
        mediaUploader.open();
    });

    $('#remove_additional_product_image').on('click', function(e) {
        e.preventDefault();
        $('#_additional_product_image').val('');
        $('#additional_product_image_preview').hide();
        $(this).hide();
    });
});
