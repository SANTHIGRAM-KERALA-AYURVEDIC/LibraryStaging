<?php
/**
   Template Name:  Main Search
 */
get_header();

$data;
$limit = 10;
$total;
$pageNumber = $_GET['page'] ? $_GET['page'] : 1;
$start = $pageNumber > 1 ? ($pageNumber - 1) * $limit : 0;
$sort = $_GET['sort'] ? $_GET['sort'] : '';
$direction = $_GET['direction'] ? $_GET['direction'] : '';
$itemType = $_GET['itemType'] ? $_GET['itemType'] : '';
$requestUri = parse_url($_SERVER['REQUEST_URI']);
$queryArray;
parse_str($requestUri['query'], $queryArray);

$tags = getSelectedItems('tag', $queryArray);
$baseUrl = getBaseURL();

if(array_key_exists('s', $queryArray)) {
	$searchTerm = $queryArray['s'];
	$tagUrl = "";
	if(!empty($tags)) {
		foreach($tags as $tag) {
			$tagUrl .= "tag=$tag&";
		}
	}
	$url = "$baseUrl?q=$searchTerm&limit=$limit&start=$start&sort=$sort&direction=$direction&$tagUrl&itemType=$itemType";
	$reqArgs = array(
		'headers' => array(
			'Authorization' => 'Bearer BVY0qCEHT3YNdwrioR8bE7wM'
		)
	);
	$response = wp_remote_get($url, $reqArgs);
	$data = json_decode($response['body']);
	$total = wp_remote_retrieve_header($response, 'Total-Results');
}

global $academix_options;
global $post;
$prefix = '_academix_';

$el_check = get_post_meta( $post->ID , '_elementor_data', true );
$display_metabox_page_banner = get_post_meta( $post->ID,  $prefix . 'display_page_banner', true );
$display_page_breadcrumbs = get_post_meta( $post->ID,  $prefix . 'display_page_breadcrumbs', true );

if( $el_check == true ){
    $el_class = '';
} else{
    $el_class = 'site-padding';
}

// args

// set meta value based on itemType
if($itemType === 'artwork') {

}


$args = array(
	'numberposts'	=> -1,
	'post_type'		=> 'post',
	'search_title'  => $queryArray['s'],
	'meta_key'		=> $itemType === 'artwork' ? 'category' : "",
	'meta_value'    => $_GET['category'] ? $_GET['category'] : "",
	'posts_per_page'=> 5,
	'offset' => 5 * ($pageNumber - 1),
);

$wpCounts = 0;

// query
$the_query = new WP_Query( $args );

 
	if( $the_query->have_posts() ): ?>
		<?php while( $the_query->have_posts() ) : $the_query->the_post();
			
			if($itemType === 'artwork' || $itemType === '' ) {
				$rawPost = (object)[];
				$postData = (object)[];
		
				$postCreator = (object)[];
				$postCreator->firstName = ucwords(get_post_meta(get_the_ID(), 'category', true));
				$postCreator->lastName = '';
	
				$postData->title = get_the_title();
				$postData->url = esc_url( get_permalink() );
				$postData->creators = [$postCreator];
				$postData->abstractNote = get_the_content();
				$postData->date = get_the_date( 'dS M Y');
				$postData->image = get_the_post_thumbnail(get_the_ID(), [100, 100]);
		
				$rawPost->data = $postData;
				array_push($data, $rawPost);
			}
			$total++;
			$wpCounts++;
		endwhile;
	endif;
?>


<?php wp_reset_query();	

$boxItems = ['', 'journalArticle', 'book || bookSection', 'thesis', 'report || presentation || document', 'newspaperArticle || magazineArticle', 'artwork'];
$resultCounts = getAllItemsCount($boxItems);

?>

<div class="loader-container" style="/* display: none; */">
    <div class="loader">
    </div>
	<p class="loader_text">We are retrieving relevant information from our repository. Thank you for your patience…
</p>
</div> 
	
<div class="search_resullts_header">
	  <div class="search_results_bar">
	     <?php echo do_shortcode( '[wd_asp id=7]' );?>
	  </div>
