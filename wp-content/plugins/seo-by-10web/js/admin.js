jQuery(document).ready(function () {
  if( jQuery(".wdseo-date").length && typeof jQuery.datepicker != undefined ) {
    jQuery(".wdseo-date").datepicker();
  }

  jQuery("#wdseo_404__redirectTo").on("change", function() {
    if( jQuery(this).val() != 2 ) {
      jQuery(".wd-group.wds_custom_url").addClass("wdseo_hidden");
    } else {
      jQuery(".wd-group.wds_custom_url").removeClass("wdseo_hidden");
    }
  });

  if (typeof jQuery(document).tooltip != "undefined") {
        jQuery(document).tooltip({
            show: null,
            items: "[data-wdseo-tooltip-key]",
            content: function () {
                var element = jQuery(this);
                if (element.is("[data-wdseo-tooltip-key]")) {
                    var tooltip_key = element.attr('data-wdseo-tooltip-key');
                    var html = jQuery('#wdseo-tooltip-info-' + tooltip_key).html();
                    return html;
                }
            },
            open: function (event, ui) {
                if (typeof(event.originalEvent) === 'undefined') {
                    return false;
                }
                var $id = jQuery(ui.tooltip).attr('id');
                // close any lingering tooltips
                jQuery('div.ui-tooltip').not('#' + $id).remove();
            },
            close: function (event, ui) {
                ui.tooltip.hover(function () {
                        jQuery(this).stop(true).fadeTo(400, 1);
                    },
                    function () {
                        jQuery(this).fadeOut('400', function () {
                            jQuery(this).remove();
                        });
                    });
            },
            position: {
                my: "center top+30",
                at: "center top",
                using: function (position, feedback) {
                    jQuery(this).css(position);
                    jQuery("<div>")
                        .addClass("tooltip-arrow")
                        .addClass(feedback.vertical)
                        .addClass(feedback.horizontal)
                        .appendTo(this);
                }
            }
        });
    }

    change_post_type("." + jQuery("select[name='wd_settings[types]']").val());

    // Add onchange event to page types list.
    jQuery("select[name='wd_settings[types]']").on('change', function () {
        change_post_type("." + jQuery(this).val());
    });

    // Select given object text.
    jQuery(".wdseo_form").on("click", ".wd-select-all", wdseo_selectText);

    // Select box.
    if (jQuery("#wd-exclude-post-types, #wd-exclude-taxonomies, #wd-exclude_archive").length > 0) {
        jQuery("#wd-exclude-post-types, #wd-exclude-taxonomies, #wd-exclude_archive").select2({
            closeOnSelect: false,
        });
    }
  if (jQuery(".wd-select2").length > 0) {
    jQuery(".wd-select2").each(function () {
      jQuery(this).select2({
        tags: true,
        selectOnClose: true,
        width: '100%',
        dropdownParent: jQuery(this).parent(),
        language: {
          noResults: function () {
            return "Please enter a non duplicate correct URL";
          }
        },
        matcher: matchCustom,
        createTag: function (term) {
          var value = term.term;
          if (jQuery(this)[0].$element[0].classList.contains('nofollow_external_urls') && !validateUrl(value)) {
            return null;
          }
          return {
            id: value,
            text: value
          };
        },
      });
    });
  }

  function matchCustom(params, data) {
    if (params.term === data.text) {
      return params.term = null; // prevent duplicate
    }
    return null; //prevent search
  }

  function validateUrl(url) {
    var re = /[-a-zA-Z0-9@:%_\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/;
    return re.test(url);
  }

    if (jQuery("select[name='country']").length > 0) {
        jQuery("select[name='country']").select2({
            tags: false,
            selectOnClose: true,
        });
    }

    if (jQuery("select[name='date']").length > 0) {
        jQuery("select[name='date']").select2({
            tags: false,
            selectOnClose: true,
            minimumResultsForSearch: -1
        });
    }

    // Add tabs.
    jQuery(".wdseo_tabs").each(function () {
        jQuery(this).tabs({
            active: jQuery('#active_tab').val(),
            activate: function( event, ui ) {
                jQuery('#active_tab').val(ui.newTab.index());
            }
        });
    });

    // Set no items row width.
    jQuery(".colspanchange").attr("colspan", jQuery(".wdseo_form table.adminlist>thead>tr>th:visible").length);

    // Show/hide twitter fields.
    jQuery('.wd-use-twitter').on("click", function () {
        wdseo_show_hide_elements(jQuery(this).is(':checked'), jQuery(this).closest('.wdseo-section').find('.wd-twitter-field'));
        wdseo_show_hide_elements(jQuery(this).is(':checked'), jQuery('.wd-box-section.wd-twitter-field'));
    });

	jQuery('.wd-use-twitter').each(function () {
        wdseo_show_hide_elements(jQuery(this).is(':checked'), jQuery(this).closest('.wdseo-section').find('.wd-twitter-field'));
    });

    // Set placeholders.
    wdseo_set_placeholder();

    // Set preview.
    wdseo_add_preview_event();

    // Change notice status.
    jQuery("body").on("click", "button.notice-dismiss", function() {
        change_notice_status(this);
    });

    // Add search event to search input.
    jQuery("input[name='s']").on("keypress", function (event) {
        var key_code = (event.keyCode ? event.keyCode : event.which);
        if (key_code == 13) {
            wdseo_search();
            return false;
        }
    });

    // Show/hide Google Authenticate button depend on Authorization Code input value.
    jQuery(".wd-group input[name='code']").on("click change keyup", function () {
        if ( jQuery(this).val() != "" ) {
            jQuery(".authenticate-btn").removeClass("wdseo-hide");
        }
        else {
            jQuery(".authenticate-btn").addClass("wdseo-hide");
        }
    });

    /* Show hide image optimizer tooltip */
    jQuery(".free, .free_tooltip").mouseenter(function() {
        jQuery(this).find(".free_tooltip").show();
    }).mouseleave(function() {
        jQuery(this).find(".free_tooltip").hide();
    });

    /* Add onclick event to tag button for robots.txt */
    jQuery(".wd-group .tag-title").on("click", function (event) {
        var tag = jQuery(this).attr("data-tag");
        jQuery("#wd-robots_file").val(jQuery("#wd-robots_file").val() + '\n' + tag);
    });

    /* Hide/Show meta options when Disable metabox is Disable/Enable  */
    disable_metabox();
    jQuery(".wd-group .disable_metabox").on("change", function (event,handler) {
        disable_metabox(jQuery(this));
    });

    /* Hide/Show video details  */
    jQuery("body").on("click", "h3.wd-block-header", function () {
        if (jQuery(this).hasClass("active"))  {
            jQuery(this).removeClass("active");
            jQuery(this).next(".wd-block-container").slideUp("fast");
        }
        else {
            jQuery("h3.wd-block-header").removeClass("active");
            jQuery(".wd-block-container").slideUp("fast");
            jQuery(this).addClass("active");
            jQuery(this).next(".wd-block-container").slideDown("fast");
        }
    })

    /* Add video block  */
    jQuery("#wdseo_add-video").click(function () {
        var number = parseInt(jQuery("#wdseo_video_count").val());
        var video_item = jQuery(".wd-block").eq(0).clone();
        video_item.find("input, textarea").each(function() {
            var type = jQuery(this).attr("type");
            var name = jQuery(this).attr("name");
            jQuery(this).attr("name", name.replace("wd_settings[wdseo_video][0]", "wd_settings[wdseo_video][" + (number + 1 )+ "]"))
            if (type == "checkbox") {
                jQuery(this).removeAttr("checked");
            }
            else {
                jQuery(this).val("");
            }

        });
        jQuery("#video-block").append(video_item);
        jQuery("#wdseo_video_count").val(number + 1);
        return false;
    });

    /* Remove video block  */
    jQuery("body").on("click", ".wdseo_remove-video", function () {
        var number = parseInt(jQuery("#wdseo_video_count").val());
        jQuery(this).closest(".wd-block").remove();
        jQuery("#wdseo_video_count").val(number - 1);
        return false;
    });

    /* Flush permalinks  */
    jQuery('#wdseo_flush_permalinks').on('click', function(e) {
        jQuery.ajax({
            method : 'GET',
            url : wdseo.flush_permalinks,
            data : {
                action: 'wdseo_flush_permalinks',
                nonce_wdseo: jQuery("#" + wdseo.nonce).val()
            },
            success : function( data ) {
                window.location.reload(true);
            }
        });

    });

    jQuery("body").on('click', '.wdseo_select_image', function (e) {
        e.preventDefault();
        var frame = false;
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Select or Upload Media Of Your Chosen Persuasion',
            button: {
                text: 'Use this media'
            },
            multiple: false
        });

        var $img = jQuery(this).closest('.wd-group').find('.wdseo_image');
        var $img_id = jQuery(this).closest('.wd-group').find('.wdseo_image_url');

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            $img.attr('src', attachment.url);
            $img_id.val(attachment.url);
        });
        frame.open();
        return false;
    });

  jQuery("#wdseo_yoast_imoprt").on("click",function (e) {
    e.preventDefault();
    self.import_offset( 0, self );
  });
  import_offset = function( offset, self ) {
    var wdseo_ajax_nonce = jQuery("#wdseo_ajax_nonce").val();
    jQuery('.wdseo_success_msg').hide();
    jQuery(".wdseo_loading").css("display", "inline-block");
    jQuery.ajax({
      method : 'POST',
      url : ajaxurl,
      data : {
        action: 'wdseo_import',
        task: 'import_yoast_post_meta',
        wdseo_offset: offset,
        nonce_wdseo: wdseo_ajax_nonce,
      },
      success : function( data ) {
        if( 'done' == data.data.offset ) {
          import_yoast_global_settings();
        } else {
          self.import_offset( parseInt( data.data.offset ), self );
        }
      },
      error : function() {
        var msg = jQuery('.wdseo_success_msg');
        msg.removeClass('updated');
        msg.addClass('error');
        msg.find('strong').text(wdseo.error_msg);
        msg.css("display","inline-block");

        jQuery(".wdseo_loading").hide();
      }
    });
  };

  // Show / Hide 'Redirect URL' field on redirect page.
  wdseo_change_redirect_client_error_type();
  wdseo_change_pageurl_to_regex();

  // Show / Hide Nofollow external URL ignore input
  jQuery('input:radio[name="wd_settings[wdseo_nofollow_external_urls_global][wdseo_nofollow_external_urls_global_enable]"]', ).change(function () {
      jQuery('#wdseo-nofollow_url_global_container').toggleClass('wdseo-hide')
  });
});

