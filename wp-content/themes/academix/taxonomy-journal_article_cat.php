<?php
get_header(); 
?>

<header class="sabbi-page-header page-header-lg>
    <div class="page-header-content conternt-center">
        <div class="header-title-block">
            <?php echo '<h1 class="page-title">'.single_cat_title('', false).'</h1>'; ?>
        </div>
    </div>

</header>

<div class="academix-content-area">
<div class="container">
			<div class="row">
				 <div class="col-md-12">
<?php

$cat = get_term_by('name', single_cat_title('',false), 'journal_article_cat');
	$args = array(
    'post_type'       => 'journal_article',
    'posts_per_page'  => -1,
    'post_status' => 'publish',
   'journal_article_cat' => $cat->slug,
);

 $q = new WP_Query( $args );

  if( $q->have_posts() ){
 	while ( $q->have_posts() ) : $q->the_post();
           	$idd = get_the_ID();
            $prefix = '_academix_';


            if( get_post_meta( $idd , $prefix . 'journal_article_authors_name', true) ){
                $journal_article_authors_name = get_post_meta( $idd , $prefix . 'journal_article_authors_name', true);
            } else{
                $journal_article_authors_name = '';
            }

            if( get_post_meta( $idd , $prefix . 'journal_article_research_topic', true) ){
                $journal_article_research_topic = get_post_meta( $idd , $prefix . 'journal_article_research_topic', true);
            } else{
                $journal_article_research_topic = '';
            }

            if( get_post_meta( $idd , $prefix . 'journal_article_publication_identity', true) ){
                $journal_article_publication_identity = get_post_meta( $idd , $prefix . 'journal_article_publication_identity', true);
            } else{
                $journal_article_publication_identity = '';
            }

            if( get_post_meta( $idd , $prefix . 'journal_article_doi', true) ){
                $journal_article_doi = get_post_meta( $idd , $prefix . 'journal_article_doi', true);
            } else{
                $journal_article_doi = '';
            }

            if( get_post_meta( $idd , $prefix . 'journal_article_doi_link', true) ){
                $journal_article_doi_link = get_post_meta( $idd , $prefix . 'journal_article_doi_link', true);
            } else{
                $journal_article_doi_link = '';
            }

            if( get_post_meta( $idd , $prefix . 'ja_link_target', true) ){
	            $book_link_target = get_post_meta( $idd , $prefix . 'ja_link_target', true);
            } else{
	            $book_link_target = '';
            }

            $doi_link_target = ( $book_link_target == 0 ) ? '_self' : '_blank';


            if( get_post_meta( $idd , $prefix . 'journal_article_pdf_title', true) ){
                $journal_article_pdf_title = get_post_meta( $idd , $prefix . 'journal_article_pdf_title', true);
            } else{
                $journal_article_pdf_title = '';
            }

            if( get_post_meta( $idd , $prefix . 'journal_article_pdf_link', true) ){
                $journal_article_pdf_link = get_post_meta( $idd , $prefix . 'journal_article_pdf_link', true);
            } else{
                $journal_article_pdf_link = '';
            }

		$html = '';
		$html .= '<div class="rushmore-content-area section-journal-papers"><div class="journal-papers-mound-wrap tab-content">';

   			$html .= '
                <div class="journal-papers-list">
                    <div class="journal-papers">
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="jp-name">'.wp_kses_post( $journal_article_authors_name ).'</p>
                            </div>
                            <div class="col-sm-6">
                                <div class="journal-papers-meta">
                                    <p>'.get_the_title().'<em> '.wp_kses_post($journal_article_research_topic).'</em> '.wp_kses_post($journal_article_publication_identity).'';
                                     if( $journal_article_pdf_link ){
                                     $html .= '<a href="'.esc_url( $journal_article_pdf_link ).'" class="pdf-link" target="_blank">'.wp_kses_post( $journal_article_pdf_title ).'</a>';
                                     }
                                     $html .= '</p>
                                </div>
                            </div>';
                            if( $journal_article_doi ){
                             $html .= '<div class="col-sm-3">
                                <div class="journal-papers-doi"><span>DOI:</span> <a href="'.esc_url($journal_article_doi_link).'" target="'.esc_attr($doi_link_target).'">'.wp_kses_post( $journal_article_doi ).'</a></div>
                            </div>';
                            }
                         $html .= '</div>
                    </div><!-- /.journal-papers -->
                </div>';
		$html .= '</div></div>';

		echo esc_html($html);
	
	endwhile; // End of the loop.
}
?>

</div>
</div>
</div>
</div>
<?php
get_footer();