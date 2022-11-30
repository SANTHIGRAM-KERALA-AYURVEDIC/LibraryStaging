<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeometaboxView {
  /**
   * Display meta box.
   */
  public static function display($options, $options_defaults) {
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script(WD_SEO_PREFIX . '_common');
    wp_enqueue_script(WD_SEO_PREFIX . '_wdseo');
    wp_enqueue_script(WD_SEO_PREFIX . '_admin');
    wp_enqueue_style(WD_SEO_PREFIX . '_admin');
    wp_enqueue_style(WD_SEO_PREFIX . '_select2');
    wp_enqueue_script(WD_SEO_PREFIX . '_select2');
    wp_enqueue_media();
    $current_url = esc_url($options->url);
    $screen = get_current_screen();
    $redirect_types = WD_SEO_Library::get_redirect_types();
    ob_start();
    ?>
    <div class="wdseo_tabs">
      <ul class="wdseo-tabs">
        <li class="tabs">
          <a href="#wdseo_tab_keywords_content" class="wdseo-tablink"><?php _e('Keywords', WD_SEO_PREFIX); ?></a>
        </li>
        <li class="tabs">
          <a href="#wdseo_tab_settings_content" class="wdseo-tablink"><?php _e('Settings', WD_SEO_PREFIX); ?></a>
        </li>
        <li class="tabs">
          <a href="#wdseo_tab_opengraph_content" class="wdseo-tablink"><?php _e('Facebook / OpenGraph', WD_SEO_PREFIX); ?></a>
        </li>
        <li class="tabs">
          <a href="#wdseo_tab_twitter_content" class="wdseo-tablink"><?php _e('Twitter', WD_SEO_PREFIX); ?></a>
        </li>
        <li class="tabs">
          <a href="#wdseo_tab_faq_content" class="wdseo-tablink"><?php _e('FAQ', WD_SEO_PREFIX); ?></a>
        </li>
        <?php if($options->nofollow_global) { ?>
          <li class="tabs">
            <a href="#wdseo_tab_nofollow_urls_content"
               class="wdseo-tablink"><?php _e('Excluded Nofollows', WD_SEO_PREFIX); ?></a>
          </li>
          <?php
        }
        if ( isset($options->sitemap_video) && $options->sitemap_video == '1' && $screen->base != "term" ) { ?>
          <li class="tabs">
            <a href="#wdseo_video_sitemap" class="wdseo-tablink"><?php _e('Video Sitemap', WD_SEO_PREFIX); ?></a>
          </li>
        <?php } ?>
      </ul>
      <div id="wdseo_tab_keywords_content" class="wdseo-section wd-table wd-preview">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Preview', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-preview">
              <div class="wd-preview-title">
                <h3>
                  <a href="<?php echo $current_url; ?>" target="_blank"></a>
                </h3>
              </div>
              <div class="wd-preview-url">
                <a href="<?php echo $current_url; ?>" target="_blank">
                  <?php echo $current_url; ?>
                </a>
              </div>
              <?php
              if ($options_defaults->date) {
                ?>
                <div class="wd-preview-date show">
                  <?php echo mysql2date(get_option("date_format"), get_the_date("M d, Y")); ?>
                </div>
                <?php
              }
              ?>
              <div class="wd-preview-description"></div>
            </div>
          </div>
        </div>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_meta_title"><?php _e('Meta Title', WD_SEO_PREFIX); ?></label>
          <input class="wd-has-placeholder wd-set-preview-title" id="wdseo_meta_title" name="wd_settings[meta_title]" value="<?php echo $options->meta_title; ?>" placeholder="<?php echo $options_defaults->meta_title; ?>" data-default="%%title%%" type="text" />
        </span>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_meta_description"><?php _e('Meta Description', WD_SEO_PREFIX); ?></label>
          <textarea class="wd-has-placeholder wd-set-preview-description" id="wdseo_meta_description" name="wd_settings[meta_description]" placeholder="<?php echo $options_defaults->meta_description; ?>" data-default="%%excerpt%%"><?php echo $options->meta_description; ?></textarea>
        </span>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_meta_keywords"><?php _e('Keywords', WD_SEO_PREFIX); ?></label>
          <select class="wd-select2 wd-hide-droprown" id="wdseo_meta_keywords" name="wd_settings[meta_keywords][]" multiple data-placeholder="<?php echo implode(', ', $options_defaults->meta_keywords); ?>">
          <?php
          if ( $options->meta_keywords ) {
            foreach ( $options->meta_keywords as $keyword ) {
              ?>
              <option <?php selected(TRUE, TRUE); ?> data-select2-tag="true" value="<?php echo $keyword; ?>"><?php echo $keyword; ?></option>
              <?php
            }
          }
          ?>
          </select>
        </span>
      </div>
      <div id="wdseo_tab_settings_content" class="wdseo-section wd-table wd-preview">
        <span class="wd-group">
          <label class="wd-label" for="wdseo_canonical_url"><?php _e('Canonical URL', WD_SEO_PREFIX); ?></label>
          <input id="wdseo_canonical_url" name="wd_settings[canonical_url]" value="<?php echo $options->canonical_url; ?>" type="text"/>
        </span>

        <span class="wd-group">
          <?php
          if ( isset( $options->redirect_url ) ) {
            ?>
            <span class="wd-group-col">
                <label class="wd-label" for="wdseo_redirect_url"><?php _e('Redirect URL', WD_SEO_PREFIX); ?></label>
                <input id="wdseo_redirect_url" name="wd_settings[redirect_url]" value="<?php echo $options->redirect_url; ?>" type="text"/>
            </span>
            <?php
          }
          ?>
          <span class="wd-group-col">
              <label class="wd-label"><?php _e('Default Redirection Type', WD_SEO_PREFIX); ?></label>
              <select name="wd_settings[redirections]">
                <option value="" <?php selected((isset( $options->redirections ) && $options->redirections == '') || !isset( $options->redirections )); ?>><?php printf(__('Inherit (currently: %s)', WD_SEO_PREFIX), $redirect_types[WDSeo()->options->redirections] ); ?></option>
                <?php foreach ( $redirect_types as $key => $val) {
                  $selected = selected($options->redirections, $key);
                  echo '<option value="'. $key .'" '. $selected .'>' . $val . '</option>';
                } ?>
              </select>
          </span>
        </span>
        <span class="wd-group">
          <label class="wd-label"><?php _e('Meta robots', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->index, 1); ?> id="wdseo_index1" class="wd-radio" value="1" name="wd_settings[index]" type="radio" />
          <label class="wd-label-radio" for="wdseo_index1"><?php _e('Index', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->index, 0); ?> id="wdseo_index0" class="wd-radio" value="0" name="wd_settings[index]" type="radio" />
          <label class="wd-label-radio" for="wdseo_index0"><?php _e('No index', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->index, ''); ?> id="wdseo_index" class="wd-radio" value="" name="wd_settings[index]" type="radio" />
          <label class="wd-label-radio" for="wdseo_index"><?php printf(__('Inherit (currently: %s)', WD_SEO_PREFIX), ($options_defaults->index ? __('Index', WD_SEO_PREFIX) : __('No index', WD_SEO_PREFIX))); ?></label>
        </span>
        <span class="wd-group">
          <input <?php checked($options->follow, 1); ?> id="wdseo_follow1" class="wd-radio" value="1" name="wd_settings[follow]" type="radio" />
          <label class="wd-label-radio" for="wdseo_follow1"><?php _e('Follow', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->follow, 0); ?> id="wdseo_follow0" class="wd-radio" value="0" name="wd_settings[follow]" type="radio" />
          <label class="wd-label-radio" for="wdseo_follow0"><?php _e('No follow', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->follow, ''); ?> id="wdseo_follow" class="wd-radio" value="" name="wd_settings[follow]" type="radio" />
          <label class="wd-label-radio" for="wdseo_follow"><?php printf(__('Inherit (currently: %s)', WD_SEO_PREFIX), ($options_defaults->follow ? __('Follow', WD_SEO_PREFIX) : __('No follow', WD_SEO_PREFIX))); ?></label>
        </span>
        <span class="wd-group">
          <input value="0" name="wd_settings[robots_advanced][]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
          <input <?php checked(1, in_array('noodp', $options->robots_advanced)); ?> id="wd-meta-advanced-noodp" class="wd-radio" value="noodp" name="wd_settings[robots_advanced][]" type="checkbox" />
          <label class="wd-label-radio" for="wd-meta-advanced-noodp"><?php _e('NO ODP', WD_SEO_PREFIX); ?></label><br />
          <input <?php checked(1, in_array('noimageindex', $options->robots_advanced)); ?> id="wd-meta-advanced-noimageindex" class="wd-radio" value="noimageindex" name="wd_settings[robots_advanced][]" type="checkbox" />
          <label class="wd-label-radio" for="wd-meta-advanced-noimageindex"><?php _e('No Image Index', WD_SEO_PREFIX); ?></label><br />
          <input <?php checked(1, in_array('noarchive', $options->robots_advanced)); ?> id="wd-meta-advanced-noarchive" class="wd-radio" value="noarchive" name="wd_settings[robots_advanced][]" type="checkbox" />
          <label class="wd-label-radio" for="wd-meta-advanced-noarchive"><?php _e('No Archive', WD_SEO_PREFIX); ?></label><br />
          <input <?php checked(1, in_array('nosnippet', $options->robots_advanced)); ?> id="wd-meta-advanced-nosnippet" class="wd-radio" value="nosnippet" name="wd_settings[robots_advanced][]" type="checkbox" />
          <label class="wd-label-radio" for="wd-meta-advanced-nosnippet"><?php _e('No Snippet', WD_SEO_PREFIX); ?></label><br />
          <input <?php checked(1, in_array('', $options->robots_advanced)); ?> id="wd-meta-advanced-inherit" class="wd-radio" value="" name="wd_settings[robots_advanced][]" type="checkbox" />
          <?php
          $robots_advanced_parent_values = str_replace(array('noodp', 'noimageindex', 'noarchive', 'nosnippet'), array(__('NO ODP', WD_SEO_PREFIX), __('No Image Index', WD_SEO_PREFIX), __('No Archive', WD_SEO_PREFIX), __('No Snippet', WD_SEO_PREFIX)), ltrim(implode(', ', $options_defaults->robots_advanced), '0, '));
          ?>
          <label class="wd-label-radio" for="wd-meta-advanced-inherit"><?php printf(__('Inherit (Overwrites current values. Currently: %s)', WD_SEO_PREFIX), $robots_advanced_parent_values ? $robots_advanced_parent_values : __('None', WD_SEO_PREFIX)); ?></label>
        </span>
      </div>
      <div id="wdseo_tab_opengraph_content" class="wdseo-section wd-table wd-preview">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Preview', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-social-preview wd-og-preview">
              <div id="wdseo_og_image" class="wdseo-social-image"></div>
              <div class="wdseo-social-body">
                <div class="wd-preview-social-title wd-preview-og-title">
                  <a href="<?php echo $current_url; ?>" target="_blank"></a>
                </div>
                <div class="wd-preview-social-description wd-preview-og-description"></div>
                <div class="wd-preview-social-url wd-preview-og-url">
                  <a href="<?php echo $current_url; ?>" target="_blank">
                    <?php echo $current_url; ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_opengraph_title"><?php _e('OpenGraph Title', WD_SEO_PREFIX); ?></label>
          <input class="wd-has-placeholder wd-set-preview-og-title" id="wdseo_opengraph_title" name="wd_settings[opengraph_title]" value="<?php echo $options->opengraph_title; ?>" placeholder="<?php echo $options_defaults->opengraph_title; ?>" data-default="%%title%%" type="text"/>
        </span>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_opengraph_description"><?php _e('OpenGraph Description', WD_SEO_PREFIX); ?></label>
          <textarea class="wd-has-placeholder wd-set-preview-og-description" id="wdseo_opengraph_description" name="wd_settings[opengraph_description]" placeholder="<?php echo $options_defaults->opengraph_description; ?>" data-default="%%excerpt%%"><?php echo $options->opengraph_description; ?></textarea>
        </span>
        <span class="wd-group">
          <label class="wd-label"><?php _e('OpenGraph Images', WD_SEO_PREFIX); ?></label>
          <div>
            <input class="image-ids" id="wdseo_opengraph_images" name="wd_settings[opengraph_images]" value="<?php echo $options->opengraph_images; ?>" data-default="<?php echo wp_get_attachment_url($options_defaults->opengraph_images); ?>" type="hidden"/>
            <?php
            // Get saved images ids.
            $attachment_ids = explode(',', $options->opengraph_images);
            // Add template to images array.
            $attachment_ids[] = 'thumb-template';
            foreach ($attachment_ids as $attachment_id) {
              if ($attachment_id) {
                ?>
            <div class="image-cont thumb<?php echo $attachment_id == 'thumb-template' ? ' ' . $attachment_id : ''; ?>"
                  <?php
                  if ($attachment_id != 'thumb-template') {
                    ?>
                    data-id="<?php echo $attachment_id; ?>"
                    data-image-url="<?php echo wp_get_attachment_url($attachment_id); ?>"
                    style="background-image: url('<?php echo wp_get_attachment_thumb_url($attachment_id); ?>')"
                    <?php
                  }
                  ?>>
              <div class="thumb-overlay">
                <div class="thumb-buttons">
                  <span class="wdseo-change-image" title="<?php _e('Change image', WD_SEO_PREFIX); ?>"></span>
                  <span class="wdseo-delete-image" title="<?php _e('Remove image', WD_SEO_PREFIX); ?>"></span>
                </div>
              </div>
            </div>
                <?php
              }
            }
            ?>
            <div class="image-cont wdseo-add-image" title="<?php _e('Add image', WD_SEO_PREFIX); ?>"></div>
          </div>
        </span>
      </div>
      <div id="wdseo_tab_twitter_content" class="wdseo-section wd-table wd-preview">
        <span class="wd-group">
          <input value="0" name="wd_settings[use_og_for_twitter]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
          <input <?php checked($options->use_og_for_twitter, 1); ?> id="wd-use-twitter" class="wd-radio wd-use-twitter" value="1" name="wd_settings[use_og_for_twitter]" type="checkbox" />
          <label class="wd-label-radio" for="wd-use-twitter"><?php _e('Same as OpenGraph', WD_SEO_PREFIX); ?></label>
        </span>
        <div class="wd-box-section wd-twitter-field wd-preview">
          <div class="wd-box-title">
            <strong><?php _e('Preview', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-social-preview wd-twitter-preview">
              <div id="wdseo_twitter_image" class="wdseo-social-image"></div>
              <div class="wdseo-social-body">
                <div class="wd-preview-social-title wd-preview-twitter-title">
                  <a href="<?php echo $current_url; ?>" target="_blank"></a>
                </div>
                <div class="wd-preview-social-description wd-preview-twitter-description"></div>
                <div class="wd-preview-social-url wd-preview-twitter-url">
                  <a href="<?php echo $current_url; ?>" target="_blank">
                    <?php echo $current_url; ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <span class="wd-group wd-twitter-field">
          <label class="wd-label" for="wdseo_twitter_title"><?php _e('Twitter title', WD_SEO_PREFIX); ?></label>
          <input class="wd-has-placeholder wd-set-preview-twitter-title" id="wdseo_twitter_title" name="wd_settings[twitter_title]" value="<?php echo $options->twitter_title; ?>" placeholder="<?php echo $options_defaults->twitter_title; ?>" data-default="%%title%%" type="text"/>
        </span>
          <span class="wd-group wd-twitter-field">
          <label class="wd-label" for="wdseo_twitter_description"><?php _e('Twitter description', WD_SEO_PREFIX); ?></label>
          <textarea class="wd-has-placeholder wd-set-preview-twitter-description" id="wdseo_twitter_description" name="wd_settings[twitter_description]" placeholder="<?php echo $options_defaults->twitter_description; ?>" data-default="%%excerpt%%"><?php echo $options->twitter_description; ?></textarea>
        </span>
        <span class="wd-group wd-twitter-field">
          <label class="wd-label"><?php _e('Twitter images', WD_SEO_PREFIX); ?></label>
          <div>
            <input class="image-ids" id="wdseo_twitter_images" name="wd_settings[twitter_images]" value="<?php echo $options->twitter_images; ?>" data-default="<?php echo wp_get_attachment_url($options_defaults->twitter_images); ?>" type="hidden"/>
            <?php
            // Get saved images ids.
            $attachment_ids = explode(',', $options->twitter_images);
            // Add template to images array.
            $attachment_ids[] = 'thumb-template';
            foreach ($attachment_ids as $attachment_id) {
              if ($attachment_id) {
                ?>
                <div class="image-cont thumb<?php echo $attachment_id == 'thumb-template' ? ' ' . $attachment_id : ''; ?>"
                  <?php
                  if ($attachment_id != 'thumb-template') {
                    ?>
                    data-id="<?php echo $attachment_id; ?>"
                    data-image-url="<?php echo wp_get_attachment_url($attachment_id); ?>"
                    style="background-image: url('<?php echo wp_get_attachment_thumb_url($attachment_id); ?>')"
                    <?php
                  }
                  ?>>
              <div class="thumb-overlay">
                <div class="thumb-buttons">
                  <span class="wdseo-change-image" title="<?php _e('Change image', WD_SEO_PREFIX); ?>"></span>
                  <span class="wdseo-delete-image" title="<?php _e('Remove image', WD_SEO_PREFIX); ?>"></span>
                </div>
              </div>
            </div>
                <?php
              }
            }
            ?>
            <div class="image-cont wdseo-add-image" title="<?php _e('Add image', WD_SEO_PREFIX); ?>"></div>
          </div>
        </span>
      </div>
      <div id="wdseo_tab_faq_content" class="wdseo-section wd-table wd-preview">
        <span class="button" onclick="wdseo_add_faq()"><?php _e('Add question', WD_SEO_PREFIX); ?></span>
        <?php
        if ( isset($options->faqs) && !empty($options->faqs) ) {
          foreach ( $options->faqs as $key => $faq ) {
            $faq['id'] = $key;
            echo self::faq_template( $faq );
          }
        }
        $template = array(
          'id' => '%%ID%%',
          'question' => '',
          'answer' => ''
        );
        echo self::faq_template( $template, TRUE );
        ?>
      </div>
      <div id="wdseo_tab_nofollow_urls_content" class="wdseo-section wd-table wd-preview">
        <label class="wd-label" for="nofollow_external_urls">Exclude URLs from nofollow</label>
        <select class="wd-select2 wd-hide-dropdown nofollow_external_urls" id="nofollow_external_urls" name="wd_settings[nofollow_excluded_urls][]" multiple data-placeholder="">
        <?php
        if ( isset($options->nofollow_excluded_urls) && !empty($options->nofollow_excluded_urls) ) {
          foreach ( $options->nofollow_excluded_urls as $id => $nofollow_excluded_url ) {
             ?>
            <option <?php selected(TRUE, TRUE); ?> data-select2-tag="true" value="<?php echo $nofollow_excluded_url; ?>"><?php echo $nofollow_excluded_url; ?></option>
            <?php
          }
        }
        ?>
        </select>
        <p class="description"><?php _e('Exclude the URLs you want search bots to follow. ', WD_SEO_PREFIX); ?></p>
      </div>
	  <?php if (isset($options->sitemap_video) && $options->sitemap_video == '1' && $screen->base != "term") { ?>
		  <div id="wdseo_video_sitemap" class="wdseo-section wd-table">
			<span class="wd-group">
			  <input value="0" name="wd_settings[exclude_from_video_sitemap]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
			  <input <?php checked($options->exclude_from_video_sitemap, 1); ?> id="wdseo_exclude_from_video_sitemap" class="wd-radio wd-use-twitter" value="1" name="wd_settings[exclude_from_video_sitemap]" type="checkbox" />
			  <label class="wd-label-radio" for="wdseo_exclude_from_video_sitemap"><?php _e('Exclude this post from Video Sitemap?', WD_SEO_PREFIX); ?></label>
			</span>
			<div id="video-block">
			<?php
			$wdseo_video = $options->wdseo_video;
			foreach ($wdseo_video as $key=>$value) { ?>
				<div class="wd-block" data-number="<?php echo $key; ?>">
					<h3 class="wd-block-header"><?php _e('Video', WD_SEO_PREFIX); ?></h3>
				 	<div class="wd-block-container">
						<span class="wd-group">
						  <label class="wd-label" for="wdseo_video_url"><?php _e('Video URL (required)', WD_SEO_PREFIX); ?></label>
						  <input id="wdseo_video_url" name="wd_settings[wdseo_video][<?php echo $key; ?>][video_url]" value="<?php echo $wdseo_video[$key]['video_url']; ?>" type="text"/>
						</span>
						<span class="wd-group">
						  <input value="0" name="wd_settings[wdseo_video][<?php echo $key; ?>][internal_video]" type="hidden" /><?php // hidden input with same name to have empty value. ?>
						  <input <?php checked($wdseo_video[$key]['internal_video'], 1); ?> id="wdseo_internal_video" class="wd-radio wd-use-twitter" value="1" name="wd_settings[wdseo_video][<?php echo $key; ?>][internal_video]" type="checkbox" />
						  <label class="wd-label-radio" for="wdseo_internal_video"><?php _e('NOT an external video (eg: video hosting on YouTube, Vimeo, Wistia...)? Check this if your video is hosting on this server.', WD_SEO_PREFIX); ?></label>
						</span>
						<span class="wd-group">
						  <label class="wd-label" for="wdseo_video_title"><?php _e('Video Title (required)', WD_SEO_PREFIX); ?></label>
						  <input id="wdseo_video_title" name="wd_settings[wdseo_video][<?php echo $key; ?>][video_title]" value="<?php echo $wdseo_video[$key]['video_title']; ?>" type="text"/>
						  <span class="option_desc"><?php _e('Default: title tag, if not available, post title.', WD_SEO_PREFIX); ?></span>
						</span>
						<span class="wd-group">
						  <label class="wd-label" for="wdseo_video_description"><?php _e('Video Description (required)', WD_SEO_PREFIX); ?></label>
							<textarea id="wdseo_video_description" name="wd_settings[wdseo_video][<?php echo $key; ?>][video_description]"><?php echo $wdseo_video[$key]['video_description']; ?></textarea>
						  <span class="option_desc"><?php _e('2048 characters max.; default: meta description. If not available, use the beginning of the post content.', WD_SEO_PREFIX); ?></span>
						</span>
						<span class="wd-group">
						  <label class="wd-label" for="wdseo_video_thumbnail"><?php _e('Video Thumbnail (required)', WD_SEO_PREFIX); ?></label>
              <input type="text" class="wdseo_image_url" id="wdseo_video_thumbnail" name="wd_settings[wdseo_video][<?php echo $key; ?>][video_thumbnail]" value="<?php echo $wdseo_video[$key]['video_thumbnail']; ?>" />
              <button class="wdseo_select_image button">Choose image</button>
              <span class="option_desc"><?php _e('Minimum size: 160x90px (1920x1080 max), JPG, PNG or GIF formats. Default: your post featured image.', WD_SEO_PREFIX); ?></span>
              <img class="wdseo_image" src="<?php echo ($wdseo_video[$key]['video_thumbnail'] != "") ? $wdseo_video[$key]['video_thumbnail'] : ''; ?>"/>
						</span>
						<span class="wd-group">
						  <label class="wd-label" for="wdseo_video_duration"><?php _e('Video Duration (recommended)', WD_SEO_PREFIX); ?></label>
						  <input id="wdseo_video_duration" name="wd_settings[wdseo_video][<?php echo $key; ?>][video_duration]" value="<?php echo $wdseo_video[$key]['video_duration']; ?>" type="number"/>
						  <span class="option_desc"><?php _e('The duration of the video in seconds. Value must be between 0 and 28800 (8 hours).', WD_SEO_PREFIX); ?></span>
						</span>
						<span class="wd-group">
						  <label class="wd-label" for="wdseo_video_rating"><?php _e('Video Rating', WD_SEO_PREFIX); ?></label>
						  <input id="wdseo_video_rating" name="wd_settings[wdseo_video][<?php echo $key; ?>][video_rating]" value="<?php echo $wdseo_video[$key]['video_rating']; ?>" type="number"/>
						  <span class="option_desc"><?php _e('Allowed values are float numbers in the range 0.0 to 5.0.', WD_SEO_PREFIX); ?></span>
						</span>
						<span class="wd-group">
						  <label class="wd-label" for="wdseo_view_count"><?php _e('View count', WD_SEO_PREFIX); ?></label>
						  <input id="wdseo_view_count" name="wd_settings[wdseo_video][<?php echo $key; ?>][view_count]" value="<?php echo $wdseo_video[$key]['view_count']; ?>" type="number"/>
						</span>
						<span class="wd-group">
						  <label class="wd-label" for="wdseo_video_tags"><?php _e('Video tags', WD_SEO_PREFIX); ?></label>
						  <input id="wdseo_video_tags" name="wd_settings[wdseo_video][<?php echo $key; ?>][video_tags]" value="<?php echo $wdseo_video[$key]['video_tags']; ?>" type="text"/>
						  <span class="option_desc"><?php _e('32 tags max., separate tags with commas. Default: post tags if available.', WD_SEO_PREFIX); ?></span>
						</span>
						<span class="wd-group">
						  <label class="wd-label" for="wdseo_video_categories"><?php _e('Video categories', WD_SEO_PREFIX); ?></label>
						  <input id="wdseo_video_categories" name="wd_settings[wdseo_video][<?php echo $key; ?>][video_categories]" value="<?php echo $wdseo_video[$key]['video_categories']; ?>" type="text"/>
						  <span class="option_desc"><?php _e('256 characters max., usually a video will belong to a single category, separate categories with commas. Default: post first category if available.', WD_SEO_PREFIX); ?></span>
						</span>
						<span class="wd-group">
						  <input value="0" name="wd_settings[wdseo_video][<?php echo $key; ?>][not_family_friendly]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
						  <input <?php checked($wdseo_video[$key]['not_family_friendly'], 1); ?> id="not_family_friendly" class="wd-radio wd-use-twitter" value="1" name="wd_settings[wdseo_video][<?php echo $key; ?>][not_family_friendly]" type="checkbox" />
						  <label class="wd-label-radio" for="wdseo_not_family_friendly"><?php _e('NOT family friendly?', WD_SEO_PREFIX); ?></label>
						  <span class="option_desc"><?php _e('The video will be available only to users with SafeSearch turned off.', WD_SEO_PREFIX); ?></span>
						</span>
						<a href="#" class="wdseo_remove-video button">Remove video</a>
					</div>
				</div>
			<?php } ?>
			</div>
			<input type="hidden" id="wdseo_video_count" value="<?php echo count($wdseo_video) - 1; ?>">
			<a href="#" id="wdseo_add-video" class="button button-primary">Add video</a>
		  </div>
	  <?php } ?>
    </div>
    <?php
    // Placeholder template.
    echo WD_SEO_Library::placeholder_template();
    echo ob_get_clean();
  }

  private static function faq_template( $faq = array(), $template = false ) {
    ob_start();
    if ($template) { ?>
      <div id="wdseo-faq-template" class="wdseo-hide">
    <?php } ?>
    <div id="wd-faq-field-<?php echo $faq['id']; ?>" class="wd-faq-field">
      <a href="javascript: void(0)" class="wd-faq-field-remove" onclick="wdseo_remove_faq_by_id('<?php echo $faq['id']; ?>')"><i class="dashicons dashicons-no-alt"></i></a>
      <div class="wd-group">
        <label class="wd-label" for="wd_settings_question_<?php echo $faq['id']; ?>"><?php _e('Question', WD_SEO_PREFIX); ?></label>
        <input id="wd_settings_question_<?php echo $faq['id']; ?>" name="wd_settings[question][]" type="text" value="<?php echo $faq['question']; ?>" />
      </div>
      <div class="wd-group">
        <label class="wd-label" for="wd_settings_answer_<?php echo $faq['id']; ?>"><?php _e('Answer', WD_SEO_PREFIX); ?></label>
        <textarea id="wd_settings_answer_<?php echo $faq['id']; ?>" name="wd_settings[answer][]"><?php echo $faq['answer']; ?></textarea>
      </div>
    </div>
    <?php
    if ($template) { ?>
      </div>
    <?php }
    return ob_get_clean();
  }
}
