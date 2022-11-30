<?php
namespace TenWebOptimizer;

class OptimizerStyles extends OptimizerBase
{
    const TWO_DELAYED_CSS_ATTRIBUTE = 'data-twodelayedcss';
    const ASSETS_REGEX              = '/url\s*\(\s*(?!["\']?data:)(?![\'|\"]?[\#|\%|])([^)]+)\s*\)([^;},\s]*)/i';
    /**
     * Font-face regex-fu from HamZa at: https://stackoverflow.com/a/21395083
     * ~
     *
     * @font-face\s* # Match @font-face and some spaces
     * (             # Start group 1
     * \{            # Match {
     * (?:           # A non-capturing group
     * [^{}]+        # Match anything except {} one or more times
     * |             # Or
     * (?1)          # Recurse/rerun the expression of group 1
     * )*            # Repeat 0 or more times
     * \}            # Match }
     * )             # End group 1
     * ~xs';
     */
    const FONT_FACE_REGEX  = '~@font-face\s*(\{(?:[^{}]+|(?1))*\})~xsi'; // added `i` flag for case-insensitivity.
    const IMPORT_URL_REGEX = '/@import.*url.*\(.*[\'|"](.*)[\'|"].*\)/Umsi';
    private $css = array();
    private $csscode = array();
    private $url = array();
    private $restofcontent = '';
    private $datauris = false;
    private $hashmap = array();
    private $alreadyminified = false;
    private $aggregate = false;
    private $inline = false;
    private $defer = false;
    private $defer_inline = false;
    private $whitelist = '';
    private $cssinlinesize = '';
    private $cssremovables = array();
    private $cssdisables = array();
    private $include_inline = false;
    private $inject_min_late = '';
    private $dontmove = array();
    private $options = array();
    private $minify_excluded = true;
    private $current_url = null;
    private $url_data = null;
    private $async_type = "stylesheet";
    private $font_swap = false;
    private $two_load_fonts_via_webfont = false;
    public $webFont_list = array();
    public $critical = null;
    // public $cdn_url; // Used all over the place implicitly, so will have to be either public or protected :/ .
    // Reads the page and collects style tags.
    /**
     * @var false|mixed|void
     */
    private $criticalCss;
    /**
     * @var array
     */
    private $hashes;
    /**
     * @var OptimizerCacheStructure
     */
    private $cacheStructure;

    /**
     * OptimizerStyles constructor.
     *
     * @param string                  $content
     * @param OptimizerCacheStructure $cacheStructure
     */


    public $two_async_css_arr = array();
    public $critical_fonts_arr = array();
    public $use_uncritical = false;
    private $TwoSettings;

    public function __construct($content, $cacheStructure, $critical = null)
    {
        global $TwoSettings;
        $this->TwoSettings = $TwoSettings;
        $this->critical = $critical;
        parent::__construct($content);
        $this->cacheStructure = $cacheStructure;
    }