</div>
<div class="wrapper">
	  
        <ul>
            <li class="box-item <?php echo isActive("") ?>"><a href="<?php $queryArray['itemType'] = ""; echo "/?" . http_build_query($queryArray); ?>">All Results <?php echo $queryArray['s'] === '' ? "" : "(" . strval($resultCounts[$boxItems[0]] + $wpCounts) . ")"?></a></li>
            <li class="box-item <?php echo isActive("journalArticle") ?>"><a href="<?php $queryArray['itemType'] = "journalArticle"; echo "/?" . http_build_query($queryArray); ?>">Journal Articles <?php echo $queryArray['s'] === '' ? "" : "(" . $resultCounts[$boxItems[1]] . ")"?></a></li>
            <li class="box-item <?php echo isActive("book || bookSection") ?>"><a href="<?php $queryArray['itemType'] = "book || bookSection"; echo "/?" . http_build_query($queryArray); ?>">Books <?php echo $queryArray['s'] === '' ? "" : "(" . $resultCounts[$boxItems[2]] . ")"?></a></li>
            <li class="box-item <?php echo isActive("thesis") ?>"><a href="<?php $queryArray['itemType'] = "thesis"; echo "/?" . http_build_query($queryArray); ?>">Thesis <?php echo $queryArray['s'] === '' ? "" : "(" . $resultCounts[$boxItems[3]] . ")"?></a></li>
            <li class="box-item <?php echo isActive("report || presentation || document") ?>"><a href="<?php $queryArray['itemType'] = "report || presentation || document"; echo "/?" . http_build_query($queryArray); ?>">Reports <?php echo $queryArray['s'] === '' ? "" : "(" . $resultCounts[$boxItems[4]] . ")"?></a></li>
            <li class="box-item <?php echo isActive("newspaperArticle || magazineArticle") ?>"><a href="<?php $queryArray['itemType'] = "newspaperArticle || magazineArticle"; echo "/?" . http_build_query($queryArray); ?>">Magazine <?php echo $queryArray['s'] === '' ? "" : "(" . $resultCounts[$boxItems[5]] . ")"?></a></li>
            <li class="box-item <?php echo isActive("artwork") ?>"><a href="<?php $queryArray['itemType'] = "artwork"; echo "/?" . http_build_query($queryArray); ?>">Media / Images <?php echo $queryArray['s'] === '' ? "" : "(" . strval($resultCounts[$boxItems[6]]) . ")"?></a></li>
			<?php $queryArray['itemType'] = $_GET['itemType']; ?>
		</ul>
    </div>
	<div class="container">
	<div class="row" style="margin-top: 5rem">
		<div class="col-lg-8">
			<div class="wrapper wrapper-content animated fadeInRight">

				<div class="ibox-content forum-container">
                    <?php 
					if(!empty($data)) {
						foreach($data as $k=>$v) {
							$url;
							if(!empty($v->data->DOI)) {
								$url = "https://doi.org/" . $v->data->DOI;
							} else {
								$url = $v->data->url;
							}
						
					?>

					<div class="forum-item">
						<div class="row">
							<?php if($itemType === 'artwork') { ?>

							
							<div class="col-md-2">
								<?php echo $v->data->image ?>
							</div>
							<div class="col-md-9">
							<?php } else { ?>
								<div class="col-md-11">
							<?php } ?>
								<div class="forum-icon">
									<?php echo (($pageNumber - 1) * $limit) + $k + 1 ?>
									<i class="fa fa-bookmark"></i>
								</div>
								<div id="title-data">
									
									<a target="_blank" href="<?php echo $url ?>" class="forum-item-title"><?php echo $v->data->title ?></a>
								</div>
								<div class="forum-sub-title" style="color:inherit;">
									<?php
										$creators = $v->data->creators;
										if($creators) {
											$iterator = 0;
											foreach($creators as $creator) {
												echo $iterator !== count($creators) - 1 ? "$creator->firstName $creator->lastName, " : "$creator->firstName $creator->lastName";
												$iterator++;
											}
										}
									?>
								</div>
								<div class="forum-sub-title" style="color:inherit; font-weight:bold"><?php 
								
								if($v->data->publicationTitle) {
									echo $v->data->publicationTitle . ", " . $v->data->date; 
								} else {
									echo $v->data->date; 
								}
									
								
								?></div>
								<div class="forum-sub-title"><?php echo wp_trim_words($v->data->abstractNote, 20) ?></div>
							
								<?php if($itemType == 'artwork') { ?>
								<div class="forum-sub-title tagtest"><?php echo $v->data->tags[0]->tag;  ?></div>
								<?php } ?>
								<?php if($itemType == '' && 'artwork') { ?>
								<div class="forum-sub-title tagtest"><?php echo $v->data->tags[0]->tag;  ?></div>
								<?php } ?>
							</div>
							
							<div class="col-md-1">
								<i class="fas fa-heart"></i>
							</div>
						</div>
					</div>
                    <?php }} ?>
				</div>
			</div>						

            <nav aria-label="Page navigation example">
                <ul class="pagination">
					
					<?php if($pageNumber > 1) {
						$queryArray['page'] = $pageNumber - 1;
					?>
                    	<li class="page-item"><a class="page-link" href="<?php echo "/?" . http_build_query($queryArray) ?>">Previous</a></li>
                    <?php } ?>
					
					<li class="page-item"><a class="page-link active"><?php echo $pageNumber ?></a></li>
					
					<?php for($i = $pageNumber + 1; $i < $pageNumber + 5; $i++) {
						if($total && $i <= ceil($total/$limit)) {
						$queryArray['page'] = $i;
					?>
                    	<li class="page-item"><a class="page-link" href="<?php  echo "/?" . http_build_query($queryArray) ?>"><?php echo $i ?></a></li>
                    <?php }} ?>

					<?php if($total && $pageNumber < ceil($total/$limit)) { 
						$queryArray['page'] = $pageNumber + 1;
					?>
                    	<li class="page-item"><a class="page-link" href="<?php  echo "/?" . http_build_query($queryArray) ?>">Next</a></li>
					<?php } ?>
                </ul>
            </nav>
		</div>
		<div class="col-lg-4 search_filter_right">
			<h3 style="margin-top: 3.2rem;">Filters</h3> <a class="btn btn-primary" href="<?php echo resetFilters($queryArray) ?>">Reset Filters</a>
			
			<h4>Sort by</h4>
			<div class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
					$sort = preg_replace('/(?<!\ )[A-Z]/', ' $0', $sort);
					echo $sort ? ucwords($sort) : "Select Option";
				?> <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<?php if(!empty($sort)) { ?>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort', 'select', $queryArray);
					?>">Select Option</a></li>
					<?php } ?>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort', 'dateAdded', $queryArray);
					?>">Date Added</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','dateModified', $queryArray);
					?>">Date Modified</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','title', $queryArray);
					?>">Title</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','creator', $queryArray);
					?>">Creator</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','itemType', $queryArray);
					?>">ItemType</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','date', $queryArray);
					?>">Date</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','publisher', $queryArray);
					?>">Publisher</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','publicationTitle', $queryArray);
					?>">Publication Title</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','journalAbbreviation', $queryArray);
					?>">Journal Abbreviation</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','language', $queryArray);
					?>">Language</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','accessDate', $queryArray);
					?>">Access Date</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','libraryCatalog', $queryArray);
					?>">Library Catalog</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','callNumber', $queryArray);
					?>">Call Number</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','rights', $queryArray);
					?>">Rights</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','addedBy', $queryArray);
					?>">Added By</a></li>
				</ul>
			</div>
			<div class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
					$orderBy = $_GET['direction'];
					echo $orderBy ? $orderBy === 'asc' ? "Ascending" : "Descending" : "Select Order";
				?> <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('direction', 'asc', $queryArray);
					?>">Ascending</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('direction','desc', $queryArray);
					?>">Descending</a></li>
				</ul>
			</div>
			<h4>Collections</h4>
			<div class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
					$collection = $_GET['collection'];
					echo $collection ? getNameById($collection) : "Select Collection";
				?> <b class="caret"></b></a>
				<ul class="dropdown-menu">
				<?php
					$collections = getItems('collection');
					if(!empty($collection)) {
						echo '<li class="dropdown-item"><a href="' . applyFilters('collection', 'select', $queryArray) . '"> ' . "Select Collection" . "</a></li>";
					}
					foreach($collections as $k=>$v) {
						$name = $v->data->name;
						$numItems = $v->meta->numItems;
						echo '<li class="dropdown-item"><a href="' . applyFilters('collection', $v->key, $queryArray) . '"> ' . $name . " ($numItems)</a></li>";
					}
				?>
				</ul>
			</div>
			<?php if($itemType === 'artwork') { ?>
				<h4>Categories</h4>
				<div class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
						$category = $_GET['category'];
						echo $category ? ucwords($category) : "All Categories";
					?> <b class="caret"></b></a>
					<ul class="dropdown-menu">
					<?php
						echo '<li class="dropdown-item"><a href="' . applyFilters('category', '', $queryArray) . '"> ' . " All Categories</a></li>";
						echo '<li class="dropdown-item"><a href="' . applyFilters('category', 'images', $queryArray) . '"> ' . " Images</a></li>";
						echo '<li class="dropdown-item"><a href="' . applyFilters('category', 'illustrations', $queryArray) . '"> ' . " Illustrations</a></li>";
						echo '<li class="dropdown-item"><a href="' . applyFilters('category', 'infographics', $queryArray) . '"> ' . " Infographics</a></li>";
						echo '<li class="dropdown-item"><a href="' . applyFilters('category', 'videos', $queryArray) . '"> ' . " Videos</a></li>";
						echo '<li class="dropdown-item"><a href="' . applyFilters('category', 'audios', $queryArray) . '"> ' . " Audios</a></li>";
					?>
					</ul>
				</div>
			<?php } ?>
		</div>	
	</div>
</div>

<?php
get_footer();