function import_yoast_global_settings() {
  var wdseo_ajax_nonce = jQuery("#wdseo_ajax_nonce").val();
  jQuery.ajax({
    method : 'POST',
    url : ajaxurl,
    data : {
      action: 'wdseo_import',
      task: 'import_yoast_global_settings',
      nonce_wdseo: wdseo_ajax_nonce,
    },
    success : function( data ) {
      jQuery('.wdseo_success_msg').css("display","inline-block");
      jQuery(".wdseo_loading").hide();
    },
    error : function() {
      var msg = jQuery('.wdseo_success_msg');
      msg.removeClass('updated');
      msg.addClass('error');
      msg.find('strong').text(wdseo.error_msg);
      msg.css("display","inline-block");

      jQuery(".wdseo_loading").hide();
    }
  });

}

/*
* Hide or Show meta options
* */
function disable_metabox(el) {
    if (typeof el == "undefined")
        el = jQuery(".wd-box-section.wd-type:visible").find(".disable_metabox:checked");
    var value = el.val();
    var parent = el.closest(".wd-group");
    var parentCol = el.closest(".wd-table-col");
    if (value == 0) {
        parent.siblings().hide();
        parentCol.siblings().css("visibility","hidden");
    }
    else {
        parent.siblings().show();
        parentCol.siblings().css("visibility","visible");
    }
}