    public function read($options)
    {
        $excludeCSS = $options['css_exclude'];
        if ('' !== $excludeCSS) {
            $this->dontmove = array_filter(array_map('trim', explode(',', $excludeCSS)));
        } else {
            $this->dontmove = array();
        }
        // forcefully exclude CSS with data-noptimize attrib.
        $this->dontmove[] = 'data-noptimize';
        $this->dontmove[] = 'two_critical_bg';

        if($this->critical->critical_enabled && $this->critical->use_uncritical && $this->critical->status =="success" && isset($this->critical->uncritical_css)){
            $this->use_uncritical = true;
            return;
        }
        $this->replaceOptions($options);
        $this->current_url = OptimizerUtils::get_page_url();
        $this->url_data = OptimizerUtils::remove_domain_part($this->current_url);
        $this->font_swap = empty($this->TwoSettings->get_settings("two_async_font")) ? false : true;
        $this->two_load_fonts_via_webfont = empty($this->TwoSettings->get_settings("two_load_fonts_via_webfont")) ? false : true;
        $two_disable_css = $this->TwoSettings->get_settings("two_disable_css");
        $two_disable_css_page = $this->TwoSettings->get_settings("two_disable_page");
        if (is_array($two_disable_css_page) && (isset($two_disable_css_page[$this->url_data]) || isset($two_disable_css_page[$this->current_url]))) {
            if (isset($two_disable_css) && !empty($two_disable_css)) {
                if (isset($two_disable_css_page[$this->url_data])) {
                    $two_disable_css .= "," . $two_disable_css_page[$this->url_data];
                }
                if (isset($two_disable_css_page[$this->current_url])) {
                    $two_disable_css .= "," . $two_disable_css_page[$this->current_url];
                }
            } else {
                if (isset($two_disable_css_page[$this->url_data])) {
                    $two_disable_css = $two_disable_css_page[$this->url_data];
                }
                if (isset($two_disable_css_page[$this->current_url])) {
                    if (isset($two_disable_css) && !empty($two_disable_css)) {
                        $two_disable_css .= "," . $two_disable_css_page[$this->current_url];
                    } else {
                        $two_disable_css .= $two_disable_css_page[$this->current_url];
                    }
                }
            }
        }

        $two_disable_css = explode(",", $two_disable_css);
        $this->cssdisables = array_filter($two_disable_css);

        $this->cssinlinesize = 256;
        // filter to "late inject minified CSS", default to true for now (it is faster).
        $this->inject_min_late = true;
        // Remove everything that's not the header.
        if ($options['justhead']) {
            $content = explode('</head>', $this->content, 2);
            $this->content = $content[0] . '</head>';
            $this->restofcontent = $content[1];
        }
        // Determine whether we're doing CSS-files aggregation or not.
        if (isset($options['aggregate'])) {
            $this->aggregate = $options['aggregate'];
        }

        // Returning true for "dontaggregate" turns off aggregation.
        // include inline?
        if ($options['include_inline'] && $this->aggregate) {
            $this->include_inline = true;
        }

        // Should we defer css?
        // value: true / false.
        $this->defer = $options['defer'];
        // Should we inline while deferring?
        // value: inlined CSS.
        $this->defer_inline = $options['defer_inline'];
        // Should we inline?
        // value: true / false.
        $this->inline = $options['inline'];
        // Store cdn url.
        $this->cdn_url = $options['cdn_url'];
        // Store data: URIs setting for later use.
        $this->datauris = $options['datauris'];
        // Determine whether excluded files should be minified if not yet so.
        /*        if ( !$options['minify_excluded'] && $options['aggregate'] ) {
                    $this->minify_excluded = FALSE;
                }*/

        $this->minify_excluded = $options['minify_excluded'];

        if ($this->aggregate) {
            $this->minify_excluded = false;
        }

        // noptimize me.
        $this->content = $this->hide_noptimize($this->content);
        // Exclude (no)script, as those may contain CSS which should be left as is.
        $this->content = $this->replace_contents_with_marker_if_exists('SCRIPT', '<script', '#<(?:no)?script.*?<\/(?:no)?script>#is', $this->content);
        // Save IE hacks.
        $this->content = $this->hide_iehacks($this->content);
        // Hide HTML comments.
        $this->content = $this->hide_comments($this->content);
        // Get <style> and <link>.
        $current_media = "all";
        if (preg_match_all('#(<style[^>]*>.*</style>)|(<link[^>]*stylesheet[^>]*>)#Usmi', $this->content, $matches)) {
            foreach ($matches[0] as $tag) {
                if ($this->isremovable($tag, $this->cssremovables)) {
                    $this->content = str_replace($tag, '', $this->content);
                    $this->cacheStructure->addToTagsToReplace($tag, "");
                }
                if ($this->is_disable($tag, $this->cssdisables)) {
                    $this->content = str_replace($tag, '', $this->content);
                    $this->cacheStructure->addToTagsToReplace($tag, "");
                } else if ($this->ismovable($tag)) {
                    // Get the media.
                    $replace_tag = "";
                    if (false !== strpos($tag, 'media=')) {
                        preg_match('#media=(?:"|\')([^>]*)(?:"|\')#Ui', $tag, $medias);
                        $medias = explode(',', $medias[1]);
                        $media = array();
                        foreach ($medias as $elem) {
                            if (empty($elem)) {
                                $elem = 'all';
                                if($this->options["async_all"]){
                                    $elem = "all_none";
                                    $current_media = 'all';
                                }
                            }
                            if ($this->is_async($tag)) {
                                $current_media = $elem;
                                $elem = $elem . "_none";
                            }
                            $media[] = $elem;
                        }
                    } else {
                        // No media specified - applies to all.
                        $media = array('all');
                        if($this->options["async_all"]){
                            $media = array('all_none');
                        }
                    }
                    if (preg_match('#<link.*href=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                        // <link>.
                        $url = current(explode('?', $source[2], 2));
                        $current_url = $source[2];
                        $path = $this->getpath($url);
                        if (false !== $path && preg_match('#\.css$#', $path)) {
                            // Good link.
                            $this->css[md5($path)] = array($media, $path);
                        } else {
                            $new_tag = "";
                            if (strpos($source[2], "fonts.googleapis")) {
                                $font_family = OptimizerUtils::get_url_query($source[2], "family");
                                if (!$this->is_async($tag) && $this->two_load_fonts_via_webfont && $font_family) {
                                    $this->content = str_replace($tag, "", $this->content);
                                    $this->cacheStructure->addToTagsToReplace($tag, "");
                                    $font_family = explode("|", $font_family);
                                    foreach ($font_family as $font) {
                                        $this->webFont_list[] = $font;
                                    }
                                } else if ($this->font_swap) {
                                    $google_fonts_src = OptimizerUtils::replace_google_font_url($source[2]);
                                    $current_url = $google_fonts_src;
                                    $new_tag = str_replace($source[2], $google_fonts_src, $tag);
                                    $this->content = str_replace($tag, $new_tag, $this->content);
                                    $this->cacheStructure->addToTagsToReplace($tag, $new_tag);
                                    $tag = $new_tag;
                                }
                            } else {
                                $new_tag = $tag;
                            }
                            if ($new_tag !== '' && $new_tag !== $tag && !strpos($source[2], "fonts.googleapis")) {
                                if ($this->is_async($tag)) {
                                    $this->two_async_css_arr[] = array(
                                        'url' => $current_url,
                                        'media' => $current_media
                                    );
                                    $new_tag = "";
                                }
                                $this->content = str_replace($tag, $new_tag, $this->content);
                                $this->cacheStructure->addToTagsToReplace($tag, $new_tag);
                            }
                            // Link is dynamic (.php etc).
                            if ($this->is_async($tag)) {
                                $this->two_async_css_arr[] = array(
                                  'url' => $current_url,
                                  'media' => $current_media
                                );
                                $replace_tag = "";
                            } else {
                                $tag = "";
                            }
                        }
                    } else {
                        //optimize inline styles
                        list($originalCode, $code) = $this->optimizeInlineStyle($tag);
                        if ($this->include_inline) {
                            $this->css[md5($code)] = array($media, 'INLINE;' . $code);
                        } else {
                            //here we change inline styles code inside <style> tag to optimized one
                            $id_empty_tag = preg_replace('/\s+/', '', $originalCode);
                            if (!empty($id_empty_tag)) {
                                $tag = $originalCode;
                                $replace_tag = $code;
                            }
                        }
                    }
                    // Remove the original style tag.
                    $this->content = str_replace($tag, $replace_tag, $this->content, $changesMade);
                    $this->cacheStructure->addToTagsToReplace($tag, $replace_tag);
                } else {
                    if (preg_match('#<link.*href=("|\')(.*)("|\')#Usmi', $tag, $source)) {

                        $exploded_url = explode('?', $source[2], 2);
                        $url = $exploded_url[0];
                        $path = $this->getpath($url);
                        $new_tag = $tag;
                        // Excluded CSS, minify that file:
                        // -> if aggregate is on and exclude minify is on
                        // -> if aggregate is off and the file is not in dontmove.

                        if ($this->is_async($tag)) {
                            $this->two_async_css_arr[] = array(
                                'url' => $source[2],
                                'media' => "all"
                            );
                            $new_tag = "";
                        }

                        if ($path && $this->minify_excluded) {
                            $consider_minified_array = false;
                            if ((false === $this->aggregate && str_replace($this->dontmove, '', $path) === $path) || (true === $this->aggregate && (false === $consider_minified_array || str_replace($consider_minified_array, '', $path) === $path))) {
                                $minified_url = $this->minify_single($path);
                                if (!empty($minified_url)) {
                                    // Replace orig URL with cached minified URL.
                                    $new_tag = str_replace($url, $minified_url, $tag);

                                }
                            }
                        }
                        // And replace!
                        if ($new_tag !== '' && $new_tag !== $tag) {
                            $this->content = str_replace($tag, $new_tag, $this->content);
                            $this->cacheStructure->addToTagsToReplace($tag, $new_tag);
                        }
                    } else {
                        //optimize inline styles
                        list($originalCode, $code) = $this->optimizeInlineStyle($tag);
                        if ($code !== '' && $code !== $originalCode) {
                            $this->content = str_replace($originalCode, $code, $this->content);
                            $this->cacheStructure->addToTagsToReplace($originalCode, $code);
                        }
                    }
                }
            }

            return $this->content;
        }

        // Really, no styles?
        return false;
    }

    /**
     * Run CSS optimization for code inside style tag and returns array of original and optimized code
     *
     * @param $tag
     *
     * @return array [$originalCode, $optimizedCode]
     */
    private function optimizeInlineStyle($tag)
    {
        $cssMinifier = new OptimizerCSSMin();
        // Inline css in style tags can be wrapped in comment tags, so restore comments.
        $tag = $this->restore_comments($tag);
        preg_match('#<style.*>(.*)</style>#Usmi', $tag, $code);
        if (empty($code)) {
            return ['', ''];
        }
        $originalCode = $code[1];
        // And re-hide them to be able to to the removal based on tag.
        $tag = $this->hide_comments($tag);
        $code = preg_replace('#^.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*$#sm', '$1', $code[1]);
        //run optimizations without minifying
        $code = $cssMinifier->run($code, false);

        return [$originalCode, $code];
    }

    private function is_async($tag)
    {
        if ($this->options["disable_async"]) {
            return false;
        }
        if (!$this->ismovable($tag)) {
            return false;
        }
        if (is_array($this->options) && isset($this->options["async_all"]) && $this->options["async_all"]) {
            return true;
        }
        $two_async_css_list = $this->TwoSettings->get_settings("two_async_css");
        $two_async_page = $this->TwoSettings->get_settings("two_async_page");
        if (is_array($two_async_page) && (isset($two_async_page[$this->url_data]) || isset($two_async_page[$this->current_url]))) {
            if (isset($two_async_css_list) && !empty($two_async_css_list)) {
                if (isset($two_async_page[$this->url_data])) {
                    $two_async_css_list .= "," . $two_async_page[$this->url_data];
                }
                if (isset($two_async_page[$this->current_url])) {
                    $two_async_css_list .= "," . $two_async_page[$this->current_url];
                }
            } else {
                if (isset($two_async_page[$this->url_data])) {
                    $two_async_css_list = $two_async_page[$this->url_data];
                }
                if (isset($two_async_page[$this->current_url])) {
                    if (!isset($two_async_css_list) && !empty($two_async_css_list)) {
                        $two_async_css_list .= "," . $two_async_page[$this->current_url];
                    } else {
                        $two_async_css_list = $two_async_page[$this->current_url];
                    }
                }
            }
        }
        $two_async_css = array();
        if (isset($two_async_css_list) && $two_async_css_list != false) {
            $two_async_css = explode(",", str_replace(' ', '', $two_async_css_list));
        }
        $flag = false;
        foreach ($two_async_css as $val) {
            if ($flag) {
                break;
            }
            if (!empty($val)) {
                $pos = strpos($tag, $val);
                if ($pos !== false) {
                    $flag = true;
                }
            }
        }
        if ($flag) {
            return true;
        }

        return false;
    }


    /**
     * Checks if the local file referenced by $path is a valid
     * candidate for being inlined into a data: URI
     *
     * @param string $path
     *
     * @return boolean
     */
    private function is_datauri_candidate($path)
    {
        // Call only once since it's called from a loop.
        static $max_size = null;
        if (null === $max_size) {
            $max_size = $this->get_datauri_maxsize();
        }
        if ($path && preg_match('#\.(jpe?g|png|gif|webp|bmp)$#i', $path) && file_exists($path) && is_readable($path) && filesize($path) <= $max_size) {

            // Seems we have a candidate.
            $is_candidate = true;
        } else {
            // Filter allows overriding default decision (which checks for local file existence).
            $is_candidate = false;
        }

        return $is_candidate;
    }

    private function get_datauri_maxsize()
    {
        static $max_size = null;
        /**
         * No need to apply the filter multiple times in case the
         * method itself is invoked multiple times during a single request.
         * This prevents some wild stuff like having different maxsizes
         * for different files/site-sections etc. But if you're into that sort
         * of thing you're probably better of building assets completely
         * outside of WordPress anyway.
         */
        if (null === $max_size) {
            $max_size = 4096;
        }

        return $max_size;
    }

    private function check_datauri_exclude_list($url)
    {
        static $exclude_list = null;
        $no_datauris = array();
        // Again, skip doing certain stuff repeatedly when loop-called.
        if (null === $exclude_list) {
            $exclude_list = '';
            $no_datauris = array_filter(array_map('trim', explode(',', $exclude_list)));
        }
        $matched = false;
        if (!empty($exclude_list)) {
            foreach ($no_datauris as $no_datauri) {
                if (false !== strpos($url, $no_datauri)) {
                    $matched = true;
                    break;
                }
            }
        }

        return $matched;
    }

    private function build_or_get_datauri_image($path, $hashes)
    {
        $hash = md5($path);
        $check = new OptimizerCache($hash, 'img', $hashes);
        if ($check->check()) {
            // we have the base64 image in cache.
            $headAndData = $check->retrieve();
            $_base64data = explode(';base64,', $headAndData);
            $base64data = $_base64data[1];
            unset($_base64data);
        } else {
            // It's an image and we don't have it in cache, get the type by extension.
            $exploded_path = explode('.', $path);
            $type = end($exploded_path);
            switch ($type) {
                case 'jpg':
                case 'jpeg':
                    $dataurihead = 'data:image/jpeg;base64,';
                    break;
                case 'gif':
                    $dataurihead = 'data:image/gif;base64,';
                    break;
                case 'png':
                    $dataurihead = 'data:image/png;base64,';
                    break;
                case 'bmp':
                    $dataurihead = 'data:image/bmp;base64,';
                    break;
                case 'webp':
                    $dataurihead = 'data:image/webp;base64,';
                    break;
                default:
                    $dataurihead = 'data:application/octet-stream;base64,';
            }
            // Encode the data.
            $base64data = base64_encode(file_get_contents($path));
            $headAndData = $dataurihead . $base64data;
            // Save in cache.
            $check->cache($headAndData, 'text/plain');
        }
        unset($check);

        return array('full' => $headAndData, 'base64data' => $base64data);
    }

    /**
     * Given an array of key/value pairs to replace in $string,
     * it does so by replacing the longest-matching strings first.
     *
     * @param string $string
     * @param array  $replacements
     *
     * @return string
     */
    protected static function replace_longest_matches_first($string, $replacements = array())
    {
        if (!empty($replacements)) {
            // Sort the replacements array by key length in desc order (so that the longest strings are replaced first).
            $keys = array_map('strlen', array_keys($replacements));
            array_multisort($keys, SORT_DESC, $replacements);
            $string = str_replace(array_keys($replacements), array_values($replacements), $string);
        }

        return $string;
    }

    public function replace_urls($code = '')
    {
        $replacements = array();
        preg_match_all(self::ASSETS_REGEX, $code, $url_src_matches);
        if (is_array($url_src_matches) && !empty($url_src_matches)) {
            foreach ($url_src_matches[1] as $count => $original_url) {
                // Removes quotes and other cruft.
                $url = trim($original_url, " \t\n\r\0\x0B\"'");
                if (!empty($this->cdn_url)) {
                    $replacement_url = $this->url_replace_cdn($url);
                    // Prepare replacements array.
                    $replacements[$url_src_matches[1][$count]] = str_replace($original_url, $replacement_url, $url_src_matches[1][$count]);
                }
            }
        }
        $code = self::replace_longest_matches_first($code, $replacements);

        return $code;
    }

    public function hide_fontface_and_maybe_cdn($code)
    {
        // Proceed only if @font-face declarations exist within $code.
        preg_match_all(self::FONT_FACE_REGEX, $code, $fontfaces);
        if (isset($fontfaces[0])) {
            // Check if we need to cdn fonts or not.
            $do_font_cdn = false;
            foreach ($fontfaces[0] as $full_match) {
                // Keep original match so we can search/replace it.
                $match_search = $full_match;
                // Do font cdn if needed.
                if ($do_font_cdn) {
                    $full_match = $this->replace_urls($full_match);
                }
                // Replace declaration with its base64 encoded string.
                $replacement = self::build_marker('FONTFACE', $full_match);
                $code = str_replace($match_search, $replacement, $code);
            }
        }

        return $code;
    }

    /**
     * Restores original @font-face declarations that have been "hidden"
     * using `hide_fontface_and_maybe_cdn()`.
     *
     * @param string $code
     *
     * @return string
     */
    public function restore_fontface($code)
    {
        return $this->restore_marked_content('FONTFACE', $code);
    }

    // Re-write (and/or inline) referenced assets.
    public function rewrite_assets($code, $hashes)
    {
        // Handle @font-face rules by hiding and processing them separately.
        $code = $this->hide_fontface_and_maybe_cdn($code);
        // Re-write (and/or inline) URLs to point them to the CDN host.
        $url_src_matches = array();
        $imgreplace = array();
        // Matches and captures anything specified within the literal `url()` and excludes those containing data: URIs.
        preg_match_all(self::ASSETS_REGEX, $code, $url_src_matches);
        if (is_array($url_src_matches) && !empty($url_src_matches)) {
            foreach ($url_src_matches[1] as $count => $original_url) {
                // Removes quotes and other cruft.
                $url = trim($original_url, " \t\n\r\0\x0B\"'");
                // If datauri inlining is turned on, do it.
                $inlined = false;
                if ($this->datauris) {
                    $iurl = $url;
                    if (false !== strpos($iurl, '?')) {
                        $iurl = strtok($iurl, '?');
                    }
                    $ipath = $this->getpath($iurl);
                    $excluded = $this->check_datauri_exclude_list($ipath);
                    if (!$excluded) {
                        $is_datauri_candidate = $this->is_datauri_candidate($ipath);
                        if ($is_datauri_candidate) {
                            $datauri = $this->build_or_get_datauri_image($ipath, $hashes);
                            $base64data = $datauri['base64data'];
                            // Add it to the list for replacement.
                            $imgreplace[$url_src_matches[1][$count]] = str_replace($original_url, $datauri['full'], $url_src_matches[1][$count]);
                            $inlined = true;
                        }
                    }
                }
                /**
                 * Doing CDN URL replacement for every found match (if CDN is
                 * specified). This way we make sure to do it even if
                 * inlining isn't turned on, or if a resource is skipped from
                 * being inlined for whatever reason above.
                 */
                if (!$inlined && (!empty($this->cdn_url))) {
                    // Just do the "simple" CDN replacement.
                    $replacement_url = $this->url_replace_cdn($url);
                    $imgreplace[$url_src_matches[1][$count]] = str_replace($original_url, $replacement_url, $url_src_matches[1][$count]);
                }
            }
        }
        $code = self::replace_longest_matches_first($code, $imgreplace);
        // Replace back font-face markers with actual font-face declarations.
        $code = $this->restore_fontface($code);

        return $code;
    }

    // Joins and optimizes CSS.
    public function optimize()
    {
        /*  $cachedData = OptimizerCache::filterThroughCache($this->css);
        $this->css = $cachedData['scripts'];
        if(!empty($cachedData['code'])){
            $this->csscode = $cachedData['code'];
        }*/
        foreach ($this->css as $styleHash => $group) {
            list($media, $css) = $group;
            $file_base_name = "";
            if (is_file($css)) {
                if (isset($file_base_name)) {
                    $file_base_name = basename($css);
                }
            }
            $cssPath = "";
            if (preg_match('#^INLINE;#', $css)) {
                // <style>.
                $css = preg_replace('#^INLINE;#', '', $css);
                $css = self::fixurls(ABSPATH . 'index.php', $css); // ABSPATH already contains a trailing slash.
                $this->hashes[] = $styleHash;
            } else {
                // <link>
                if (false !== $css && file_exists($css) && is_readable($css)) {
                    $cssPath = $css;
                    $css = self::fixurls($cssPath, file_get_contents($cssPath));
                    $css = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $css);
                    $tmpstyle = $this->css_snippetcacher($css, "", $styleHash);

                    if (!empty($tmpstyle)) {
                        $css = $tmpstyle;
                        $this->alreadyminified = true;
                    } else if ($this->can_inject_late($cssPath, $css)) {
                        $css = self::build_injectlater_marker($cssPath, md5($css));
                    }
                    $this->hashes[] = $styleHash;
                } else {
                    // Couldn't read CSS. Maybe getpath isn't working?
                    $css = '';
                }
            }

            foreach ($media as $elem) {
                if (!empty($css)) {
                    if(!empty($elem)){
                        $css_media = $elem;
                        $pos = strpos($css_media, "_none");
                        if ($pos) {
                            $css_media = str_replace("_none", "", $css_media);
                        }else{
                            $elem = "all";
                        }
                        if($css_media != "all"){
                            $css= "@media ".$css_media."{ ".$css." }";
                        }
                    }
                    if (!isset($this->csscode[$elem])) {
                        $this->csscode[$elem] = '';
                    }
                    $this->csscode[$elem] .= "\n\n/*FILESTART  " . ($cssPath ? $cssPath : '') . " */\n" . $css;
                }
            }
        }
        // Check for duplicate code.
        $md5list = array();
        $tmpcss = $this->csscode;
        foreach ($tmpcss as $media => $code) {
            $md5sum = md5($code);
            $medianame = $media;
            foreach ($md5list as $med => $sum) {
                // If same code.
                if ($sum === $md5sum) {
                    // Add the merged code.
                    $medianame = $med . ', ' . $media;
                    $this->csscode[$medianame] = $code;
                    $md5list[$medianame] = $md5list[$med];
                    unset($this->csscode[$med], $this->csscode[$media], $md5list[$med]);
                }
            }
            $md5list[$medianame] = $md5sum;
        }
        unset($tmpcss);
        // Manage @imports, while is for recursive import management.
        foreach ($this->csscode as &$thiscss) {
            // Flag to trigger import reconstitution and var to hold external imports.
            $fiximports = false;
            $external_imports = '';
            // remove comments to avoid importing commented-out imports.
            $thiscss_nocomments = preg_replace('#/\*.*\*/#Us', '', $thiscss);
            while (preg_match_all('#@import +(?:url)?(?:(?:\((["\']?)(?:[^"\')]+)\1\)|(["\'])(?:[^"\']+)\2)(?:[^,;"\']+(?:,[^,;"\']+)*)?)(?:;)#mi', $thiscss_nocomments, $matches)) {
                foreach ($matches[0] as $import) {
                    if ($this->isremovable($import, $this->cssremovables)) {
                        $thiscss = str_replace($import, '', $thiscss);
                        $import_ok = true;
                    } else {
                        $url = trim(preg_replace('#^.*((?:https?:|ftp:)?//.*\.css).*$#', '$1', trim($import)), " \t\n\r\0\x0B\"'");
                        $path = $this->getpath($url);
                        $import_ok = false;
                        if (file_exists($path) && is_readable($path)) {
                            $code = addcslashes(self::fixurls($path, file_get_contents($path)), "\\");
                            $code = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $code);
                            $tmpstyle = $code;
                            if (!empty($tmpstyle)) {
                                $code = $tmpstyle;
                                $this->alreadyminified = true;
                            } else if ($this->can_inject_late($path, $code)) {
                                $code = self::build_injectlater_marker($path, md5($code));
                            }
                            if (!empty($code)) {
                                $tmp_thiscss = preg_replace('#(/\*FILESTART\*/.*)' . preg_quote($import, '#') . '#Us', '/*FILESTART2*/' . $code . '$1', $thiscss, -1, $replaceCount);
                                if (!empty($tmp_thiscss) && !empty($replaceCount)) {
                                    $thiscss = $tmp_thiscss;
                                    $import_ok = true;
                                    unset($tmp_thiscss);
                                }
                            }
                            unset($code);
                        }
                    }
                    if (!$import_ok) {
                        // External imports and general fall-back.
                        $external_imports .= $import;
                        $thiscss = str_replace($import, '', $thiscss);
                        $fiximports = true;
                    }
                }
                $thiscss = preg_replace('#/\*FILESTART\*/#', '', $thiscss);
                $thiscss = preg_replace('#/\*FILESTART2\*/#', '/*FILESTART*/', $thiscss);
                // and update $thiscss_nocomments before going into next iteration in while loop.
                $thiscss_nocomments = preg_replace('#/\*.*\*/#Us', '', $thiscss);
            }

            unset($thiscss_nocomments);
            // Add external imports to top of aggregated CSS.
            if ($fiximports) {
                $thiscss = $external_imports . $thiscss;
            }
        }
        unset($thiscss);
        // $this->csscode has all the uncompressed code now.
        foreach ($this->csscode as &$code) {
            // Check for already-minified code.
            $hash = md5($code);
            $ccheck = new OptimizerCache($hash, 'css', $this->hashes);
            if ($ccheck->check()) {
                $code = $ccheck->retrieve();
                $this->hashmap[md5($code)] = $hash;
                continue;
            }
            unset($ccheck);
            // Rewrite and/or inline referenced assets.
            $code = $this->rewrite_assets($code, $this->hashes);
            // Load Google fonts via webfont
            $code = $this->get_replace_GoogleFonts($code);
            // Minify.
            $code = $this->run_minifier_on($code);
            // Bring back INJECTLATER stuff.
            $code = $this->inject_minified($code);

            // Filter results.
            $tmp_code = $code;
            if (!empty($tmp_code)) {
                $code = $tmp_code;
                unset($tmp_code);
            }
            $this->hashmap[md5($code)] = $hash;
        }
        unset($code);

