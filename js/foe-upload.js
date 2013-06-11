jQuery(document).ready(function($){
	//Banner Uploader
    var custom_uploader;
    $('#upload_banner_button').click(function(e) {
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Upload or Select Banner Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#foe-banner-image').val(attachment.url);
        });
        //Open the uploader dialog
        custom_uploader.open();
    });
		//Regular Chevron Uploader
	    var custom_uploader2;
    $('#upload_regular_button').click(function(e) {
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader2) {
            custom_uploader2.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader2 = wp.media.frames.file_frame = wp.media({
            title: 'Upload or Select Regular Chevron Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader2.on('select', function() {
            attachment = custom_uploader2.state().get('selection').first().toJSON();
            $('#foe-chevron-image-regular').val(attachment.url);
        });
        //Open the uploader dialog
        custom_uploader2.open();
    });
		//Retina Chevron Uploader
	    var custom_uploader3;
    $('#upload_retina_button').click(function(e) {
        e.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader3) {
            custom_uploader3.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader3 = wp.media.frames.file_frame = wp.media({
            title: 'Upload or Select Retina Chevron Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader3.on('select', function() {
            attachment = custom_uploader3.state().get('selection').first().toJSON();
            $('#foe-chevron-image-retina').val(attachment.url);
        });
        //Open the uploader dialog
        custom_uploader3.open();
    });
});