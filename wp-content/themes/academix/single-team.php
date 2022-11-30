<?php
/**
 * The template for displaying all single post
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package academix
 */
get_header();
global $post;
?>

<?php 
    $el_check = get_post_meta( $post->ID , '_elementor_data', true );

    if( $el_check == true ){
        $el_class = '';
    } else{
        $el_class = 'site-padding-single';
    }

    academix_page_banner();
    while( have_posts() ): the_post();  

    $idd = get_the_ID();
    $prefix = '_academix_';
    $image_id = get_post_meta( get_the_ID(), $prefix . 'team_profile_picture_id', 1 );
    
    if( wp_get_attachment_image( $image_id, 'academix-single-team-thumb', false, array( 'class' => 'img-responsive img-bit-round' ) ) ){
        $profile_image = wp_get_attachment_image( $image_id, 'academix-single-team-thumb', false, array( 'class' => 'img-responsive img-bit-round' ) );
    } else{
        $profile_image = '';
    }
        
    if( get_post_meta( $idd , $prefix . 'contact_title', true) ){
        $contact_title = get_post_meta( $idd , $prefix . 'contact_title', true);
    } else{
        $contact_title = '';
    }

    if( get_post_meta( $idd , $prefix . 'address_title', true) ){
        $address_title = get_post_meta( $idd , $prefix . 'address_title', true);
    } else{
        $address_title = '';
    }

    if( get_post_meta( $idd , $prefix . 'member_address', true) ){
        $member_address = get_post_meta( $idd , $prefix . 'member_address', true);
    } else{
        $member_address = '';
    }

    if( get_post_meta( $idd , $prefix . 'phone_number', true) ){
        $phone_number = get_post_meta( $idd , $prefix . 'phone_number', true);
    } else{
        $phone_number = '';
    }

    if( get_post_meta( $idd , $prefix . 'email', true) ){
        $email = get_post_meta( $idd , $prefix . 'email', true);
    } else{
        $email = '';
    }

    if( get_post_meta( $idd , $prefix . 'member_designation', true) ){
        $member_designation = get_post_meta( $idd , $prefix . 'member_designation', true);
    } else{
        $member_designation = '';
    }

    if( get_post_meta( $idd , $prefix . 'member_institute', true) ){
        $member_institute = get_post_meta( $idd , $prefix . 'member_institute', true);
    } else{
        $member_institute = '';
    }

    if( get_post_meta( $idd , $prefix . 'education_title', true) ){
        $education_title = get_post_meta( $idd , $prefix . 'education_title', true);
    } else{
        $education_title = '';
    }
    $team_education = get_post_meta( $idd , $prefix . 'team_education', true);


    if( get_post_meta( $idd , $prefix . 'pa_title', true) ){
        $pe_title = get_post_meta( $idd , $prefix . 'pa_title', true);
    } else{
        $pe_title = '';
    }
    $team_professional_experience = get_post_meta( $idd , $prefix . 'team_professional_appoinments', true);

    if( get_post_meta( $idd , $prefix . 'award_prize_title', true) ){
        $award_prize_title = get_post_meta( $idd , $prefix . 'award_prize_title', true);
    } else{
        $award_prize_title = '';
    }
    $team_awards_prizes = get_post_meta( $idd , $prefix . 'team_awards_prizes', true);
    
?>

