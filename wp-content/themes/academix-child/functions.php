<?php 

// Load main style css
function academix_child_custom_page_style(){
	wp_enqueue_style( 'academix-parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'academix-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'academix-parent-style' ) );
}
add_action( 'wp_enqueue_scripts', 'academix_child_custom_page_style', 155 );
add_theme_support('post-formats', array('aside'));
function fetchWithZotero($results, $searchId, $isAjax, $args) {
	$searchTerm = urlencode_deep($args['s']);
	$url = "https://api.zotero.org/groups/4545912/items?q=$searchTerm&limit=4";
	$reqArgs = array(
		'headers' => array(
			'Authorization' => 'Bearer BVY0qCEHT3YNdwrioR8bE7wM'
		)
	);
	$response = wp_remote_get($url, $reqArgs);
	$data = json_decode($response['body']);
	$totalResults = wp_remote_retrieve_header($response, 'Total-Results');
	foreach($data as $k=>$v) {
		$url;
		if(!empty($v->data->DOI)) {
			$url = "https://doi.org/" . $v->data->DOI;
		} else {
			$url = $v->data->url;
		}
		$obj = (object)['title' => $v->data->title, 'content' => '', 'link' => $url];
		$key = $v->key;
		$results = array_merge([$key => $obj], $results);
	}
	return $results;
}

add_filter( 'asp_results', 'fetchWithZotero', 3, 10);

function collectionPageCheck() {
	
	global $wp_query;
	$requestUri = parse_url($_SERVER['REQUEST_URI']);
	$path = basename( untrailingslashit($requestUri['path']));
	$limit = 5;
	if (strpos( $requestUri['path'], '/collections') === 0) {
		$pageNumber = $_GET['page'];
		$singleView = $_GET['id'];
		$start = $pageNumber > 1 ? ($pageNumber - 1) * $limit : 0;
		
		$reqArgs = array(
			'headers' => array(
				'Authorization' => 'Bearer BVY0qCEHT3YNdwrioR8bE7wM'
			)
		);

		if(!empty($singleView)) {
			$url = "https://api.zotero.org/groups/4545912/items/$singleView";
			$response = wp_remote_get($url, $reqArgs);
			$data = json_decode($response['body']);
			echo get_template_part('template-parts/collection-single', null, ['data' => $data]);
		} else {
			$url = "https://api.zotero.org/groups/4545912/items?limit=$limit&start=$start";
			$response = wp_remote_get($url, $reqArgs);
			$data = json_decode($response['body']);
			$totalResults = wp_remote_retrieve_header($response, 'Total-Results');
			echo get_template_part('template-parts/collections', null, ['data' => $data, 'total' => $totalResults, 'limit' => $limit]);
		}
		exit;
	}
}


add_action( 'pre_handle_404', 'collectionPageCheck');


function applyFilters(string $type, string $by, array $queryArray, bool $allowMultiple = false) : string {
	if(!empty($queryArray)) {
		$queryArray['page'] = 1;
		if(array_key_exists($type, $queryArray)) {
			if($allowMultiple) {
				$currentBy = $queryArray[$type];
				$currentByArray = explode("--", $currentBy);

				if(in_array($by, $currentByArray)) {
					// time to unselect, remove
					$currentByArray = array_diff($currentByArray, [$by]);
				} else {
					// time to select, add
					$currentByArray[] = $by;
				}

				$queryArray[$type] = join("--", $currentByArray);
			} else {
				if($by === 'select') {
					$queryArray[$type] = '';
				} else {
					$queryArray[$type] = $by;
				}
				
			}
			return "/?" . http_build_query($queryArray);
		} else {
			return $_SERVER['REQUEST_URI'] . "&$type=$by";
		}
	} else {
		return "/?$type=$by";
	}
}

function getSelectedItems(string $type, array $queryArray) : array {
	$currentBy = $queryArray[$type];
	return explode("--", $currentBy);
}

function includesSelf(string $type, array $queryArray, string $self) : string {
	$selectedItems = getSelectedItems($type, $queryArray);
	return in_array($self, $selectedItems) ? 'checked' : '';
}