/**
 * Show parameters for given type.
 *
 * @param obj
 */
function change_post_type(obj) {
    jQuery(".wd-type").hide();
    jQuery(obj).show();

    wdseo_show_hide_elements(jQuery(obj).find('.wdseo-social').length == 0, jQuery('.wd-social-section'));
    // Show/hide twitter preview.
    wdseo_show_hide_elements(jQuery(obj).find('.wd-use-twitter').length == 0 || jQuery(obj).find('.wd-use-twitter').is(':checked'), jQuery('.wd-box-section.wd-twitter-field'));

    // Set preview.
    wdseo_add_preview_event();

    /* Hide/Show meta options when Disable metabox is Disable/Enable  */
    disable_metabox();
}
/**
 * Set href for given thickbox class.
 *
 * @param that
 */
function set_thickbox_href(that, event) {
    // Container id which content will be shown in popup.
    var inlineId = jQuery(that).attr("data-inlineId");

    // URL will be redirected.
    var url = jQuery(that).attr("data-url");
    var url_input = jQuery("#url");
    url_input.val(url);
    if ( '' != url ) {
        url_input.attr('disabled', 'disabled');
    }
    else {
        url_input.removeAttr('disabled');
    }
    var redirect_url = jQuery(that).attr("data-redirect-url");
    jQuery("#redirect_to").val(redirect_url);

    var width = Math.min(jQuery(that).attr("data-width"), jQuery(window).width() - 30);
    // Get container height if height is not set.
    var height = jQuery(that).attr("data-height") != "" ? jQuery(that).attr("data-height") : jQuery("#" + inlineId).height() + 15;
    height = Math.min(height, jQuery(window).height() - 50);

    jQuery(that).attr("href", "#TB_inline&width=" + width + "&height=" + height + "&inlineId=" + inlineId);
}