<div class="container page-academix <?php echo esc_attr($el_class); ?>">
        <div class="row">
            <div class="col-sm-4 col-md-4 pull-up-240">
                <?php echo wp_kses_post($profile_image); ?>
                <div class="sp-blank-20"></div>

                <div class="addr_future_memb">
                    <h4 class="entry-title"><?php echo wp_kses_post( $address_title );?></h4>
                    <div class="address-entry">
                       <p class="__adtext"><?php echo wp_kses_post( $member_address );?></p>
                    </div>
                    <footer class="contact-info">
                        <?php if( !empty($contact_title) ){ ?>
                        <h4 class="entry-title"><?php echo wp_kses_post( $contact_title );?></h4>
                        <?php } ?>
                        <?php if( !empty($phone_number) ){ ?>
                        <p class="__ci_num"><strong><?php echo __('Call:', 'academix') ?></strong> <span><a
                                        href="<?php echo esc_url('tel:'.$phone_number); ?>"><?php echo wp_kses_post($phone_number)?></a></span></p>
                        <?php } ?>
                        <?php if( !empty($email) ){ ?>
                        <p class="__ci_num"><strong><?php echo __('Email:', 'academix') ?></strong> <span><a
                                        href="<?php echo esc_url('mailto:'.$email); ?>"><?php echo wp_kses_post($email)?></a></span></p>
                        <?php } ?>
                    </footer>
                </div>

            </div>
            <div class="col-sm-8">
                <article class="profile-glimps">
                    <h2 class="entry-title title-foc-md"><?php the_title(); ?></h2>
                    <p class="text-foc-md"><?php echo wp_kses_post( $member_designation );?></p>
                    <p class="text-foc-md"><?php echo wp_kses_post( $member_institute );?></p>
                    <div class="stage-content-biog">
                       <?php the_content(); ?>
                    </div><!-- /.stage-content-biog -->
                </article>
                <?php if( !empty($education_title) || !empty($team_education) ){ ?>
                <section class="gimps-container">
                    <h4 class="gimps-title"><?php echo wp_kses_post( $education_title );?></h4>
                    <?php if( isset($team_education) && is_array($team_education) ){ ?>
                    <div class="row">
                        <div class="col-md-8 col-md-offset-1 col-sm-10">
                            <ol class="ol-timeline">
                                <?php foreach ($team_education as $education) { 
                                    if( isset( $education['_academix_education_year'] ) || !empty( $education['_academix_education_year'] ) ){
                                        $education_year = $education['_academix_education_year'];
                                    } else{
                                        $education_year = '';
                                    }

                                    if( isset( $education['_academix_education_institute'] ) || !empty( $education['_academix_education_institute'] ) ){
                                        $education_institute = $education['_academix_education_institute'];
                                    } else{
                                        $education_institute = '';
                                    }

                                    if( isset( $education['_academix_education_degree'] ) || !empty( $education['_academix_education_degree'] ) ){
                                        $education_degree = $education['_academix_education_degree'];
                                    } else{
                                        $education_degree = '';
                                    }
                                ?>
                                <li class="tl-item with-icon">
                                    <p><span class="item-section"><?php echo wp_kses_post( $education_year );?></span></p>
                                    <div class="content-wrapper">
                                        <h3 class="title"><?php echo wp_kses_post( $education_degree );?></h3>
                                        <div class="description"><?php echo wp_kses_post( $education_institute );?></div>
                                    </div>
                                </li>
                                <?php } ?>
                            </ol>
                        </div>
                    </div>
                    <?php } ?>
                </section><!-- /.gimps-container -->
                <?php } ?>
                <?php if( !empty($team_professional_experience) || !empty($team_professional_experience) ){ ?>
                <section class="gimps-container appoint-timeline-holder">
                    <h4  class="gimps-title"><?php echo wp_kses_post( $pe_title );?></h4>
                    <?php if( isset($team_professional_experience) && is_array($team_professional_experience) ){ ?>
                    <div class="row">
                        <div class="col-sm-9">
                            <ol class="appoint-timeline  list-unstyled">
                                <?php foreach ($team_professional_experience as $pro_experience) { 
                                    if( isset( $pro_experience['_academix_pa_year'] ) || !empty( $pro_experience['_academix_pa_year'] ) ){
                                        $pe_year = $pro_experience['_academix_pa_year'];
                                    } else{
                                        $pe_year = '';
                                    }

                                    if( isset( $pro_experience['_academix_pa_designation'] ) || !empty( $pro_experience['_academix_pa_designation'] ) ){
                                        $pe_designation = $pro_experience['_academix_pa_designation'];
                                    } else{
                                        $pe_designation = '';
                                    }

                                    if( isset( $pro_experience['_academix_pa_institute'] ) || !empty( $pro_experience['_academix_pa_institute'] ) ){
                                        $pe_institute = $pro_experience['_academix_pa_institute'];
                                    } else{
                                        $pe_institute = '';
                                    }
                                ?>
                                <li>
                                    <span class="year"><?php echo wp_kses_post( $pe_year );?></span>
                                    <div class="appoint-meta">
                                        <h5 class="pex-title"><?php echo wp_kses_post( $pe_designation );?></h5>
                                        <div class="meta-span"><?php echo wp_kses_post( $pe_institute );?></div>
                                    </div>
                                </li>
                                <?php } ?>
                            </ol>
                        </div>
                    </div>
                    <?php } ?>
                </section><!-- /.gimps-container .appoint-timeline-holder -->
                <?php } ?>
                <?php if( !empty($team_awards_prizes) || !empty($team_awards_prizes) ){ ?>
                <section class="gimps-container">
                    <h4  class="gimps-title"><?php echo wp_kses_post( $award_prize_title );?></h4>
                    <?php if( isset($team_awards_prizes) && is_array($team_awards_prizes) ){ ?>
                    <div class="row">
                        <div class="col-sm-11">
                            <ul class="awards-list list-unstyled">
                                <?php foreach ($team_awards_prizes as $team_award_prize) { 
                                    if( isset( $team_award_prize['_academix_award_prize_year'] ) || !empty( $team_award_prize['_academix_award_prize_year'] ) ){
                                        $award_prize_year = $team_award_prize['_academix_award_prize_year'];
                                    } else{
                                        $award_prize_year = '';
                                    }

                                    if( isset( $team_award_prize['_academix_award_prize_designation'] ) || !empty( $team_award_prize['_academix_award_prize_designation'] ) ){
                                        $award_prize_designation = $team_award_prize['_academix_award_prize_designation'];
                                    } else{
                                        $award_prize_designation = '';
                                    }

                                    if( isset( $team_award_prize['_academix_award_prize_organization'] ) || !empty( $team_award_prize['_academix_award_prize_organization'] ) ){
                                        $award_prize_organization = $team_award_prize['_academix_award_prize_organization'];
                                    } else{
                                        $award_prize_organization = '';
                                    }
                                ?>
                                <li>
                                    <span class="year"><?php echo wp_kses_post( $award_prize_year );?></span>
                                    <div class="awards-meta">
                                        <h5 class="awards-title"><?php echo wp_kses_post( $award_prize_designation );?></h5>
                                        <div class="awards-meta"><span><?php echo wp_kses_post( $award_prize_organization );?></span></div>
                                    </div>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <?php } ?>
                </section><!-- /.gimps-container -->
                <?php } ?>
            </div><!-- #main -->
        </div>
    </div>
<?php endwhile; ?>

<?php 
get_footer();