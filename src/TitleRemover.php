<?php
/**
 * Removes titles from posts.
 *
 * @package     wp-4-me-title-remover
 * @category    Core
 * @author      Daryl Peterson (@gmail)
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace WP4Me_Title_Remover;

if (!defined('ABSPATH') | (!defined(__NAMESPACE__ . '\PLUGIN_INIT'))) {
    exit;
}

/**
 * Title Remover Object
 */
class TitleRemover
{

    protected $mbID;
    protected $metaKey;
    protected $inputName;
    protected $nonce;

    /**
     * Object initialization
     */
    public function __construct()
    {
        $this->mbID = getKeyName('hide-title', '-');
        $this->metaKey = getKeyName('hide_title');
        $this->nonce = getKeyName('meta_nonce');
        $this->inputName = getKeyName('hide-title-checkbox', '-');
        
    }

    /**
     * Register primary hooks
     *
     * @return void
     */
    public static function init()
    {
        $obj = new TitleRemover();
        add_filter('the_title', [$obj, 'remove'], 10, 2);

        if (is_admin()) {
            add_action('load-post.php', [$obj, 'setupPostMeta']);
            add_action('load-post-new.php', [$obj, 'setupPostMeta']);
        }
    }


    /**
     * Filter the title and return empty string if necessary.
     *
     * @param $title string The old title
     * @param int $post_id The post ID
     *
     * @return string title or empty string.
     */
    public function remove($title, $post_id = 0)
    {
        if (!$post_id) {
            return $title;
        }

        $hide_title = get_post_meta($post_id, $this->metaKey, true);
        if (!is_admin() && is_singular() && intval($hide_title) && in_the_loop()) {
            return '';
        }

        return $title;
    }

    /**
     * Register hooks to add meta boxes
     * 
     * @return void
     */
    public function setupPostMeta()
    {
        /* Add meta boxes on the 'add_meta_boxes' hook. */
        add_action('add_meta_boxes', [$this, 'addPostMeta']);

        /* Save post meta on the 'save_post' hook. */
        add_action('save_post', [$this, 'savePostMeta'], 10, 2);
    }

    /**
     * Add post meta boxes
     *
     * @return void
     */
    public function addPostMeta()
    {
        add_meta_box(
            $this->mbID,
            esc_html__('Title', PLUGIN_DOMAIN),
            [$this, 'render'],
            null,
            'side',
            'core'
        );
    }

    /**
     * Save post meta
     *
     * @param int $post_id
     * @param \WP_Post $post
     * @return void
     */
    public function savePostMeta($post_id, $post)
    {

        /* Verify the nonce before proceeding. */
        if (!isset($_POST[$this->nonce]) || !wp_verify_nonce($_POST[$this->nonce], basename(__FILE__))) {
            return;
        }

        /* Get the post type object. */
        $post_type = get_post_type_object($post->post_type);

        /* Check if the current user has permission to edit the post. */
        if (!current_user_can($post_type->cap->edit_post, $post_id)) {
            return;
        }

        /* Get the posted data and sanitize it for use as an HTML class. */
        $form_data = (isset($_POST[$this->inputName]) ? $_POST[$this->inputName] : '0');
        update_post_meta($post_id, $this->metaKey, $form_data);
    }

    /**
     * Render post meta boxes
     *
     * @param [type] $post
     * @return void
     */
    public function render($post)
    {

        wp_nonce_field(basename(__FILE__), $this->nonce);

        $curr_value = get_post_meta($post->ID, $this->metaKey, true);
        $checked = ' ' . checked($curr_value, '1', false);

        $trans = esc_html__('Hide the title for this item', 'title-remover');

        $html = <<<EOD
        <input type="hidden" name="$this->inputName" value="0" />
        <input type="checkbox" name="$this->inputName" id="$this->inputName" value="1"$checked />
        <label for="$this->inputName">$trans</label>
EOD;
        echo $html;
    }
}