/**
 * Create redirect
 */
function create_redirect() {
    var url = jQuery("input[name='url']").val();
    var redirect_url = jQuery("input[name='redirect_to']").val();

    execute_task('create_redirect', url, redirect_url);
}

/**
 * Delete redirect.
 *
 * @param that
 * @param event
 */
function delete_redirect(that, event) {
    var url = jQuery(that).attr("data-url");
    var redirect_url = jQuery(that).attr("data-redirect-url");

    execute_task('delete_redirect', url, redirect_url);
}

/**
 * Submit form to save/delete redirect URL.
 */
function execute_task(task, url, redirect_url) {
    var form = jQuery("form[name='wdseo_form']");
    jQuery("<input />").attr("type", "hidden")
        .attr("name", "url")
        .attr("value", url)
        .appendTo(form);
    jQuery("<input />").attr("type", "hidden")
        .attr("name", "redirect_url")
        .attr("value", redirect_url)
        .appendTo(form);
    jQuery("<input />").attr("type", "hidden")
        .attr("name", "task")
        .attr("value", task)
        .appendTo(form);
    form.submit();
}

/**
 * Mark as fixed.
 *
 * @param that
 */
function mark_as_fixed(that, event) {
    var url = jQuery(that).attr("data-url");
    var form = jQuery("form[name='wdseo_form']");
    jQuery("<input />").attr("type", "hidden")
        .attr("name", "url")
        .attr("value", url)
        .appendTo(form);
    jQuery("<input />").attr("type", "hidden")
        .attr("name", "task")
        .attr("value", "mark_as_fixed")
        .appendTo(form);
    form.submit();
}

/**
 * Filter.
 */
function wdseo_filter(that) {
    var form = jQuery("form[name='wdseo_form']");

    form.attr("action", window.location + "&" + jQuery(that).attr("name") + "=" + jQuery(that).val());

    form.submit();
}

/**
 * Search.
 */
function wdseo_search() {
    var form = jQuery("form[name='wdseo_form']");

    form.attr("action", window.location + "&paged=1&s=" + jQuery("input[name='s']").val());

    form.submit();
}

/**
 * Change notice status.
 *
 * @param key
 */
function change_notice_status(that) {
    var post_data = {};
    post_data['task'] = 'dismiss';
    post_data['key'] = jQuery(that).parent(".notice").data("value");
    post_data[wdseo.nonce] = jQuery("#" + wdseo.nonce).val();

    jQuery.post(
        window.location,
        post_data,
        function (data, textStatus, errorThrown) {
            location.reload();
        });
}

