<?php



    /****************************************************************************************
    *
    *     ZOTPRESS BASIC IMPORT FUNCTIONS
    *
    ****************************************************************************************/

    if ( ! function_exists('zp_db_prep') )
    {
        function zp_db_prep ($input)
        {
            $input =  str_replace("%", "%%", $input);
            return ($input);
        }
    }


    if ( ! function_exists('zp_extract_year') )
    {
        function zp_extract_year ($date)
        {
    		if ( strlen($date) > 0 ):
    			preg_match_all( '/(\d{4})/', $date, $matches );
    			if ( isset($matches[0][0]) ):
    				return $matches[0][0];
    			else:
    				return "";
    			endif;
    		else:
    			return "";
    		endif;
        }
    }


    if ( ! function_exists('zp_get_api_user_id') )
    {
        function zp_get_api_user_id ($api_user_id_incoming=false)
        {
            if (isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1)
                $api_user_id = htmlentities($_GET['api_user_id']);
            else if ($api_user_id_incoming !== false)
                $api_user_id = $api_user_id_incoming;
            else
                $api_user_id = false;

            return $api_user_id;
        }
    }


    if ( ! function_exists('zp_get_account') )
    {
        function zp_get_account ($wpdb, $api_user_id_incoming=false)
        {
            if ($api_user_id_incoming !== false)
    		{
                $zp_account = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='%s'",
                        $api_user_id_incoming
                    )
                );
    		}
            else
    		{
                $zp_account = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY id DESC LIMIT 1");
    		}

            return $zp_account;
        }
    }



    if ( ! function_exists('zp_clear_cache_for_user') )
    {
        function zp_clear_cache_for_user ($wpdb, $api_user_id)
        {
            $wpdb->query("DELETE FROM ".$wpdb->prefix."zotpress_cache WHERE api_user_id='".$api_user_id."'");
        }
    }


    if ( ! function_exists('zp_check_author_continue') )
    {
    	// Takes single author
    	function zp_check_author_continue( $item, $author )
    	{
    		$author_continue = false;
    		$author = strtolower($author);

    		// Accounts for last names with: de, van, el, seif
    		if ( strpos( strtolower($author), "van " ) !== false )
    		{
    			$author = explode( "van ", $author );
    			$author[1] = "van ".$author[1];
    		}
    		else if ( strpos( strtolower($author), "de " ) !== false )
    		{
    			$author = explode( "de ", $author );
    			$author[1] = "de ".$author[1];
    		}
    		else if ( strpos( strtolower($author), "el " ) !== false )
    		{
    			$author = explode( "el ", $author );
    			$author[1] = "el ".$author[1];
    		}
    		else if ( strpos( strtolower($author), "seif " ) !== false )
    		{
    			$author = explode( "seif ", $author );
    			$author[1] = "seif ".$author[1];
    		}
    		else // Multipart names
    		{
    			// First and last names OR multiple last names
    			if ( strpos( strtolower($author), " " ) !== false )
    			{
    				$author = explode( " ", $author );

    				// Deal with multiple blanks
                    // NOTE: Previously assumed multiple first/middle names
                    // CHANGED: Check this possibility as well as multiple (7.3)
                    // last names; so keep array of 1-3+ items
    				// if ( count($author) > 2 )
    				// {
    				// 	$new_name = array();
    				// 	foreach ( $author as $num => $author_name )
    				// 	{
    				// 		if ( $num == 0 ) $new_name[0] .= $author_name;
    				// 		else if ( $num != count($author)-1 ) $new_name[0] .= " ". $author_name;
    				// 		else if ( $num == count($author)-1 ) $new_name[1] .= $author_name;
    				// 	}
    				// 	$author = $new_name;
    				// }
    			}
    			else // Multi-part last name with plus sign separator
    			{
                    // REVIEW: What if there's a first name(s)?
                    if ( strpos( strtolower($author), "+" ) !== false )
                    {
                        // $author = explode( "+", $author );
                        $author = array( str_replace( "+", " ", $author ) );
                    }
                    else // Just last name
                    {
                        $author = array( $author );
                    }
    			}
    		}

    		// Deal with blank firstname
    		if ( $author[0] == "" )
    		{
    			$author[0] = $author[1];
    			unset( $author[1] );
    		}

    		// Trim firstname
            // QUESTION: Is this needed?
    		$author[0] = trim($author[0]);

    		// Check
    		foreach ( $item->data->creators as $creator )
    		{
                // NOTE: Assumes last name only
    			if ( count($author) == 1 )
    			{
    				if ( ( isset($creator->lastName)
                            && strtolower($creator->lastName) == $author[0] )
    						|| ( isset($creator->name)
                                    && strtolower($creator->name) == $author[0] ) )
    					$author_continue = true;
    			}
                // NOTE: Assumes first and last names OR two last names
    			elseif ( count($author) == 2 )
    			{
    				if ( ( isset($creator->firstName)
						&& ( strtolower($creator->firstName) == $author[0]
						&& strtolower($creator->lastName) == $author[1] )
                       )
                       || ( strtolower($creator->lastName) == $author[0]." ".$author[1] )
                       || ( isset($creator->name)
                            && ( strtolower($creator->name) == implode(" ", $author) ) ) )
    					$author_continue = true;
    			}
                else {
                    // NOTE: Assumes multiple first (inc. middle) OR multiple last
                    // with at least one first name
                    // CHANGED: Fix for multiple last names (7.3)
                    if 	(
                        // Two first names (or one middle) and last name
                        ( isset($creator->firstName)
							&& ( strtolower($creator->firstName) == ($author[0]." ".$author[1])
			                     && strtolower($creator->lastName) == $author[2] ) )
                        // One first name and two last names
						|| ( isset($creator->firstName)
                            && ( strtolower($creator->firstName) == $author[0]
                                && strtolower($creator->lastName) == ($author[1]." ".$author[2]) ) )
                        // All combined
                        || ( isset($creator->name)
                                && strtolower($creator->name) == implode(" ", $author) )
                    )
    					$author_continue = true;
                }
    		}

    		return $author_continue;

    	} // function zp_check_author_continue
    }

?>
