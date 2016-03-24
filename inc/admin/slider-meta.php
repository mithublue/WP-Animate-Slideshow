<?php

class WPAS_slider_meta_box {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_slider_custom_box' ) );
        add_action( 'save_post', array( $this, 'save_postdata' ) );

        //save before save_post hook
        add_action('admin_head-post.php', array( $this, 'save_slider_data' ) );
        add_action('admin_head-post-new.php', array( $this, 'save_slider_data' ) );

        //ajax
        add_action( 'wp_ajax_wpas_save_slider_settings', array( $this, 'wpas_save_slider_settings' ) );

        //redirect to the slider when the slide is trashed
        add_action( 'wp_trash_post', array( $this, 'redirect_to_slider' ) );

        //renove some stuffs
        add_filter( 'get_sample_permalink_html', array( $this, 'remove_permalink' ) );
        add_action( 'admin_head-post-new.php', array( $this, 'remove_preview_button' ) );
        add_action( 'admin_head-post.php', array( $this, 'remove_preview_button' ) );
    }

    /**
     * Add slider meata box
     */
    function add_slider_custom_box() {
        add_meta_box(
            'wpas_slider_settings',
            __( 'Slider Settings', 'wpas' ),
            array( $this, 'render_slider_settings') ,
            'wpas_slider'
        );

        add_meta_box(
            'wpas_slider_meta',
            __( 'Slides', 'wpas' ),
            array( $this, 'render_slider_content') ,
            'wpas_slider'
        );
        add_meta_box(
            'wpas_slider_shortcode',
            __( 'Slider Shortcode', 'wpas' ),
            array( $this, 'render_slider_shortcode') ,
            'wpas_slider'
        );
    }

    /**
     * Slider settings
     */
    function render_slider_settings( $slider_post ) {
        ?>
        <div id="sliderApp">
            <table>
                <tr>
                    <td>Slider Width : </td>
                    <td><input type="number" v-model="settings.width">
                        <select v-model="settings.w_unit">
                            <option value="px">PX</option>
                            <option value="%">%</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Slider Height : </td>
                    <td><input type="number" v-model="settings.height">
                        <select v-model="settings.h_unit">
                            <option value="px">PX</option>
                            <option value="%">%</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" v-model="settings.autoplay"></td>
                    <td>Enable autoplay</td>
                </tr>
                <tr>
                    <td>Slide change interval: </td>
                    <td><input type="number" v-model="settings.intNumber" @change="senInterval"> second(s)</td>
                </tr>

            </table>
            <?php $slider_settings = json_encode(get_post_meta( $slider_post->ID, 'slider_settings', true ));// var_dump($slider_settings); ?>
        </div>
        <script>
            var slider_settings = JSON.parse('<?php echo $slider_settings; ?>'); console.log(slider_settings);
            var initial_settings = { autoplay	: true,
                interval	: 1000,
                intNumber : 1,
                width : 500 ,
                w_unit : 'px',
                height : 300,
                h_unit : 'px'
            };
            typeof  slider_settings != "object" ? ( slider_settings = initial_settings ) : '';
            var vm = new Vue({
                el : '#sliderApp',
                data : {
                    settings : slider_settings
                },
                methods : {
                    senInterval : function() {
                        vm.settings.interval = ( vm.settings.intNumber * 1000 );
                    }
                }
            })
        </script>
        <?php
    }

    /**
     * Slider shortcode
     */
    function render_slider_shortcode( $slider_post ) {
        ?>
        <input type="text" readonly value="[animate_slider id=<?php echo $slider_post->ID; ?>]"/>
    <?php
    }

    /**
     * Render slider content
     * slider posts
     */
    function render_slider_content( $slider_post ) {

        $slides = get_posts( array(
            'post_type' => 'wpas_slide',
            'post_parent' => $slider_post->ID
        ) ); ?>
        <div class="slider-metabox">
            <a class="button button-success" href="<?php echo admin_url().'post-new.php?post_type=wpas_slide&parent_id='.$slider_post->ID;?> ">New Slide</a>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                <tr>
                    <th>Slide Name</th>
                </tr>
                </thead>

                <tbody id="the-list">
                <?php if( !empty( $slides ) ) :  ?>
                    <?php foreach ( $slides as $key => $slide ) : ?>
                        <tr>
                            <td>
                                <p><?php echo $slide->post_title; ?></p>
                                <p><a href="<?php echo get_edit_post_link($slide->ID); ?>">Edit</a> | <a href="<?php echo get_delete_post_link($slide->ID) ?>">Remove</a></p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <td>No Slide Created Yet ! Create <a href="<?php echo admin_url().'post-new.php?post_type=wpas_slide&parent_id='.$slider_post->ID;?> ">New Slide</a></td>
                <?php endif; ?>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>
    <?php
    }

    /**
     * Save the data
     */
    function save_postdata() {

    }


    /**
     * Save slider data before save_post
     */
    function save_slider_data() {
        global $post;
        if('wpas_slider' === $post->post_type){
            ?>
            <script>
                jQuery(document).ready(function($){
                    //Click handler - you might have to bind this click event another way
                    $('input#publish, input#save-post').click(function(){
                        var slider_settings = vm.$data.settings;
                        $.post(
                            ajaxurl,
                            {
                                action : 'wpas_save_slider_settings',
                                slider_settings : JSON.stringify(slider_settings),
                                post_id : '<?php echo $post->ID ?>'
                            },
                            function(data) {
                            }
                        );
                    });
                });
            </script>
            <?php
        }
    }

    /**
     * Redirect to slider post
     */
    function redirect_to_slider( $post_id ) {
        $parent_id = wp_get_post_parent_id( $post_id );

        if($parent_id){
            $edit_link = get_edit_post_link( $parent_id );
            ?>
            <script>
                window.location = '<?php echo $edit_link; ?>';
            </script>
            <?php

        }
    }

    /**
     * Remove permalink
     */
    function remove_permalink( $return ) {

        if( get_post_type() == 'wpas_slider'){
            $return = '';
        }
        return $return;
    }
    /**
     * Remove preview button
     */
    function remove_preview_button() {
        global $post_type;
        $post_types = array(
            'wpas_slider',
            'wpas_slide'
        );
        if(in_array($post_type, $post_types)) {
            echo '<style type="text/css">#post-preview, #view-post-btn{display: none;}</style>';
        }

    }


    /**
     * Run ajax to save slider settings
     */
    function wpas_save_slider_settings() {
        if ( !is_numeric($_POST['post_id']) || get_post_type( $_POST['post_id'] ) != 'wpas_slider' ) {
            return;
        }
        update_post_meta( $_POST['post_id'], 'slider_settings', (array)json_decode( stripslashes( $_POST['slider_settings'] ) ) );
        exit;
    }

    public static function init() {
        new WPAS_slider_meta_box();
    }
}

WPAS_slider_meta_box::init();