<?php 

add_action( 'cmb2_admin_init', 'academix_metabox' );

function academix_metabox(){

	$prefix = '_academix_';

    // team profile picture
	$team_profile_picture_options = new_cmb2_box( array(
		'id'  => $prefix . 'team_profile_picture_metabox',
		'title' => esc_html__('Upload Profile Picture', 'academix'),
		'object_types' => array( 'team' ), //Post type
		'context'  => 'side',
		'priority' => 'low',
		'show_names' => true, // Show field names on the left
	) );
    $team_profile_picture_options->add_field( array(
    	'id' => $prefix . 'team_profile_picture',
    	'name' => esc_html__( 'image size is 360 x 270px', 'academix' ),
    	'type' => 'file',
    	'options' => array(
    		'url' => false, // Hide the text input for the url
    	)
    ));

	// team basic info
	$team_basic_info_options = new_cmb2_box( array(
		'id'  => $prefix . 'a_team_basic_info_metabox',
		'title' => esc_html__('Basic Info', 'academix'),
		'object_types' => array( 'team' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
    $team_basic_info_options->add_field( array(
    	'id' => $prefix . 'member_designation',
    	'name' => esc_html__( 'Designation', 'academix' ),
    	'type' => 'textarea',
    	'default' => '',
    ));
    $team_basic_info_options->add_field( array(
    	'id' => $prefix . 'member_institute',
    	'name' => esc_html__( 'Institute', 'academix' ),
    	'type' => 'textarea',
    	'default' => '',
    ));

    // team address 
    $team_address_options = new_cmb2_box( array(
		'id'  => $prefix . 'team_address_metabox',
		'title' => esc_html__('Address', 'academix'),
		'object_types' => array( 'team' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
	$team_address_options->add_field( array(
    	'id' => $prefix . 'address_title',
    	'name' => esc_html__( 'Title', 'academix' ),
    	'type' => 'text',
    	'default' => esc_html__('Address', 'academix'),
    ));
    $team_address_options->add_field( array(
    	'id' => $prefix . 'member_address',
    	'name' => esc_html__( 'Address', 'academix' ),
    	'type' => 'textarea',
    	'default' => '',
    ));

    // team contact 
    $team_contact_options = new_cmb2_box( array(
		'id'  => $prefix . 'team_contact_metabox',
		'title' => esc_html__('Contact', 'academix'),
		'object_types' => array( 'team' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
	$team_contact_options->add_field( array(
    	'id' => $prefix . 'contact_title',
    	'name' => esc_html__( 'Title', 'academix' ),
    	'type' => 'text',
    	'default' => esc_html__('Contact Information', 'academix'),
    ));
    $team_contact_options->add_field( array(
    	'id' => $prefix . 'phone_number',
    	'name' => esc_html__( 'Phone Number', 'academix' ),
    	'type' => 'text',
    	'default' => '',
    ));
    $team_contact_options->add_field( array(
    	'id' => $prefix . 'email',
    	'name' => esc_html__( 'Email', 'academix' ),
    	'type' => 'text',
    	'default' => '',
    ));

    // team education 
    $team_education_options = new_cmb2_box( array(
		'id'  => $prefix . 'team_education_metabox',
		'title' => esc_html__('Education', 'academix'),
		'object_types' => array( 'team' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
    $team_education_options->add_field( array(
    	'id' => $prefix . 'education_title',
    	'name' => esc_html__( 'Title', 'academix' ),
    	'type' => 'text',
    	'default' => '',
    ));
    $team_education_group = $team_education_options->add_field(array(
		'id' => $prefix . 'team_education',
		'type' => 'group',
		'options'     => array(
			'group_title'   => esc_html__( 'Add Row{#}', 'academix' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add Another Row', 'academix' ),
			'remove_button' => esc_html__( 'Remove Row', 'academix' ),
			'sortable'      => true, // beta
			// 'closed'     => true, // true to have the groups closed by default
		),
	));
	$team_education_options->add_group_field($team_education_group, array(
		'id' => $prefix.'education_year',
		'name' => esc_html__( 'Education Year', 'academix' ),
		'type' => 'text',
		'default' => '',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));
	$team_education_options->add_group_field($team_education_group, array(
		'id' => $prefix.'education_institute',
		'name' => esc_html__( 'Education Institute', 'academix' ),
		'type' => 'text',
		'default' => '',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));
	$team_education_options->add_group_field($team_education_group, array(
		'id' => $prefix.'education_degree',
		'name' => esc_html__( 'Education Degree', 'academix' ),
		'type' => 'text',
		'default' => '',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));
    
    // team professional appoinments
    $team_professional_appoinments_options = new_cmb2_box( array(
		'id'  => $prefix . 'team_professional_appoinments_metabox',
		'title' => esc_html__('Professional Experience', 'academix'),
		'object_types' => array( 'team' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
	$team_professional_appoinments_options->add_field( array(
    	'id' => $prefix . 'pa_title',
    	'name' => esc_html__( 'Title', 'academix' ),
    	'type' => 'text',
    	'default' => '',
    ));
	$team_professional_appoinments_group = $team_professional_appoinments_options->add_field(array(
		'id' => $prefix . 'team_professional_appoinments',
		'type' => 'group',
		'options'     => array(
			'group_title'   => esc_html__( 'Add Row{#}', 'academix' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add Another Row', 'academix' ),
			'remove_button' => esc_html__( 'Remove Row', 'academix' ),
			'sortable'      => true, // beta
			// 'closed'     => true, // true to have the groups closed by default
		),
	));
	$team_professional_appoinments_options->add_group_field($team_professional_appoinments_group, array(
		'id' => $prefix.'pa_year',
		'name' => esc_html__( 'Year', 'academix' ),
		'type' => 'text',
		'default' => '',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));
	$team_professional_appoinments_options->add_group_field($team_professional_appoinments_group, array(
		'id' => $prefix.'pa_designation',
		'name' => esc_html__( 'Designation', 'academix' ),
		'type' => 'text',
		'default' => '',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));
	$team_professional_appoinments_options->add_group_field($team_professional_appoinments_group, array(
		'id' => $prefix.'pa_institute',
		'name' => esc_html__( 'Institute', 'academix' ),
		'type' => 'text',
		'default' => '',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));

    // team awards and prizes
	$team_awards_prizes_options = new_cmb2_box( array(
		'id'  => $prefix . 'team_awards_prizes_metabox',
		'title' => esc_html__('Awards Prizes', 'academix'),
		'object_types' => array( 'team' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
	$team_awards_prizes_options->add_field( array(
    	'id' => $prefix . 'award_prize_title',
    	'name' => esc_html__( 'Title', 'academix' ),
    	'type' => 'text',
    	'default' => '',
    ));
	$team_awards_prizes_group = $team_awards_prizes_options->add_field(array(
		'id' => $prefix . 'team_awards_prizes',
		'type' => 'group',
		'options'     => array(
			'group_title'   => esc_html__( 'Add Row{#}', 'academix' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add Another Row', 'academix' ),
			'remove_button' => esc_html__( 'Remove Row', 'academix' ),
			'sortable'      => true, // beta
			// 'closed'     => true, // true to have the groups closed by default
		),
	));
	$team_awards_prizes_options->add_group_field($team_awards_prizes_group, array(
		'id' => $prefix.'award_prize_year',
		'name' => esc_html__( 'Year', 'academix' ),
		'type' => 'text',
		'default' => '',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));
	$team_awards_prizes_options->add_group_field($team_awards_prizes_group, array(
		'id' => $prefix.'award_prize_designation',
		'name' => esc_html__( 'Award Title', 'academix' ),
		'type' => 'text',
		'default' => '',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));
	$team_awards_prizes_options->add_group_field($team_awards_prizes_group, array(
		'id' => $prefix.'award_prize_organization',
		'name' => esc_html__( 'Organization', 'academix' ),
		'type' => 'text',
		'default' => '',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));



    // team social
	$team_social_options = new_cmb2_box( array(
		'id'  => $prefix . 'team_social_metabox',
		'title' => esc_html__('Social', 'academix'),
		'object_types' => array( 'team' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
	$team_social_group = $team_social_options->add_field(array(
		'id' => $prefix . 'team_social',
		'type' => 'group',
		'options'     => array(
			'group_title'   => esc_html__( 'Add Row{#}', 'academix' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add Another Row', 'academix' ),
			'remove_button' => esc_html__( 'Remove Row', 'academix' ),
			'sortable'      => true, // beta
			// 'closed'     => true, // true to have the groups closed by default
		),
	));
	$team_social_options->add_group_field($team_social_group, array(
		'id' => $prefix.'socail_link',
		'name' => esc_html__( 'Social Link', 'academix' ),
		'type' => 'text_url',
		'default' => '#',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));
    $team_social_options->add_group_field($team_social_group, array(
		'id' => $prefix.'socail_icon',
		'name' => esc_html__( 'Social Icon', 'academix' ),
		'type' => 'file',
		'options' => array(
			'url' => false
		),
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	));


	// metabox for page, custom post
	$page_options = new_cmb2_box( array(
		'id'  => $prefix . 'page_metabox',
		'title' => esc_html__('Page Settings', 'academix'),
		'object_types' => array( 'page', 'post', 'team', 'event' ), //Post type
		'context'  => 'normal',
		'priority' => 'default',
		'show_names' => true, // Show field names on the left
	) );

	$page_options->add_field( array(
    	'id' => $prefix . 'display_page_banner',
    	'name' => esc_html__( 'Display / Hide Page Banner', 'academix' ),
    	'description' => esc_html__('Do you want to display page banner section?', 'academix'),
    	'type' => 'radio_inline',
    	'options' => array(
    		'2' => esc_html__( 'Default', 'academix' ),
    		'1' => esc_html__( 'Display', 'academix' ),
    		'0' => esc_html__( 'Hide', 'academix' ),
    	),
    	'default' => '1',
    ));

    $page_options->add_field( array(
    	'id' => $prefix . 'banner_bg_color',
    	'name' => esc_html__( 'Banner Background Color', 'academix' ),
    	'description' => esc_html__('if you don\'t set any featured image then it will work', 'academix'),
    	'type' => 'colorpicker',
    	'default' => '',
    ));

    // page breadcrumbs
	$page_options->add_field( array(
    	'id' => $prefix . 'display_page_breadcrumbs',
    	'name' => esc_html__( 'Display / Hide Page Breadcrumbs', 'academix' ),
    	'description' => esc_html__('Do you want to display page breadcrumbs?', 'academix'),
    	'type' => 'radio_inline',
    	'options' => array(
    		'2' => esc_html__( 'Default', 'academix' ),
    		'1' => esc_html__( 'Display', 'academix' ),
    		'0' => esc_html__( 'Hide', 'academix' ),
    	),
    	'default' => '1',
    ));


    // event metabox
	$event_options = new_cmb2_box( array(
		'id'  => $prefix . 'event_metabox',
		'title' => esc_html__('Event Settings', 'academix'),
		'object_types' => array( 'event' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
	$event_options->add_field( array(
    	'id' => $prefix . 'event_location',
    	'name' => esc_html__( 'Event Location', 'academix' ),
    	'type' => 'textarea',
    ));
    $event_options->add_field( array(
    	'id' => $prefix . 'event_date',
    	'name' => esc_html__( 'Event Date', 'academix' ),
    	'type' => 'text_date_timestamp',
    	'date_format' => 'j M Y',
    ));
    $event_options->add_field( array(
    	'id' => $prefix . 'event_image_gallery',
    	'name' => esc_html__( 'Event Gallery Image', 'academix' ),
    	'type' => 'file_list',
    ));

    $event_picture_options = new_cmb2_box( array(
		'id'  => $prefix . 'event_picture_metabox',
		'title' => esc_html__('Upload Event Image', 'academix'),
		'object_types' => array( 'event' ), //Post type
		'context'  => 'side',
		'priority' => 'low',
		'show_names' => true, // Show field names on the left
	) );
    $event_picture_options->add_field( array(
    	'id' => $prefix . 'event_image',
    	'name' => esc_html__( 'image size is 750 x 495px', 'academix' ),
    	'type' => 'file',
    	'context'  => 'side',
		'priority' => 'low',
    	'options' => array(
    		'url' => false, // Hide the text input for the url
    	) 
    ));


    // book metabox
	$book_options = new_cmb2_box( array(
		'id'  => $prefix . 'book_metabox',
		'title' => esc_html__('Book Settings', 'academix'),
		'object_types' => array( 'book' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
	$book_options->add_field( array(
    	'id' => $prefix . 'book_link',
    	'name' => esc_html__( 'Book Link', 'academix' ),
    	'type' => 'text_url',
    	'default' => '#'
    ));
    $book_options->add_field( array(
    	'id' => $prefix . 'book_link_target',
    	'name' => esc_html__( 'Book link open in new tab?', 'academix' ),
    	'type' => 'radio_inline',
    	'options' => array(
    		'0' => esc_html__( 'No', 'academix' ),
    		'1' => esc_html__( 'Yes', 'academix' ),
    	),
    	'default' => '0',
    ));
	$book_options->add_field( array(
    	'id' => $prefix . 'book_description',
    	'name' => esc_html__( 'Book Description', 'academix' ),
    	'type' => 'textarea_small',
    	'default' => ''
    ));
    $book_options->add_field( array(
    	'id' => $prefix . 'book_author_name',
    	'name' => esc_html__( 'Author Name', 'academix' ),
    	'type' => 'text',
    	'default' => ''
    ));
    $book_options->add_field( array(
    	'id' => $prefix . 'book_image',
    	'name' => esc_html__( 'Upload Book Image', 'academix' ),
    	'type' => 'file',
    	'context'  => 'side',
		'priority' => 'low',
    	'options' => array(
    		'url' => false, // Hide the text input for the url
    	) 
    ));

    // journal article metabox
	$journal_article_options = new_cmb2_box( array(
		'id'  => $prefix . 'journal_article_metabox',
		'title' => esc_html__('Journal Article Settings', 'academix'),
		'object_types' => array( 'journal_article' ), //Post type
		'context'  => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
	) );
	$journal_article_options->add_field( array(
    	'id' => $prefix . 'journal_article_authors_name',
    	'name' => esc_html__( 'Authors Name', 'academix' ),
    	'type' => 'textarea_small',
    	'default' => ''
    ));
    $journal_article_options->add_field( array(
    	'id' => $prefix . 'journal_article_research_topic',
    	'name' => esc_html__( 'Research Topic', 'academix' ),
    	'type' => 'textarea_small',
    	'default' => ''
    ));
    $journal_article_options->add_field( array(
    	'id' => $prefix . 'journal_article_publication_identity',
    	'name' => esc_html__( 'Publication Identity', 'academix' ),
    	'type' => 'textarea_small',
    	'default' => ''
    ));
    $journal_article_options->add_field( array(
    	'id' => $prefix . 'journal_article_doi',
    	'name' => esc_html__( 'DOI', 'academix' ),
    	'type' => 'textarea_small',
    	'default' => ''
    ));
    $journal_article_options->add_field( array(
    	'id' => $prefix . 'journal_article_doi_link',
    	'name' => esc_html__( 'DOI Link', 'academix' ),
    	'type' => 'text_url',
    	'default' => '#'
    ));
	$journal_article_options->add_field( array(
		'id' => $prefix . 'ja_link_target',
		'name' => esc_html__( 'DOI open in new tab?', 'academix' ),
		'type' => 'radio_inline',
		'options' => array(
			'0' => esc_html__( 'No', 'academix' ),
			'1' => esc_html__( 'Yes', 'academix' ),
		),
		'default' => '0',
	));
    $journal_article_options->add_field( array(
    	'id' => $prefix . 'journal_article_pdf_title',
    	'name' => esc_html__( 'PDF Title', 'academix' ),
    	'type' => 'text',
    	'default' => ''
    ));
    $journal_article_options->add_field( array(
    	'id' => $prefix . 'journal_article_pdf_link',
    	'name' => esc_html__( 'PDF Link', 'academix' ),
    	'type' => 'file',
    	'options' => array(
				'url' => true, 
	     ),
    	'text'    => array(
			'add_upload_file_text' => esc_html__('Add PDF', 'academix')
	    ),
		'query_args' => array(
			'type' => 'application/pdf',
		),
    ));

    $journal_article_side_options = new_cmb2_box( array(
		'id'  => $prefix . 'journal_article_side_metabox',
		'title' => esc_html__('Selected Publications', 'academix'),
		'object_types' => array( 'journal_article' ), //Post type
		'context'  => 'side',
		'priority' => 'low',
		'show_names' => true, // Show field names on the left
	) );
    $journal_article_side_options->add_field( array(
    	'id' => $prefix . 'selected_publications',
    	'title' => esc_html__('Selected Publications', 'academix'),
    	'desc' => esc_html__( 'Selected Publication ( Please check this - if you want show this post on selectd publication box)', 'academix' ),
    	'type' => 'checkbox',
    ));
    $journal_article_side_options->add_field( array(
    	'id' => $prefix . 'journal_article_selecte_publication_btn_text',
    	'name' => esc_html__( 'SP Button Text', 'academix' ),
    	'type' => 'text',
    	'default' => ''
    ));
    $journal_article_side_options->add_field( array(
    	'id' => $prefix . 'journal_article_selecte_publication_btn_link',
    	'name' => esc_html__( 'SP Button Link', 'academix' ),
    	'type' => 'text_url',
    	'default' => ''
    ));


}