function getBaseURL() : string {
	$collection = $_GET['collection'];
	if(!empty($collection)) {
		return "https://api.zotero.org/groups/4545912/collections/$collection/items/top";
	}

	return 'https://api.zotero.org/groups/4545912/items';
}

function getItems(string $type) : array {
	$urlPart;
	if($type === 'collection') {
		$urlPart = 'collections';
	} else {
		$urlPart = '/items/tags';
	}
	$url = "https://api.zotero.org/groups/4545912/$urlPart";
	$reqArgs = array(
		'headers' => array(
			'Authorization' => 'Bearer BVY0qCEHT3YNdwrioR8bE7wM'
		)
	);
	$response = wp_remote_get($url, $reqArgs);
	$data = json_decode($response['body']);

	return $data;
}

function getNameById(string $id) : string {
	
	$url = "https://api.zotero.org/groups/4545912/collections/$id";
	$reqArgs = array(
		'headers' => array(
			'Authorization' => 'Bearer BVY0qCEHT3YNdwrioR8bE7wM'
		)
	);
	$response = wp_remote_get($url, $reqArgs);
	$data = json_decode($response['body']);
	return $data->data->name . ' (' . $data->meta->numItems . ')';
}

function resetFilters($queryArray) : string {
	$queryArray['page'] = 1;
	$queryArray['tag'] = '';
	$queryArray['collection'] = '';
	$queryArray['sort'] = '';
	$queryArray['direction'] = '';
	$queryArray['s'] = '';

	return "/?" . http_build_query($queryArray);
}

function showBoxedItems( $atts ){
	return '<nav class="navbar navbar-inverse" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>
    <div class="navbar-collapse collapse">

        <ul class="nav navbar-nav navbar-center">
            <li><a href="#">Top Results</a></li>
            <li><a href="#">Artworks</a></li>
            <li><a href="#">Documents</a></li>
            <li><a href="#">Websites</a></li>
            <li><a href="#">Journal Articles</a></li>
            <li><a href="#">Videos</a></li>

        </ul>
    </div>
</nav>';
}
add_shortcode( 'sg-boxed-search', 'showBoxedItems' ); 

function isActive($currentType) {
	$itemType = $_GET['itemType'];

	if(($itemType === null || $itemType === "") && $currentType === "") {
		return "active-box";
	}
	if($itemType === $currentType) {
		return "active-box";
	}

	return "";
}

function getAllItemsCount($items) {
	$baseUrl = getBaseURL();
	$searchTerm = $_GET['s'];
	$finalResults = [];
	foreach ($items as $item) {
		$url = "$baseUrl?q=$searchTerm&itemType=$item";
		$reqArgs = array(
			'headers' => array(
				'Authorization' => 'Bearer BVY0qCEHT3YNdwrioR8bE7wM'
			)
		);
		$response = wp_remote_get($url, $reqArgs);
		$data = json_decode($response['body']);
		$total = wp_remote_retrieve_header($response, 'Total-Results');
		
		if($item === 'artwork') {
			$total += getArtworksCount($_GET['s']);
		}

		$finalResults[$item] = $total;
	}

	return $finalResults;
	
}

function getArtworksCount($searchTerm) {
	global $wpdb;

	$query = "
		SELECT $wpdb->posts.* 
		FROM $wpdb->posts, $wpdb->postmeta
		WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
		AND $wpdb->postmeta.meta_key = 'category'
		AND $wpdb->posts.post_status = 'publish' 
		AND $wpdb->posts.post_type = 'post'
		AND $wpdb->posts.post_title LIKE '%$searchTerm%'
	";
	$result = $wpdb->get_results($query);
	return count($result);
}

add_filter( 'posts_where', 'qirolab_posts_where', 10, 2 );
function qirolab_posts_where( $where, $wp_query )
{
    global $wpdb;
    if ( $title = $wp_query->get( 'search_title' ) ) {
        $where .= " AND " . $wpdb->posts . ".post_title LIKE '%" . esc_sql( $wpdb->esc_like( $title ) ) . "%'";
    }
    return $where;
}