/**
 * Bulk actions.
 *
 * @param that
 */
function wdseo_bulk_action(that) {
  var form = jQuery(that).parents("form");
  var action = jQuery("select[name='bulk_action']").val();
  if (action != -1) {
    if (!jQuery("input[name^='check']").is(':checked')) {
      alert(wdseo.select_at_least_one_item);
      return;
    }
    if (action == 'delete') {
      if (!confirm(wdseo.delete_confirmation)) {
        return false;
      }
    }

	var task_input = jQuery("input[name='task']");
	if( ! task_input.length ) {
		jQuery("<input />")
		.attr("type", "hidden")
        .attr("name", "task")
		.appendTo(form);
	}
    jQuery("input[name='task']").val(action);
    form.submit();
  }
}

function wdseo_redirect_form(that) {
  var form = jQuery(that).parents("form");
  var inputs = jQuery('.wdseo_form input[type=text]:enabled');
  var error = false;
  jQuery('.wdseo_form select[name=redirect_type]').removeClass('wdseo-required-field');
  jQuery('.wdseo-field-error').remove();
  inputs.removeClass('wdseo-required-field');
  var redirect_type_val = jQuery('.wdseo_form select[name=redirect_type] option:selected').val();
  if ( redirect_type_val == -1) {
    error = true;
    jQuery('.wdseo_form select[name=redirect_type]').addClass('wdseo-required-field');
    wdseo_set_form_required_field(jQuery('.wdseo_form select[name=redirect_type]').parents('.wd-group'));
  }
  inputs.each(function (i, v) {
    if (jQuery(v).val() == null || jQuery(v).val() == '') {
      error = true;
      jQuery(v).addClass('wdseo-required-field');
      wdseo_set_form_required_field(jQuery(v).parents('.wd-group'));
    }
  });
  if (jQuery('#url').val().trim() === jQuery('#redirect_to').val().trim()){
    error = true;
    wdseo_form_same_field(jQuery('#url').parents('.wd-group'));
    wdseo_form_same_field(jQuery('#redirect_to').parents('.wd-group'));
  }
  if (error == false) {
    form.submit();
  }
}

function wdseo_set_form_required_field(append) {
  jQuery("<span/>")
    .attr('class', 'wdseo-field-error')
    .text(wdseo.the_field_is_required)
    .appendTo(append);
}

function wdseo_form_same_field(append) {
  jQuery("<span/>")
    .attr('class', 'wdseo-field-error')
    .text(wdseo.same_page_redirect_URL)
    .appendTo(append);
}

function wdseo_change_redirect_client_error_type() {
  var val = jQuery('#redirect_type option:selected').val();
  var redirect_to = jQuery('#redirect_to');
  var redirect_query_parameters = jQuery('#redirect_query_parameters');
  var regexVal = jQuery('#regex_enable:checked').val() || 0;
  redirect_to.closest('.wd-group').hide();
  redirect_to.prop('disabled', true);
  redirect_query_parameters.closest('.wd-group').hide();
  redirect_query_parameters.prop('disabled', true);
  if (val < 400) {
    redirect_to.prop('disabled', false);
    redirect_to.closest('.wd-group').show();
    if (regexVal == 0) {
      redirect_query_parameters.prop('disabled', false);
      redirect_query_parameters.closest('.wd-group').show();
    }
  }
}

function wdseo_change_pageurl_to_regex() {
  var val = jQuery('#redirect_type option:selected').val();
  var regexVal = jQuery('#regex_enable:checked').val();
  var redirect_query_parameters = jQuery('#redirect_query_parameters');
  var regex_cheat_sheet = jQuery('#regex-cheat-sheet');
  if (regexVal) {
    regex_cheat_sheet.removeClass("wdseo-hidden");
  }
  else {
    regex_cheat_sheet.addClass("wdseo-hidden");
  }
  if (regexVal > 0 || val >= 400) {
    redirect_query_parameters.prop('disabled', true);
    redirect_query_parameters.closest('.wd-group').hide();
  }
  else {
    redirect_query_parameters.prop('disabled', false);
    redirect_query_parameters.closest('.wd-group').show();
  }
}

