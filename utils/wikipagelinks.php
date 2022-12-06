<?php
define("SITE_URL", "localhost/");
class WikiFilter {
    var $defaultShortcuts = array(
        'wiki' => 'http://en.wikipedia.org/wiki/%s',
    );

    function log($message) {
        if ($this->debug)
            error_log($message . "\n", 3, "/tmp/wikilinks.log");

    }

    //PHP5 constructor
    function __construct() {
        // WordPress Hooks
        //add_action('admin_menu', array(&$this, 'addAdminPanel')); 
    }

    function wiki_get_piped_title($fulltext) {
        if($last_danda_position = strrpos($fulltext, '|'))
        {
            $link = substr($fulltext, 0, $last_danda_position);
            $title = substr($fulltext, $last_danda_position+1, strlen($fulltext));
        } else {
            $link = $fulltext;
            $title = $fulltext;
        }
        $link = strtolower($link);
        $link = trim($link);
        $title = trim($title);
        return array($link, $title);
    }

    /* The filter.
	 * Replaces double brackets with links to pages.
	 */
    function bracketsToLinks($content, $db) {
        $options = $this->getAdminOptions();

        //Match only phrases in double brackets.  A backslash can be
        //used to escape the sequence, if you want literal double brackets.
        preg_match_all('/\[\[([^\]]+)\]\]/', $content, $matches);

        //$matches[1] is an array of all the phrases in double brackets.
        //Dumping all the matches into a hash ensures we only look up
        //each matching page name once.
        $links = array();
        foreach( $matches[1] as $keyword ) {
            $links[$keyword] = current($matches[0]);
            next($matches[0]);
        }

        foreach( $links as $full_link => $match ) {
            // If the "page title" contains a ':', it *may* be a shortcut
            // link rather than a page.  Deal with those first.
            //            print_r(explode(':', $full_link, 2));
            //			list($prefix, $sublink) = explode(':', $full_link, 2);
            //
            //			if ( $sublink ) {
            //				if ( array_key_exists($prefix, $options['shortcuts']) ) {
            //					list($link, $subtitle) = $this->wiki_get_piped_title($sublink);
            //					$shortcutLink = sprintf( $options['shortcuts'][$prefix],
            //						rawurlencode($link));
            //					$content = str_replace($match, 
            //						"<a href='$shortcutLink'>$subtitle</a>",
            //						$content);
            //					continue;
            //				}
            //			}

            list($link, $page_title) = $this->wiki_get_piped_title($full_link);
            //We have a page link. Check if page already exists.
            //TODO: cut down on db hits and get the list of pages instead.
            
            if ( $db->TopicExists($link) ) {
                $content = str_replace($match, 
                                       "<a style='color: rgb(119,41,83)' href='topic.php?id=".$link ."'>$page_title</a>",
                                       $content);
            } else /*if ( is_user_logged_in())*/ 
            {
                //Add a link to create the page if it doesn't exist.
                $content = str_replace($match, 
                                       "<a style='color: rgb(221,72,20)' href='topic.php?id=".$link ."'>$page_title</a>",
                                       $content);

            } /*else {

                $content = str_replace($match, $page_title, $content);
            }*/
        }

        return $content;
    }

    function getAdminOptions() {
        //defaults
        $options = array(
            'shortcuts' => $this->defaultShortcuts,
        );

        //    	$savedOptions = get_option($this->adminOptionsName);
        //    	
        //		if (!empty($savedOptions)) {
        //			foreach ($savedOptions as $key => $value) {
        //			    $options[$key] = $value;
        //			}
        //		} 

        return $options;

    }

    function saveAdminOptions($options) {
        //        if (get_option($this->adminOptionsName)) {
        //            $this->log("Updating option: " . $this->adminOptionsName);
        //            update_option($this->adminOptionsName, $options);
        //        } else {
        //            $this->log("Adding new option: " . $this->adminOptionsName);
        //            add_option($this->adminOptionsName, $options);
        //        }
    }

    function adminPanel() {  
        $adminOptions = $this->getAdminOptions();
        $submitButton = "submit_Save${shortname}Options";

        if (isset($_POST[$submitButton])) {
            for ( $i=0; $i < $this->shortcutCount; $i++ ) {
                if (isset($_POST["shortcut$i"])) {
                    $adminOptions['feeds'][$i] = $_POST["feeds$i"];
                }
            }

            print_r($adminOptions);
            $this->saveAdminOptions($adminOptions);

?>

<div class="updated"><p><strong>
    <?php  _e("Settings Updated", $this->name); ?>
    </strong></p></div>

<?php
        }

?>  
<div class="wrap">  
    <h2><?php echo $longName; ?></h2>  
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <?php wp_nonce_field('update-options'); ?>

        <table class="form-table">
            <tr valign="top">

                <th scope="row"></th>
                <td>

                    <input type="text" 
                           name="feeds<?php echo $i; ?>" 
                           value="<?php echo $feed; ?>"
                           size="50" />
                    <br />

                </td>
            </tr>

        </table>

        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options"
               value="new_option_name,some_other_option,option_etc" />

        <p class="submit">
            <input type="submit" 
                   name="<?php echo $submitButton; ?>" 
                   value="<?php _e('Save Changes'); ?>" />
        </p>


    </form>
</div>  
<?php  
    }    

    function addAdminPanel()   
    {  
        add_submenu_page('options-general.php', 
                         $this->longName, 
                         $this->shortName, 
                         10, __FILE__, 
                         array(&$this, 'adminPanel'));
    }    

}
?>
