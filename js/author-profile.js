jQuery(document).ready(function($){
    let mediaUploader;

    $('#author-profile-image-remove').click(function(){
        $('#author_profile_image_id').val('');
        $('#author-profile-image-preview').attr('src','').hide();
        $(this).hide();
        $('#author-profile-image-upload').text(capProfile.selectLabel);
    });

    $('#author-profile-image-upload').click(function(e){
        e.preventDefault();
        if(mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: capProfile.chooseTitle,
            button: { text: capProfile.selectButton },
            multiple: false
        });

        mediaUploader.on('select', function(){
            let attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#author-profile-image-preview').attr('src', attachment.url).show();
            $('#author_profile_image_id').val(attachment.id);
            $('#author-profile-image-remove').show();
            $('#author-profile-image-upload').text(capProfile.changeLabel);
        });
        mediaUploader.open();
    });
});
