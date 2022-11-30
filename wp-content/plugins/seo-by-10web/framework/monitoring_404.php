<?php

/**
 * Class WD_SEO_404monitoring
 */
class WD_SEO_404monitoring {

  public function execute() {
    $wdseo_options = new WD_SEO_Options();
    $wdseo_404_monitoring = $wdseo_options->wdseo_404_monitoring;
    if ( isset($wdseo_404_monitoring->wdseo_404_enable) && $wdseo_404_monitoring->wdseo_404_enable === "1" ) {
      $this->wdseo_404_create_redirect($wdseo_404_monitoring);
    }
  }

  //Create Redirection in Post Type
  public function wdseo_404_create_redirect( $wdseo_404_monitoring ) {
    global $wp;
    $get_current_url = esc_url( (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
    //Get User Agent
    if ( !empty($_SERVER['HTTP_USER_AGENT']) ) {
      $wdseo_get_ua = $_SERVER['HTTP_USER_AGENT'];
    }
    $redirect_url = "";
    $redirect_type = 301;
    if ( $get_current_url ) {
      if ( isset($wdseo_404_monitoring->wdseo_404__redirectTo) && $wdseo_404_monitoring->wdseo_404__redirectTo == "1" ) {
        $redirect_url = get_home_url();
        if ( isset($wdseo_404_monitoring->wdseo_404_status_code) && $wdseo_404_monitoring->wdseo_404_status_code != "" ) {
          $redirect_type = $wdseo_404_monitoring->wdseo_404_status_code;
        }
        else {
          $redirect_type = 301;
        }
      }
      elseif ( isset($wdseo_404_monitoring->wdseo_404__redirectTo) && $wdseo_404_monitoring->wdseo_404__redirectTo == "2" && isset($wdseo_404_monitoring->wdseo_404_redirecturl) && $wdseo_404_monitoring->wdseo_404_redirecturl != "" ) {
        $redirect_url = $wdseo_404_monitoring->wdseo_404_redirecturl;
        if ( isset($wdseo_404_monitoring->wdseo_404_status_code) && $wdseo_404_monitoring->wdseo_404_status_code != "" ) {
          $redirect_type = $wdseo_404_monitoring->wdseo_404_status_code;
        }
        else {
          $redirect_type = 301;
        }
      }
      $data = array(
        'enable' => 1,
        'url' => $get_current_url,
        'redirect_url' => $redirect_url,
        'redirect_type' => $redirect_type,
        'agent' => $wdseo_get_ua,
        'redirect_404' => 1,
        'date' => current_time('mysql'),
      );
      $this->set_redirects_to_db($data);
      if ( isset($wdseo_404_monitoring->wdseo_404_enable_email) && $wdseo_404_monitoring->wdseo_404_enable_email == "1" && isset($wdseo_404_monitoring->wdseo_404_email_to) && $wdseo_404_monitoring->wdseo_404_email_to != "" ) {
        $this->wdseo_404_send_alert($get_current_url, $wdseo_404_monitoring);
      }
    }
    if ( isset($wdseo_404_monitoring->wdseo_404__redirectTo) && $wdseo_404_monitoring->wdseo_404__redirectTo == "1" ) {
      if ( isset($wdseo_404_monitoring->wdseo_404_status_code) && $wdseo_404_monitoring->wdseo_404_status_code != "" ) {
        WD_SEO_Library::_redirect(get_home_url(), $wdseo_404_monitoring->wdseo_404_status_code);
        exit;
      }
      else {
        WD_SEO_Library::_redirect(get_home_url(), '301');
        exit;
      }
    }
    elseif ( isset($wdseo_404_monitoring->wdseo_404__redirectTo) && $wdseo_404_monitoring->wdseo_404__redirectTo == "2" && isset($wdseo_404_monitoring->wdseo_404_redirecturl) && $wdseo_404_monitoring->wdseo_404_redirecturl != "" ) {
      if ( isset($wdseo_404_monitoring->wdseo_404_status_code) && $wdseo_404_monitoring->wdseo_404_status_code != "" ) {
        WD_SEO_Library::_redirect($wdseo_404_monitoring->wdseo_404_redirecturl, $wdseo_404_monitoring->wdseo_404_status_code);
        exit;
      }
      else {
        WD_SEO_Library::_redirect($wdseo_404_monitoring->wdseo_404_redirecturl, '301');
        exit;
      }
    }
  }

  public function set_redirects_to_db( $data ) {
    global $wpdb;
    $url = $data['url'];
    $row = $wpdb->get_row("SELECT id,count FROM " . $wpdb->prefix . WD_SEO_PREFIX . "_redirects WHERE url = '$url'");
    if ( !empty($row) ) {
      $id = $row->id;
      $count = $row->count;
      $data['count'] = $count + 1;
      $wpdb->update($wpdb->prefix . WD_SEO_PREFIX . '_redirects', $data, array( 'id' => $id ));
    }
    else {
      $data['count'] = 1;
      $wpdb->insert($wpdb->prefix . WD_SEO_PREFIX . '_redirects', $data);
    }
  }

  public function wdseo_404_send_alert_content_type() {
    return 'text/html';
  }

  /* Send email */
  public function wdseo_404_send_alert( $get_current_url, $wdseo_404_monitoring ) {
    add_filter('wp_mail_content_type', array( $this, 'wdseo_404_send_alert_content_type' ));
    add_filter('wp_mail_from_name', function( $from_name ) {
      return 'SEO by 10Web';
    });
    $to = $wdseo_404_monitoring->wdseo_404_email_to;
    $subject = 'SEO by 10Web: 404 alert - ' . get_bloginfo('name');
    $body = "<style>
              #wds_container {
                width:auto;
                max-width:580px;
                height:auto;
                padding: 30px;
                border-radius: 5px;
                text-align: center;
                background: #FFFFFF 0% 0% no-repeat padding-box;
                -webkit-box-shadow: 0px 3px 17px 0px rgba(0,0,0,0.34);
                -moz-box-shadow: 0px 3px 17px 0px rgba(0,0,0,0.34);
                box-shadow: 0px 3px 17px 0px rgba(0,0,0,0.34);;
              }
              .wds_container h1 {
                font-size: 26px;
                color:#323A45;
                text-align: center;
                display: block;
                padding: 0;
                margin: 20px 0;
              }
              .wds_container p {
                font-size: 16px;
                color:#323A45;
                text-align: center;
                display: block;
                padding: 0;
                margin: 0 0 20px 0;
              }
            </style>";
    $src = WD_SEO_URL . '/images/alert404.png';
    $body .= "<div id='wds_container'>";
    $body .= "<img src='data:image/png;base64,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'>";
    $body .= "<h1>" . __('404 alert', WD_SEO_PREFIX) . "</h1>";
    $body .= "<p>" . __('You are receiving this email because a new 404 error has been logged on your site. See below.', WD_SEO_PREFIX) . "</p>";
    $body .= "<p>" . __('URL : ', WD_SEO_PREFIX) . $get_current_url . "</p>";
    $body .= "</div>";
    wp_mail($to, $subject, $body);
    remove_filter('wp_mail_content_type', array( $this, 'wdseo_404_send_alert_content_type' ));
  }
}