function wdseo_remove_additional_page(that) {
  jQuery(that).parents('tr').remove();
  wdseo_not_additional_pages();
}

function wdseo_add_new_page() {
  var changeFreqVals = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
  var changeFreqNames = ['Always', 'Hourly', 'Daily', 'Weekly', 'Monthly', 'Yearly', 'Never'];
  var priorities = [0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0];
  var table = document.getElementById('wdseo_additional_pages').getElementsByTagName('TBODY')[0];
  var ce = function (ele) {
    return document.createElement(ele)
  };
  var tr = ce('TR');
  var td = ce('TD');
  var iUrl = ce('INPUT');
  iUrl.type = "text";
  iUrl.style.width = '100%';
  iUrl.name = "wd_settings[additional_pages][page_url][]";
  td.appendChild(iUrl);
  tr.appendChild(td);
  td = ce('TD');
  td.style.width = '45px';
  var iPrio = ce('SELECT');
  iPrio.style.width = '100%';
  iPrio.name = "wd_settings[additional_pages][priority][]";
  for (var i = 0; i < priorities.length; i++) {
    var op = ce('OPTION');
    op.text = priorities[i];
    op.value = priorities[i];
    try {
      iPrio.add(op, null); // standards compliant; doesn't work in IE
    } catch (ex) {
      iPrio.add(op); // IE only
    }
  }
  td.appendChild(iPrio);
  tr.appendChild(td);
  td = ce('TD');
  td.style.width = '75px';
  var iFreq = ce('SELECT');
  iFreq.name = "wd_settings[additional_pages][frequency][]";
  iFreq.style.width = '100%';
  for (var i = 0; i < changeFreqVals.length; i++) {
    var op = ce('OPTION');
    op.text = changeFreqNames[i];
    op.value = changeFreqVals[i];
    try {
      iFreq.add(op, null); // standards compliant; doesn't work in IE
    } catch (ex) {
      iFreq.add(op); // IE only
    }
  }
  td.appendChild(iFreq);
  tr.appendChild(td);
  var td = ce('TD');
  td.style.width = '100px';
  var iChanged = ce('INPUT');
  iChanged.type = "text";
  iChanged.name = "wd_settings[additional_pages][last_changed][]";
  iChanged.classList.add("wdseo-date");
  iChanged.style.width = '100%';
  td.appendChild(iChanged);
  jQuery(iChanged). datepicker({
    dateFormat: 'yy-mm-dd'
  });
  tr.appendChild(td);
  var td = ce('TD');
  td.style.textAlign = "center";
  td.style.width = '5px';
  var iAction = ce('A');
  iAction.className = 'dashicons dashicons-trash';
  iAction.innerHTML = '';
  iAction.href = "javascript:void(0);";
  iAction.onclick = function () {
    table.removeChild(tr);
    wdseo_not_additional_pages();
  };
  td.appendChild(iAction);
  tr.appendChild(td);
  var firstRow = table.getElementsByTagName('TR')[1];
  if (firstRow) {
    var firstCol = (firstRow.childNodes[1] ? firstRow.childNodes[1] : firstRow.childNodes[0]);
    if (firstCol.colSpan > 1) {
      firstRow.parentNode.removeChild(firstRow);
    }
  }
  var cnt = table.getElementsByTagName('TR').length;
  if (cnt % 2) {
    tr.className = "alternate";
  }
  table.appendChild(tr);
  if (jQuery('#wdseo_additional_pages .alternate').length) {
    jQuery('#not_additional_pages').val(0);
  }
}

function wdseo_not_additional_pages() {
  if (jQuery('#wdseo_additional_pages .alternate').length == 0) {
    var table = document.getElementById('wdseo_additional_pages').getElementsByTagName('TBODY')[0];
    var additional_page = document.createElement('INPUT');
    additional_page.type = "hidden";
    additional_page.name = "wd_settings[additional_pages][]";
    table.appendChild(additional_page);
  }
}
