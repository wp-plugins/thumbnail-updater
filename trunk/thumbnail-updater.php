<?php
/**
 * Thumbnail Updater
 *
 * @category Plugins
 * @package Thumbnail_Updater
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/*
 Plugin Name: Thumbnail Updater
 Plugin URI: http://www.dumpster-fairy.com/
 Description: Updates WordPress Media library thumbnails whenever a new image size is added via add_image_size().
 Version: 1.0
 Author: Jessica Green
 Author URI: http://www.dumpster-fairy.com
*/
/*
 * (c) 2010 Jessica C Green
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

global $_wp_additional_image_sizes, $thumbnail_init;

add_action('init', create_function('$thumbnail_init', '$thumbnail_init = new Update_Thumbnail_Interface();'));
/**
 * @package Thumbnail_Updater
 * @subpackage Update_Thumbnail_Interface
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class Update_Thumbnail_Interface {
    /**
     * Name of plugin. Used to provide multilanguage support
     * in future versions.
     *
     * @var string
     */
    public $locale = 'thumbnail-updater';
    /**
     * Name of Ajax action to be passed to admin-ajax.php. Also used to create
     * the Ajax hook for this action. This action updates the thumbnail.
     *
     * @var string
     */
    private $ajax_thumbnail = 'update-thumbnail';
    /**
     * Ajax action. This action displays the Update Media Thumbnail interface.
     * 
     * @var string
     */
    private $image_size = 'display-thumbnails';
    /**
     * PHP5 magic function. Initializes the plugin.
     *
     * @return void
     */
    public function  __construct() {
        $this->dir = WP_CONTENT_DIR."/plugins/".plugin_basename( dirname(__FILE__) );
        $this->url = WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__) );
        
        $this->ajax_update_thumbnail = admin_url() . 'admin-ajax.php?action=' . $this->ajax_thumbnail;
        $this->ajax_display_thumbnails = admin_url() . 'admin-ajax.php?action=' . $this->image_size;
        
        /* Media Hooks. */
        add_filter( 'media_meta', array( &$this, 'thumbnail_button' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array(&$this,'media_enqueue_scripts'));
        add_action( 'wp_ajax_' . $this->ajax_thumbnail, array( &$this, 'update_thumbnail_ajax' ), 10 );
        add_action( 'wp_ajax_' . $this->image_size, array( &$this, 'image_sizes' ), 10 );
    }
    /**
     * Adds the Update Image Sizes button to the Media meta section.
     *
     * @param string $image_meta Original content of the meta section.
     * @param mixed $post WordPress post object.
     * @return string
     */
    public function thumbnail_button( $image_meta, $post ) {

        $id = (int) $post->ID;

        if ($this->is_image($post->post_mime_type)) {
            $text = __( 'Update Image Sizes', $this->locale );

            $ajax_img = '<img alt="" class="' . $this->locale . '-spin imgedit-wait-spin"' .
                        ' src="'.admin_url().'images/wpspin_light.gif">';

            $button = $image_meta . '<p style="margin:10px 0 0">' .
                            '<a class="button ' . $this->locale .
                            '" id="'. $this->locale . '-' . $id . '"' .
                            ' href="javascript:void(0);" onclick="imageSizes.open(\'' . $id .  '\', \'' .
                            wp_create_nonce($this->locale) . '\' )">' .
                            $text . '</a>'.$ajax_img.'</p>';

            return $button;
        } else {
            return $image_meta;
        }
    }
    /**
     * Checks post_mime_type to see if it is an image.
     *
     * @param string $mime_type post_mime_type being checked.
     * @return boolean
     */
    public function is_image($mime_type) {

        $type = strtolower(substr($mime_type, 0, 5));

        if ($type == 'image')
            return true;
        else
            return false;

    }
    /**
     * Function for the update-thumbnail Ajax action.
     *
     * @return void
     */
    public function update_thumbnail_ajax(){

        require_once( $this->dir . '/ajax-thumbnail.php' );

    }
    /**
     * Enqueues needed scripts for the Media Library section.
     *
     * @global string $hook_suffix Current hook being called.
     * @return void
     */
    public function media_enqueue_scripts() {
        global $hook_suffix;

        if ($hook_suffix == 'media.php')
            wp_enqueue_script('imgsize', $this->url.'/js/image-size.js');
    }
    /**
     * Displays the Update Media Thumbnail interface for the display-thumbnails action.
     *
     * @global array $_wp_additional_image_sizes Array of available image sizes.
     * @return void
     */
    public function image_sizes() {
        global $_wp_additional_image_sizes;

        $ajax_img = '<img alt="" class="' . $this->locale . '-spin imgedit-wait-spin"' .
                    ' src="'.admin_url().'images/wpspin_light.gif">';


        $intermediate_sizes = get_intermediate_image_sizes();
        $post_id = intval($_POST['postid']);
        $nonce = $_POST['_ajax_nonce'];
    ?>
<div class="imgedit-wrap" style="width: 50%;">
    <h2>Update Media Thumbnail</h2>
    <div id="thumb-update-<?php echo $post_id; ?>" class="below-h2"></div>
    <table class="widefat fixed">
        <thead>
            <tr>
                <th style="font-weight: bold;" class="manage-column" scope="col">Thumbnail Exists?</th>
                <th style="font-weight: bold;" class="manage-column" scope="col">Name</th>
                <th style="font-weight: bold;" class="manage-column" scope="col">Size Name</th>
                <th style="font-weight: bold;" class="manage-column" scope="col">Dimensions</th>
            </tr>
        </thead>
        <tbody>
<?php
        foreach( $_wp_additional_image_sizes as $key => $value) :
            $size = image_get_intermediate_size( $post_id, $key);

            $size_exists[$key] = (bool)$size;
?>
            <tr>
                <th style="padding: 10px; text-align: center; vertical-align: top;" scope="row">

                    <img src="<?php echo $this->url ?>/images/<?php echo (bool)$size ? 'thumb-exists.png' : 'no-thumb.png'; ?>"
                         title="<?php echo (bool)$size?'Yes':'No'; ?>"
                         alt="<?php echo (bool)$size?'Yes':'No'; ?>" height="16" width="16" />
                </th>
                <td style="padding: 10px; vertical-align: top;">
                        <?php echo ucwords(str_replace('-', ' ', $key)); ?>
                </td>
                <td style="padding: 10px; vertical-align: top;">
                        <?php echo $key; ?>
                </td>
                <td style="padding: 10px; vertical-align: top;">
                        <?php echo $value['width'] ?> &times; <?php echo $value['height'] ?>
                </td>
            </tr>
            
<?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th style="font-weight: bold;" class="manage-column" scope="col">Thumbnail Exists?</th>
                <th style="font-weight: bold;" class="manage-column" scope="col">Name</th>
                <th style="font-weight: bold;" class="manage-column" scope="col">Size Name</th>
                <th style="font-weight: bold;" class="manage-column" scope="col">Dimensions</th>
            </tr>
        </tfoot>

    </table>
    <p class="imgedit-submit">
        <input type="button" id="update-thumbnail-<?php echo $post_id; ?>"
               class="button-primary imgedit-submit-btn" value="Update"
               onclick="imageSizes.update(<?php echo $post_id; ?>, '<?php echo $nonce ?>')"
               <?php disabled(!in_array(false,$size_exists)) ?> />
        
        <?php echo $ajax_img ?>
        <input type="button" class="button <?php echo $this->locale ?>" value="Close" onclick="imageSizes.close()" />
    </p>

</div>

    <?
    exit(); // needed to keep from displaying a 0 at the end of the call.
    }

}
?>