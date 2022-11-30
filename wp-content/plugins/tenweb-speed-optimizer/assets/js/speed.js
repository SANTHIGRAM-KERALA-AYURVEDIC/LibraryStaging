/* Do request to get optimized images count */
jQuery.ajax({
  type: 'POST',
  url: two_speed.ajax_url,
  dataType: 'json',
  data: {
    action: "two_get_optimized_images",
    nonce: two_speed.nonce,
  }
}).success(function(res){
  let total_images_count, optimized_images_count;
  if( typeof res['data'] != 'undefined' ) {
    total_images_count = res['data']['total_images_count'];
    optimized_images_count = res['data']['optimized_images_count']
    jQuery('.two-adminBar.two_empty_images_count').text(optimized_images_count + ' of ' + total_images_count);
    jQuery('.two-settings-basic.two_empty_images_count').text(optimized_images_count);
  }
});

/* Keeping time interval to check page optimized or not every 3 min*/
var two_is_page_optimized_interval;

jQuery(document).ready(function () {
  /* Add an action to check a page score.*/
  jQuery(".two-notoptimized a").on("click", function () {
    if( typeof jQuery(this).attr("href") != 'undefined' ) {
      return;
    }
    if (two_speed.optimize_entire_website != false) {
      window.open(two_speed.optimize_entire_website + '?two_comes_from=pagesListAfterLimit', '_blank');
    }
    else {
      two_optimize_page(this);
    }
  });

  jQuery("#two_optimize_now_button").on("click", function () {
    two_optimize_page(this);
    /* Run ajax every 3 min to check if page optimized */
    two_is_page_optimized_interval = setInterval( two_is_page_optimized, 180000, this );
  });

  /* Add a hover action to show scores.*/
  jQuery(".two-optimized").hover(function () {
      jQuery(this).parent().parent().find(".two-score-container").removeClass("two-hidden");
    },
    function () {
      jQuery(this).parent().parent().find(".two-score-container").addClass("two-hidden");
    });
  /* Draw circle on given scores.*/
  jQuery('.two-score-circle').each(function () {
    two_draw_score_circle(this);
  });

  /* Show/hide Image optimizer menu content container */
  jQuery("#wp-admin-bar-two_adminbar_info").mouseenter(function(){
      if( jQuery(".two_admin_bar_menu_main_notif").length ) {
        return;
      }
      jQuery(".two_admin_bar_menu_main").removeClass("two_hidden");
  }).mouseleave(function() {
      if( jQuery(".two_admin_bar_menu_main_notif").length ) {
        return;
      }
      jQuery(".two_admin_bar_menu_main").addClass("two_hidden");
  });

  jQuery(".two_clear_cache").on("click", function (e) {
    e.preventDefault();
    two_clear_cache(this);
  });

  jQuery(".two_optimized_cont").on("click", function() {
    if( jQuery(this).find(".two_arrow").hasClass("two_up_arrow") ) {
      jQuery(this).find(".two_score_block_container").addClass("two_hidden");
      jQuery(this).find(".two_arrow").addClass("two_down_arrow").removeClass("two_up_arrow");
    } else {
      jQuery(".two_score_block_container").addClass("two_hidden");
      jQuery(".two_optimized_congrats_row .two_arrow").addClass("two_down_arrow").removeClass("two_up_arrow");
      jQuery(this).find(".two_score_block_container").removeClass("two_hidden");
      jQuery(this).find(".two_arrow").addClass("two_up_arrow").removeClass("two_down_arrow");
    }
  });

  /* Remove inprogress optimize notification popup */
  jQuery('body').on("click", function (e) {
    if (e.target.class != "two_admin_bar_menu_main_notif" && !jQuery(e.target).parents(".two_admin_bar_menu_main_notif").length) {
      jQuery(".two_admin_bar_menu_main_notif").remove();
    }
  });

  jQuery(".two_recount_score").on("click", function() {
    two_recount_score(this);
  });

  jQuery('.two-faq-item').on('click', function () {
    jQuery(this).toggleClass('active');
  });
  jQuery('.two-disconnect-link a').on('click', function () {
    jQuery('.two-disconnect-popup').appendTo('body').addClass('open');
    return false;
  });
  jQuery('.two-button-cancel, .two-close-img').on('click', function () {
    jQuery('.two-disconnect-popup').removeClass('open');
    return false;
  });
});

