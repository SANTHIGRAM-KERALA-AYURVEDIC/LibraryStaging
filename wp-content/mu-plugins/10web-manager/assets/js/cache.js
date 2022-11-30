function tenwebCachePurge() {

    var data = {};
    data.action = 'tenweb_cache_purge_all';

    jQuery("#error_response").hide();
    jQuery.ajax({
        type: "POST",
        //  dataType: 'json',
        url: tenweb.ajaxurl,
        data: data,
        success: function (response) {
            var response = JSON.parse(response);
            if (typeof response.error != "undefined") {
                jQuery('#tenweb_cache_message').removeClass('hidden').addClass('error').html('<p>' + response.error + '</p>')
            } else {
                jQuery('#tenweb_cache_message').removeClass('hidden').addClass('success').html('<p>' + response.message + '</p>')
            }
        },
        failure: function (errorMsg) {
            console.log('Failure' + errorMsg);
        },
        error: function (error) {
            console.log(error);
        }
    });
}


function tenwebCachePurgeDropdown() {

    var data = {};
    data.action = 'tenweb_cache_purge_all';
    jQuery.ajax({
        type: "POST",
        //  dataType: 'json',
        url: tenweb.ajaxurl,
        data: data,
        success: function (response) {
            var response = JSON.parse(response);
            if (typeof response.error != "undefined") {
                jQuery('#tenweb_cache_dropdown_message').removeClass('hidden').addClass('error');
                jQuery('#tenweb_cache_dropdown_message p').html(response.error);
            } else {
                jQuery('#tenweb_cache_dropdown_message').removeClass('hidden').addClass('success');
                jQuery('#tenweb_cache_dropdown_message p').html(response.message);
            }
            jQuery("#my-dismiss-admin-message").click(function(event) {
                event.preventDefault();
                jQuery('#tenweb_cache_dropdown_message').addClass("hidden");
            });
        },
        failure: function (errorMsg) {
            console.log('Failure' + errorMsg);
        },
        error: function (error) {
            console.log(error);
        }
    });
}

function tenwebCloudflareCachePurge() {

    var data = {};
    data.action = 'tenweb_cache_purge_cloudflare';
    jQuery.ajax({
        type: "POST",
        url: tenweb.ajaxurl,
        data: data,
        success: function (response) {
            var response = JSON.parse(response);
            if (typeof response.error != "undefined") {
                jQuery('#tenweb_cache_dropdown_message').removeClass('hidden').addClass('error');
                jQuery('#tenweb_cache_dropdown_message p').html(response.error);
            } else {
                jQuery('#tenweb_cache_dropdown_message').removeClass('hidden').addClass('success');
                jQuery('#tenweb_cache_dropdown_message p').html(response.message);
            }
            jQuery("#my-dismiss-admin-message").click(function(event) {
                event.preventDefault();
                jQuery('#tenweb_cache_dropdown_message').addClass("hidden");
            });
        },
        failure: function (errorMsg) {
            console.log('Failure' + errorMsg);
        },
        error: function (error) {
            console.log(error);
        }
    });
}

function tenwebClearSOCache() {

    var data = {};
    data.action = 'tenweb_cache_purge_optimizer';
    jQuery.ajax({
        type: "POST",
        url: tenweb.ajaxurl,
        data: data,
        success: function (response) {

            var response = JSON.parse(response);
            if (typeof response.error != "undefined") {
                jQuery('#tenweb_cache_dropdown_message').removeClass('hidden').addClass('error');
                jQuery('#tenweb_cache_dropdown_message p').html(response.error);
            } else {
                jQuery('#tenweb_cache_dropdown_message').removeClass('hidden').addClass('success');
                jQuery('#tenweb_cache_dropdown_message p').html(response.message);
            }
            jQuery("#my-dismiss-admin-message").click(function(event) {
                event.preventDefault();
                jQuery('#tenweb_cache_dropdown_message').addClass("hidden");
            });
        },
        failure: function (errorMsg) {
            console.log('Failure' + errorMsg);
        },
        error: function (error) {
            console.log(error);
        }
    });
}

function tenwebClearAllCache() {

    var data = {};
    data.action = 'tenweb_cache_clear_all';
    jQuery.ajax({
        type: "POST",
        url: tenweb.ajaxurl,
        data: data,
        success: function (response) {
            console.log(response);
            var response = JSON.parse(response);
            if (typeof response.error != "undefined") {
                jQuery('#tenweb_cache_dropdown_message').removeClass('hidden').addClass('error');
                jQuery('#tenweb_cache_dropdown_message p').html(response.error);
            } else {
                jQuery('#tenweb_cache_dropdown_message').removeClass('hidden').addClass('success');
                jQuery('#tenweb_cache_dropdown_message p').html(response.so + '<br/>' + response.tenweb + '<br/>' + response.cloudflare);
            }
            jQuery("#my-dismiss-admin-message").click(function(event) {
                event.preventDefault();
                jQuery('#tenweb_cache_dropdown_message').addClass("hidden");
            });
        },
        failure: function (errorMsg) {
            console.log('Failure' + errorMsg);
        },
        error: function (error) {
            console.log(error);
        }
    });
}