        return true;
    }

    /*replace google fonts to empty string for WebFont*/
    public function get_replace_GoogleFonts($code)
    {

        preg_match_all(self::IMPORT_URL_REGEX, $code, $matches, PREG_SET_ORDER, 0);
        if (is_array($matches)) {
            foreach ($matches as $font_el) {
                if (isset($font_el[0]) && isset($font_el[1])) {
                    if (filter_var($font_el[1], FILTER_VALIDATE_URL) != false) {
                        $url = $font_el[1];
                        $font_family = OptimizerUtils::get_url_query($url, "family");
                        if ($font_family) {
                            $code = str_replace($font_el[0], "", $code);
                            $font_family = explode("|", $font_family);
                            foreach ($font_family as $font) {
                                $this->webFont_list[] = $font;
                            }
                        }
                    }
                }
            }
        }

        return $code;
    }

    public function run_minifier_on($code)
    {
        if (!$this->alreadyminified) {
            $do_minify = true;
            if ($do_minify) {
                $cssmin = new OptimizerCSSMin();
                $tmp_code = trim($cssmin->run($code));
                if (!empty($tmp_code)) {
                    $code = $tmp_code;
                    unset($tmp_code);
                }
            }
        }

        return $code;
    }

    // Caches the CSS in uncompressed, deflated and gzipped form.
    public function cache()
    {
        // CSS cache.
        foreach ($this->csscode as $media => $code) {
            $md5 = $this->hashmap[md5($code)];
            $cache = new OptimizerCache($md5, 'css', $this->hashes, $media);
            if (!$cache->check()) {
                $cache->cache($code, 'text/css');
            }
            $this->url[$media] = TWO_CACHE_URL . $cache->getname();
        }
    }

    // Returns the content.
    public function getcontent()
    {
        if (!empty($this->restofcontent)) {
            $this->content .= $this->restofcontent;
            $this->restofcontent = '';
        }
        // Inject the new stylesheets.
        $replaceTag = array('<title', 'before');
        if ($this->inline) {
            foreach ($this->csscode as $media => $code) {
                $this->content = OptimizerUtils::inject_in_html($this->content, '<style type="text/css" media="' . $media . '">' . $code . '</style>', $replaceTag);
                $this->cacheStructure->addToTagsToAdd('<style type="text/css" media="' . $media . '">' . $code . '</style>', $replaceTag);
            }
        } else {
            if ($this->defer) {
                $preloadCssBlock = '';
                $noScriptCssBlock = "<noscript id=\"aonoscrcss\">";
                $defer_inline_code = $this->defer_inline;
                if (!empty($defer_inline_code)) {
                    $iCssHash = md5($defer_inline_code);
                    $iCssCache = new OptimizerCache($iCssHash, 'css', [$iCssHash]);
                    if ($iCssCache->check()) {
                        // we have the optimized inline CSS in cache.
                        $defer_inline_code = $iCssCache->retrieve();
                    } else {
                        $cssmin = new OptimizerCSSMin();
                        $tmp_code = trim($cssmin->run($defer_inline_code));
                        if (!empty($tmp_code)) {
                            $defer_inline_code = $tmp_code;
                            $iCssCache->cache($defer_inline_code, 'text/css');
                            unset($tmp_code);
                        }
                    }
                    // inlined critical css set here, but injected when full CSS is injected
                    // to avoid CSS containing SVG with <title tag receiving the full CSS link.
                    $inlined_ccss_block = '<style type="text/css" id="aoatfcss" media="all">' . $defer_inline_code . '</style>';
                }
            }
            //$this->content = OptimizerUtils::inject_in_html($this->content,$this->get_ao_css_preload_polyfill(),$replaceTag);
            foreach ($this->url as $media => $url) {
                $url = $this->url_replace_cdn($url);
                $load_none = '';
                $rel = "stylesheet";

                $css_href = "href";
                if ($this->critical->uncritical_load_type === "on_interaction" && $this->critical->critical_enabled) {
                    $css_href = self::TWO_DELAYED_CSS_ATTRIBUTE;
                }
                $two_new_tag = '<link type="text/css" media="' . $media . '" ' . $css_href . '="' . $url . '" rel="' . $rel . '" ' . $load_none . ' />';


                $pos = strpos($media, "_none");
                if ($pos) {
                    $data_media = str_replace("_none", "", $media);
                    $load_none = 'data-two_media="' . $data_media . '" onload="if(media!=\'all\')media=this.getAttribute(\'data-two_media\');"';
                    $media = "none";
                    $rel = "stylesheet";
                    $two_new_tag= "";
                    $this->two_async_css_arr[] = array(
                        'url' => $url,
                        'media' => $data_media
                    );
                }
                $this->content = OptimizerUtils::inject_in_html(
                    $this->content,
                    $two_new_tag,
                    $replaceTag
                );
                $this->cacheStructure->addToTagsToAdd(
                    $two_new_tag,
                    $replaceTag
                );

            }
        }
        $this->content = OptimizerUtils::injectCriticalBg($this->content, $this->critical, $this->cacheStructure);
        if ($this->critical->critical_enabled || $this->critical->critical_font_enabled) {
            $this->content = $this->injectCriticalCss();
        }
        // restore comments.
        $this->content = $this->restore_comments($this->content);
        // restore IE hacks.
        $this->content = $this->restore_iehacks($this->content);
        // restore (no)script.
        $this->content = $this->restore_marked_content('SCRIPT', $this->content);
        // Restore noptimize.
        $this->content = $this->restore_noptimize($this->content);

        // Return the modified stylesheet.
        return $this->content;
    }

    static function fixurls($file, $code, $asyncAllIsEnabled = false)
    {
        // Switch all imports to the url() syntax.
        $code = preg_replace('#@import ("|\')(.+?)\.css.*?("|\')#', '@import url("${2}.css")', $code);
        if (preg_match_all(self::ASSETS_REGEX, $code, $matches)) {
            $file = str_replace(WP_ROOT_DIR, '/', $file);
            $dir = dirname($file); // Like /themes/expound/css.
            /**
             * $dir should not contain backslashes, since it's used to replace
             * urls, but it can contain them when running on Windows because
             * fixurls() is sometimes called with `ABSPATH . 'index.php'`
             */
            $dir = str_replace('\\', '/', $dir);
            unset($file); // not used below at all.
            $replace = array();
            foreach ($matches[1] as $k => $url) {
                // Remove quotes.
                $old_url = $url;
                $url = trim($url, " \t\n\r\0\x0B\"'");
                $noQurl = trim($url, "\"'");
                if ($old_url !== $noQurl) {
                    $removedQuotes = true;
                } else {
                    $removedQuotes = false;
                }
                if ('' === $noQurl) {
                    continue;
                }
                $url = $noQurl;
                if (preg_match('#^(https?://|ftp://|data:)#i', $url)) {
                    // URL is protocol-relative, host-relative or something we don't touch.
                    continue;
                } else {
                    if(strpos($url, "//") ===0){
                        $url_data = wp_parse_url($url);
                        if(is_array($url_data) && isset($url_data["host"])){
                            if(strpos($url, "//".$url_data["host"]) == 0){
                                $newurl = str_replace("//".$url_data["host"] , TWO_WP_ROOT_URL, $url);
                            }else{
                                $newurl = str_replace("//" , TWO_WP_ROOT_URL, $url);
                            }
                        }else{
                            continue;
                        }
                    }elseif (strpos($url, "/") ===0){
                        $newurl = TWO_WP_ROOT_URL.$url;
                    }else{
                        $newurl = str_replace(' ', '%20', TWO_WP_ROOT_URL . str_replace('//', '/', $dir . '/' . $url));
                    }
                    /**
                     * Hash the url + whatever was behind potentially for replacement
                     * We must do this, or different css classes referencing the same bg image (but
                     * different parts of it, say, in sprites and such) loose their stuff...
                     */
                    $hash = md5($url . $matches[2][$k]);
                    $code = str_replace($matches[0][$k], $hash, $code);
                    if ($removedQuotes) {
                        $replace[$hash] = "url('" . $newurl . "')" . $matches[2][$k];
                    } else {
                        $replace[$hash] = 'url(' . $newurl . ')' . $matches[2][$k];
                    }
                }
            }
            $code = self::replace_longest_matches_first($code, $replace);
        }

        return $code;
    }

    private function ismovable($tag)
    {
        if (!$this->aggregate) {
            return false;
        }
        if (!empty($this->whitelist)) {
            foreach ($this->whitelist as $match) {
                if (false !== strpos($tag, $match)) {
                    return true;
                }
            }

            // no match with whitelist.
            return false;
        } else {
            if (is_array($this->dontmove) && !empty($this->dontmove)) {
                foreach ($this->dontmove as $match) {
                    if (false !== strpos($tag, $match)) {
                        // Matched something.
                        return false;
                    }
                }
            }

            // If we're here it's safe to move.
            return true;
        }
    }

    private function can_inject_late($cssPath, $css)
    {
        $consider_minified_array = false;
        if (true !== $this->inject_min_late) {
            // late-inject turned off.
            return false;
        } else if ((false === strpos($cssPath, 'min.css')) && (str_replace($consider_minified_array, '', $cssPath) === $cssPath)) {
            // file not minified based on filename & filter.
            return false;
        } else if (false !== strpos($css, '@import')) {
            // can't late-inject files with imports as those need to be aggregated.
            return false;
        } else if ((($this->datauris == true) || (!empty($this->cdn_url))) && preg_match('#background[^;}]*url\(#Ui', $css)) {
            // don't late-inject CSS with images if CDN is set OR if image inlining is on.
            return false;
        } else {
            // phew, all is safe, we can late-inject.
            return true;
        }
    }

    /**
     * Minifies (and cdn-replaces) a single local css file
     * and returns its (cached) url.
     *
     * @param string $filepath   Filepath.
     * @param bool   $cache_miss Optional. Force a cache miss. Default false.
     *
     * @return bool|string Url pointing to the minified css file or false.
     */
    public function minify_single($filepath, $cache_miss = false)
    {
        $contents = $this->prepare_minify_single($filepath);
        if (empty($contents)) {
            return false;
        }
        // Check cache.
        $hash = 'single_' . md5($contents);
        $cache = new OptimizerCache($hash, 'css', [md5($filepath)]);
        // If not in cache already, minify...
        if (!$cache->check() || $cache_miss) {
            // Fixurls...
            $contents = self::fixurls($filepath, $contents);
            // CDN-replace any referenced assets if needed...
            $contents = $this->replace_urls($contents);
            // Now minify...
            $cssmin = new OptimizerCSSMin();
            $contents = trim($cssmin->run($contents));
            // Store in cache.
            $contents = $this->get_replace_GoogleFonts($contents);
            $cache->cache($contents, 'text/css');
        }
        $url = $this->build_minify_single_url($cache);

        return $url;
    }

    public function css_snippetcacher($cssin, $cssfilename, $hash)
    {
        $md5hash = 'snippet_' . md5($cssin);
        $ccheck = new OptimizerCache($md5hash, 'css', [$hash]);
        if ($ccheck->check()) {
            $stylesrc = $ccheck->retrieve();
        } else {
            $cssmin = new OptimizerCSSMin();
            $tmp_code = trim($cssmin->run($cssin));

            if (!empty($tmp_code)) {
                $stylesrc = $tmp_code;
                unset($tmp_code);
            } else {
                $stylesrc = $cssin;
            }
        }
        unset($ccheck);

        return $stylesrc;
    }
    /**
     * Returns whether we're doing aggregation or not.
     *
     * @return bool
     */


    /**
     * Returns preload polyfill JS.
     *
     * @return string
     */
    public static function get_ao_css_preload_polyfill()
    {
        $preload_poly = '<script data-cfasync=\'false\'>!function(t){"use strict";t.loadCSS||(t.loadCSS=function(){});var e=loadCSS.relpreload={};if(e.support=function(){var e;try{e=t.document.createElement("link").relList.supports("preload")}catch(t){e=!1}return function(){return e}}(),e.bindMediaToggle=function(t){function e(){t.media=a}var a=t.media||"all";t.addEventListener?t.addEventListener("load",e):t.attachEvent&&t.attachEvent("onload",e),setTimeout(function(){t.rel="stylesheet",t.media="only x"}),setTimeout(e,3e3)},e.poly=function(){if(!e.support())for(var a=t.document.getElementsByTagName("link"),n=0;n<a.length;n++){var o=a[n];"preload"!==o.rel||"style"!==o.getAttribute("as")||o.getAttribute("data-loadcss")||(o.setAttribute("data-loadcss",!0),e.bindMediaToggle(o))}},!e.support()){e.poly();var a=t.setInterval(e.poly,500);t.addEventListener?t.addEventListener("load",function(){e.poly(),t.clearInterval(a)}):t.attachEvent&&t.attachEvent("onload",function(){e.poly(),t.clearInterval(a)})}"undefined"!=typeof exports?exports.loadCSS=loadCSS:t.loadCSS=loadCSS}("undefined"!=typeof global?global:this);</script>';

        return $preload_poly;
    }

    public function aggregating()
    {
        return $this->aggregate;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function replaceOptions($options)
    {
        $this->options = $options;
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
        $this->$name = $value;
    }

    public function getOption($name)
    {
        return $this->options[$name];
    }

    private function is_disable($tag, $disables_css)
    {
        foreach ($disables_css as $match) {
            if (false !== strpos($tag, $match)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Inject criticalCss that we placed in admin
     *
     * @return string
     */
    private function injectCriticalCss()
    {
        if (isset($_GET['no_critical_css']) && $_GET['no_critical_css'] == 1) {
            return $this->content;
        }
        if (isset($this->critical->critical_css) && isset($this->critical->status) && $this->critical->status =="success") {
            $file_url = TWO_CACHE_URL . "critical/" . $this->critical->critical_css;
            $file_dir = TWO_CACHE_DIR . "critical/" . $this->critical->critical_css;

            if(file_exists($file_dir)){
                $critical_content = file_get_contents($file_dir);
                $critical_content = OptimizerUtils::replace_bg($critical_content);
                if(!empty($critical_content)){
                    $critical_styles ='<style class="two_critical_css" type="text/css">'.$critical_content.'</style>';
                }
            }
            if (isset($critical_styles)) {


                $init_uncritical = false;
                if($this->use_uncritical && isset($this->critical->uncritical_css) && isset($this->critical->critical_fonts)){
                    $this->two_async_css_arr = array();
                    $this->two_async_css_arr[] = array(
                        'url' => TWO_CACHE_URL . "critical/" . $this->critical->uncritical_css,
                        'media' => "all"
                    );
                    $this->critical_fonts_arr = $this->critical->critical_fonts;
                    $init_uncritical = true;
                }

                $critical_font_css = "";
                if(isset($this->critical->critical_fonts) && is_array($this->critical->critical_fonts) && !$init_uncritical){
                    foreach ($this->critical->critical_fonts as $critical_font){
                        if(isset($critical_font->font_face)){
                            $critical_font_css.= " ".$critical_font->font_face;
                        }
                    }
                }
                $critical_font_css ='<style class="two_critical_font_css" type="text/css">'.$critical_font_css.'</style>';

                if ($this->critical->uncritical_load_type === "not_load" || $init_uncritical) {
                    if (preg_match_all('#(<style[^>]*>.*</style>)|(<link[^>]*stylesheet[^>]*>)#Usmi', $this->content, $matches)) {
                        foreach ($matches[0] as $tag) {
                            if (is_array($this->dontmove) && !empty($this->dontmove)) {
                                foreach ($this->dontmove as $ex_el) {
                                    if (false !== strpos($tag, $ex_el)) {
                                        continue 2;
                                    }
                                }
                            }
                            $this->content = str_replace($tag, '', $this->content);
                            $this->cacheStructure->addToTagsToReplace($tag, "");
                        }
                    }
                }

              if($this->TwoSettings->get_settings('two_use_font_ready') == 'on') {
                $all_fonts_are_loaded = "<script data-pagespeed-no-defer " . OptimizerScripts::TWO_NO_DELAYED_JS_ATTRIBUTE . " type='text/javascript'>
                    window['two_fonts_loaded'] = false;
                    let critical_fonts = document.querySelector('.two_critical_font_css');
                    if (critical_fonts && critical_fonts.innerText) {                 
                        document.fonts.ready.then(function () {
                            window['two_fonts_loaded'] = true;
                        });
                    } else {
                        window['two_fonts_loaded'] = true;
                    }
                    </script>";
                $critical_font_css .= $all_fonts_are_loaded;
              }


              if($this->critical->critical_enabled) {
                $this->content = OptimizerUtils::inject_in_html($this->content, $critical_styles, array('</head>', 'before'));
              }

              if($this->critical->critical_font_enabled) {
                $this->content = OptimizerUtils::inject_in_html($this->content, $critical_font_css, array('</head>', 'before'));
              }

              if($this->critical->critical_enabled) {
                $this->cacheStructure->addToTagsToAdd($critical_font_css, array('</head>', 'before'));
              }

              if($this->critical->critical_font_enabled) {
                $this->cacheStructure->addToTagsToAdd($critical_styles, array('</head>', 'before'));
              }
            }
        }

        return $this->content;
    }
}