/* Recount google speed score */
function two_recount_score(that) {
  jQuery(that).addClass("two_loading");
  var post_id = jQuery(that).data("post_id");
  jQuery.ajax({
    type: "POST",
    url: two_speed.ajax_url,
    dataType: 'json',
    data: {
      action: "two_recount_score",
      post_id: post_id,
      nonce: two_speed.nonce,
    }
  }).success(function (results) {
      var data;
      if( typeof results['data'] !== 'undefined' && typeof results['data']['previous_score'] !== 'undefined' ) {
        data = results['data'];
        jQuery(".two_score_success_container .two_score_container_mobile_old .two-score-circle").data("score", data["previous_score"]["mobile_score"]);
        jQuery(".two_score_success_container .two_score_container_mobile_old .two_load_time").text(data["previous_score"]["mobile_tti"] + 's');
        jQuery(".two_score_success_container .two_score_container_desktop_old .two-score-circle").data("score", data["previous_score"]["desktop_score"]);
        jQuery(".two_score_success_container .two_score_container_desktop_old .two_load_time").text(data["previous_score"]["desktop_tti"] + 's');
      }
      if( typeof results['data'] !== 'undefined' && typeof results['data']['current_score'] !== 'undefined' ) {
        data = results['data'];
        jQuery(".two_score_success_container .two_score_container_mobile .two-score-circle").data("score", data["current_score"]["mobile_score"]);
        jQuery(".two_score_success_container .two_score_container_mobile .two_load_time").text(data["current_score"]["mobile_tti"] + 's');
        jQuery(".two_score_success_container .two_score_container_desktop .two-score-circle").data("score", data["current_score"]["desktop_score"]);
        jQuery(".two_score_success_container .two_score_container_desktop .two_load_time").text(data["current_score"]["desktop_tti"] + 's');
        jQuery(".two_score_success_container").removeClass("two_hidden");
        jQuery(".two_home_score_error").remove();
        jQuery('.two-score-circle').each(function() {
          two_draw_score_circle(this);
        });
      }
      jQuery(that).removeClass("two_loading");
  }).error(function (data) {
      jQuery(that).removeClass("two_loading");
  });

}

/* Recount google speed score */
function two_clear_cache(that) {
  jQuery(that).text( two_speed.clearing );
  jQuery(that).prepend("<span class='two_cache_clearing'></span>");
  jQuery.ajax({
    type: "POST",
    url: ajaxurl,
    dataType: 'json',
    data: {
      action: "two_settings",
      task: "clear_cache",
      nonce: two_speed.nonce,
    }
  }).done(function (data) {
      jQuery(".two_cache_clearing").remove();
      if (data.success) {
          jQuery(that).text(two_speed.cleared);
          jQuery(that).addClass("two_cache_cleared");
      } else {
          jQuery(that).text(two_speed.clear);
      }
  }).error(function (data) {
      jQuery(".two_cache_clearing").remove();
      jQuery(that).text(two_speed.clear);
  });
}

/* Checking is page optimized */
function two_is_page_optimized( that ) {
  var post_id = jQuery(that).data("post-id");
  jQuery.ajax({
    url: two_speed.ajax_url,
    type: "POST",
    data: {
      action: "two_is_page_optimized",
      post_id: post_id,
      nonce: two_speed.nonce
    },
    success: function (result) {
      if ( result['success'] ) {
        var res = result['data'];
        clearInterval(two_is_page_optimized_interval);
        jQuery(".two_in_progress_cont").remove();
        jQuery(".two-score-circle_temp").addClass("two-score-circle").removeClass("two-score-circle_temp");
        jQuery(".two_empty_front_optimized_content").addClass("two_optimized ");
        jQuery(".two_empty_front_optimized_content .two_score_container_mobile_old .two-score-circle").attr("data-score", res['previous_score']['mobile_score']);
        jQuery(".two_empty_front_optimized_content .two_score_container_mobile_old .two_score_info .two_load_time").append(res['previous_score']['mobile_tti']+'s');
        jQuery(".two_empty_front_optimized_content .two_score_container_desktop_old .two-score-circle").attr("data-score", res['previous_score']['desktop_score']);
        jQuery(".two_empty_front_optimized_content .two_score_container_desktop_old .two_score_info .two_load_time").append(res['previous_score']['desktop_tti']+'s');

        jQuery(".two_empty_front_optimized_content .two_score_container_mobile .two-score-circle").attr("data-score", res['current_score']['mobile_score']);
        jQuery(".two_empty_front_optimized_content .two_score_container_mobile .two_score_info .two_load_time").append(res['current_score']['mobile_tti']+'s');
        jQuery(".two_empty_front_optimized_content .two_score_container_desktop .two-score-circle").attr("data-score", res['current_score']['desktop_score']);
        jQuery(".two_empty_front_optimized_content .two_score_container_desktop .two_score_info .two_load_time").append(res['current_score']['desktop_tti']+'s');

        jQuery(".two_frontpage_optimizing span").removeClass('two_hidden').remove();
        jQuery(".two_admin_bar_menu_header.two_frontpage_optimizing img").show();
        jQuery(".two_admin_bar_menu_header.two_frontpage_optimizing").addClass("two_frontpage_optimized").removeClass("two_frontpage_optimizing");



        jQuery('.two-score-circle').each(function() {
          two_draw_score_circle(this);
        });
        jQuery(".two_empty_front_optimized_content").removeClass("two_hidden");
      }
    },
    error: function () {},
  });
}

/**
 * Optimize the page.
 * @param that
 */
function two_optimize_page(that) {
  var post_id = jQuery(that).data("post-id");
  var initiator = jQuery(that).data("initiator");
  if ( jQuery(that).attr('id') == 'two_optimize_now_button' ) {
    jQuery(".two_admin_bar_menu_header.two_frontpage_not_optimized img, .two_admin_bar_menu_header.two_frontpage_optimized img").hide();
    jQuery(".two_admin_bar_menu_header.two_frontpage_not_optimized,.two_admin_bar_menu_header.two_frontpage_optimized").removeClass("two_frontpage_not_optimized").addClass("two_frontpage_optimizing");
      jQuery(".two_frontpage_optimizing span").removeClass('two_hidden');
      var two_in_progress_cont = jQuery(".two_in_progress_cont").html();
      jQuery(".two_admin_bar_menu_content.two_not_optimized_content, .two_optimized").empty().append(two_in_progress_cont).addClass("two_in_progress_cont");
  } else if(jQuery(that).attr('class') == "two_optimize_button_elementor") {
      jQuery(".elementor-control-title, .two_elementor_control_container").addClass("two-hidden");
      jQuery(".two-score-section,.two-elementor-container-title").addClass("two-hidden");
      jQuery(".two_elementor_settings_content").addClass("two-optimizing");
      jQuery(".two-page-speed.two-optimizing").removeClass("two-hidden");
  } else {
      var parent = jQuery(that).parent().parent();
      parent.find(".two-optimizing").removeClass("two-hidden");
      parent.find(".two-notoptimized").addClass("two-hidden");
  }
  jQuery.ajax({
    url: two_speed.ajax_url,
    type: "GET",
    data: {
      action: "two_optimize_page",
      post_id: post_id,
      initiator: initiator,
      nonce: two_speed.nonce
    },
    success: function (result) {},
    error: function (xhr, ajaxOptions, thrownError) {
      clearInterval(two_is_page_optimized_interval);
    },
  });
}

/**
 * Draw circle on given score.
 * @param that
 */
function two_draw_score_circle(that) {
  var score = parseInt(jQuery(that).data('score'));
  var size = parseInt(jQuery(that).data('size'));
  var thickness = parseInt(jQuery(that).data('thickness'));
  var color = score <= 49 ? "rgb(253, 60, 49)" : (score >= 90 ? "rgb(12, 206, 107)" : "rgb(255, 164, 0)");
  var background_color = score <= 49 ? "#FD3C311A" : (score >= 90 ? "#22B3391A" : "#fd3c311a");
  if ( jQuery(that).hasClass('two_circle_with_bg') ) {
    jQuery(that).css('background-color',background_color);
  }
  jQuery(that).parent().find('.two-load-time').html(jQuery(that).data('loading-time'));
  var _this = that;
  jQuery(_this).circleProgress({
    value: score / 100,
    size: size,
    startAngle: -Math.PI / 4 * 2,
    lineCap: 'round',
    emptyFill: "rgba(255, 255, 255, 0)",
    thickness: thickness,
    fill: {
      color: color
    }
  }).on('circle-animation-progress', function (event, progress) {
    var content = '<span class="two-score0"></span>';
    if (score != 0) {
      content = Math.round(score * progress);
    }
    jQuery(that).find('.two-score-circle-animated').html(content).css({"color": color});
    jQuery(that).find('canvas').html(Math.round(score * progress));
  });
}

/* Adding button in Elementor edit panel navigation view */
function two_add_elementor_button() {
  window.elementor.modules.layouts.panel.pages.menu.Menu.addItem({
    name: two_speed.title,
    icon: "two-element-menu-icon",
    title: two_speed.title,
    type: "page",
    callback: () => {
      try {
        window.$e.route("panel/page-settings/two_optimize")
      } catch (e) {
        window.$e.route("panel/page-settings/settings"), window.$e.route("panel/page-settings/two_optimize")
      }
    }
  }, "more")
}
/* show 10web Booster button in sidebar only for pages and posts */
if ( (two_speed.post_type == 'page' || two_speed.post_type == 'post') && two_speed.post_status == 'publish') {
  jQuery(window).on("elementor:init", () => {
    window.elementor.on("panel:init", () => {
      setTimeout(two_add_elementor_button)
    })
  });
}