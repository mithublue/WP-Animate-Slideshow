<?php

class WPAS_Slide_Meta_Box{

    function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script_style' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_slide_custom_box' ) );
        add_action( 'save_post', array( $this, 'save_postdata' ) );
        add_filter( 'wp_insert_post_data', array( $this, 'filter_handler' ), '99', 2 );
        // UPLOAD ENGINE
        function load_wp_media_files() {
            wp_enqueue_media();
        }
        add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );
        //save before save_post hook
        add_action('admin_head-post.php', array( $this, 'save_layer_data' ) );
        add_action('admin_head-post-new.php', array( $this, 'save_layer_data' ) );
        add_action( 'wp_ajax_wpas_save_slide_meta', array( $this, 'wpas_save_slide_meta' ) );

        //add slide list button
        add_action( 'edit_form_top', array( $this, 'add_slide_list_form_button' ) );

        //remove stuffs
        add_action('admin_head',array( $this, 'disable_new_posts_btn' ) );
    }

    /**
     * Add meta box
     */
    function add_slide_custom_box() {
        add_meta_box(
            'wpas_slide_meta',
            __( 'Slides', 'wpas' ),
            array( $this, 'render_slide_content') ,
            'wpas_slide'
        );
    }


    /**
     * Slide post type meta content
     */
    function render_slide_content( $slide_post ){

        $parent_id = wp_get_post_parent_id( $slide_post->ID );
        $slide_meta = get_post_meta( $slide_post->ID, 'slide_meta' , true );
        include_once 'templates/template-slide.php'; ?>
        <input name="post_parent_id" type="hidden" value="<?php echo $parent_id? $parent_id : ( isset( $_GET['parent_id'] ) && is_numeric( $_GET['parent_id'] ) ? $_GET['parent_id'] : 0 ) ;?>"/>
        <div id="slideApp">
            <div class="slide-preview-title">Slide Preview</div>
            <div id="slide-preview">
                <div id="wp-animate-slider-<?php echo $slide_post->ID; ?>" style="height: 100%">
                    <ul class="anim-slider">
                        <li v-for="( key, item ) in slide_meta.layers">
                            <img :id="key" :src="item.imgurl" :width="item.width[0]" :height="item.height[0]" alt="" v-if="item.type == 'image'"
                                 :style="{
                                    top : item.final_pos.top.val + item.final_pos.top.unit,
                                    left : item.final_pos.left.val + item.final_pos.left.unit
                                }"
                                >
                            <p :id="key" v-if="item.type == 'text'"
                                :style="{
                                    fontSize : item.settings['font-size'] + 'px',
                                    color : item.settings['color'],
                                    fontWeight : item.settings['font-weight'],
                                    fontStyle : item.settings['font-style'],
                                    backgroundColor : item.settings['background-color'],
                                    top : item.final_pos.top.val + item.final_pos.top.unit,
                                    left : item.final_pos.left.val + item.final_pos.left.unit
                                }">{{ item.content }}</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="add_layer_panel">
                <select name="laye_type" id="layer_type" v-model="selected_layer_type">
                    <option value="Select layer type" selected>Select layer type</option>
                    <option value="image">Image</option>
                    <option value="text">Text</option>
                </select>
                <button class="add_layer button-secondary" @click="addNewLayer" >Add New Layer</button>
            </div>
            <div v-for="( key, item ) in slide_meta.layers">
                <meta_comp :item.sync="item" :key.sync="key"></meta_comp>
            </div>
        </div>
        <script>
            var slide_meta = JSON.parse('<?php echo json_encode( $slide_meta )?>');
            var tmp_layer_settings = {
                layers : {}
            }
            typeof  slide_meta != "object" ? ( slide_meta = { layers : {}} ) : '';
            var vm = new Vue({

                el : '#slideApp',

                data : {
                    slide_meta : slide_meta,

                    selected_layer_type : 'Select layer type',

                    image_layer_array :  {
                        type : 'image' ,
                        imgurl : "",
                        show: "",
                        hide: "",
                        delayShow: "delay1s",
                        width : [],
                        height : [],
                        delayTime : 1,
                        final_pos : {
                            top : {
                                val : 0,
                                unit : 'px'
                            },
                            left : {
                                val : 0,
                                unit : 'px'
                            }
                        }
                    },

                    text_layer_array : {
                        type : 'text',
                        content : 'Some content',
                        settings : {
                            'font-size' : '10px',
                            'color' : '#000000',
                            'font-weight' : '',
                            'font-style' : 'normal',
                            'background-color' : ''
                        },
                        show : 'fadeIn',
                        hide : 'fadeOut',
                        delayShow : 'delay1s',
                        delayTime : 1,
                        final_pos : {
                            top : {
                                val : 0,
                                unit : 'px'
                            },
                            left : {
                                val : 0,
                                unit : 'px'
                            }
                        }
                    }
                },
                components : {
                    meta_comp : {
                        template : '#layer-template',
                        props : [ 'item', 'key' ],
                        data : function () {
                            return {
                                animations : [
                                    'fadeIn', 'fadeOut' , 'bounceIn' , 'bounceOut', 'bounceInDown' , 'bounceOutLeft' , 'bounceInRight' ,
                                    'bounceOutRight' , 'fadeInLeft' , 'fadeOutLeft' , 'fadeInUpBig', 'fadeOutUpBig', 'fadeInDownBig' , 'fadeOutDownBig' ,
                                    'rotateIn' , 'rotateOut' , 'rotateInUpRight' , 'rotateOutDownRight', 'rotateInUpLeft' , 'rotateOutDownLeft'
                                ],
                                font_weight_opt : [100,200,300,400,500,600,700,800,900,'bold','bolder','normal'],
                                font_style_opt : ['italic','normal','oblique','initial']
                            }
                        },
                        methods : {
                            setDelay : function(item){
                                item.delayShow = 'delay' + item.delayTime + 's';
                            },
                            removeLayer : function(key, item) {
                                var temp_slide_meta = {};
                                for( k  in vm.slide_meta.layers ) {
                                    if( k != key ) {
                                        temp_slide_meta[k] = vm.slide_meta.layers[k];
                                    }
                                }
                                vm.slide_meta.layers = temp_slide_meta;
                            },
                            mediaPopup : function(key,item){
                                var clicked_btn = jQuery(this);
                                var image = wp.media({
                                    title: 'Upload Image',
                                    // mutiple: true if you want to upload multiple files at once
                                    multiple: false
                                }).open()
                                    .on('select', function(e){
                                        var uploaded_image = image.state().get('selection').first();
                                        // We convert uploaded_image to a JSON object to make accessing it easier
                                        // Output to the console uploaded_image
                                        var image_url = uploaded_image.toJSON().url;
                                        // Let's assign the url value to the input field
                                        //$('.image_url').val(image_url);
                                        item.imgurl = image_url;
                                        clicked_btn.siblings('.preview').html('<img src="'+ image_url +'" width="100" />')
                                    });
                                return false;
                            }
                        }
                    }
                },
                methods : {
                    addNewLayer : function(e) {
                        e.preventDefault();
                        var layer_type = this.selected_layer_type;
                        var new_layer_id = 'layer_' + new Date().getTime();
                        vm.$set( 'slide_meta.layers.' + new_layer_id ,
                           this[layer_type + '_layer_array']
                        );
                        make_draggable(jQuery);
                    }
                }
            })

            jQuery(document).ready(function($){
                make_draggable(jQuery);
            });
            var make_draggable = function($) {
                var init_left = '';
                var init_left_val = '';
                var init_top = ''
                var init_top_val = '';

                $('#slide-preview li *').draggable({
                    containment : '#slide-preview',
                    start : function( event, ui ){
                        elem_id = $(this).attr('id');  console.log(elem_id);
                        init_left = parseInt($(this).css('left'));
                        init_left_val = vm.$data.slide_meta.layers[elem_id].final_pos.left.val;
                        init_top = parseInt($(this).css('left'));
                        init_top_val = vm.$data.slide_meta.layers[elem_id].final_pos.top.val;
                        console.log(vm.$data.slide_meta.layers[elem_id]);
                    },
                    stop: function(event,ui){
                        if( vm.$data.slide_meta.layers[elem_id].final_pos.left.unit == 'px' ) {
                            vm.$data.slide_meta.layers[elem_id].final_pos.left.val = parseInt($(this).css('left'));
                        }else{
                            var percent_val_left = ( init_left_val * parseInt($(this).css('left')) ) / init_left;
                            vm.$data.slide_meta.layers[elem_id].final_pos.left.val = percent_val_left;


                        }

                        if( vm.$data.slide_meta.layers[elem_id].final_pos.top.unit == 'px' ) {
                            vm.$data.slide_meta.layers[elem_id].final_pos.top.val = parseInt($(this).css('top'));
                        }else{
                            var percent_val_top = ( init_top_val * parseInt($(this).css('top')) ) / init_top;
                            vm.$data.slide_meta.layers[elem_id].final_pos.top.val = percent_val_top;
                        }

                    }
                });
            }
        </script>
        <?php
    }

    /**
     *
     * Save layer data
     */
    function save_layer_data() {
        global $post;
        if('wpas_slide' === $post->post_type){
            ?>
            <script>
                jQuery(document).ready(function($){
                    //Click handler - you might have to bind this click event another way
                    $('input#publish, input#save-post').click(function(){
                        var slide_meta = vm.$data.slide_meta;
                        $.post(
                            ajaxurl,
                            {
                                action : 'wpas_save_slide_meta',
                                slide_meta : JSON.stringify(slide_meta),
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
     * ajax save action to save slide meta
     */
    function wpas_save_slide_meta() {
        if ( !is_numeric($_POST['post_id']) || get_post_type( $_POST['post_id'] ) != 'wpas_slide' ) {
            return;
        }
        update_post_meta( $_POST['post_id'], 'slide_meta', (array)json_decode( stripslashes( $_POST['slide_meta'] ) ) );
        exit;
    }

    /**
     * insert post parent id before saving the post
     */
    function filter_handler( $data , $postarr ){
        if( isset($_POST['post_parent_id']) && is_numeric( $_POST['post_parent_id'] ) && get_post_type( $_POST['post_parent_id'] ) == 'wpas_slider' ) {
            $data['post_parent'] = $_POST['post_parent_id'];
        }
        return $data;
    }

    /**
     * Save postdata
     */
    function save_postdata( $post_id ){
    }

    /**
     * Add 'Back to Slide List' button to
     * the top of the form
     */
    function add_slide_list_form_button( $post ) {
        if( 'wpas_slide' == $post->post_type ) {
            $parent_id = wp_get_post_parent_id( $post->ID );
            $parent_id = $parent_id ? $parent_id : ( isset( $_GET['parent_id'] ) && is_numeric( $_GET['parent_id'] ) && get_post_type( $_GET['parent_id'] ) == 'wpas_slider' ? $_GET['parent_id'] : 0 );
            echo "<a class='page-title-action' href='".get_edit_post_link( $parent_id )."' id='wpas-custom-header-link'>Back to Slide List</a>";
        }

    }

    /**
     * Remove add new button
     */
    function disable_new_posts_btn() {
        global $pagenow;
        if(is_admin()){
            if( get_post_type() == 'wpas_slide'){
                echo '<style>h1 > a.page-title-action{display: none;}</style>';
            }
        }
    }

    /**
     * Enqueue script and style
     */
    function enqueue_script_style( $hook ) {
        global $post;
        if( $hook == 'post.php' && get_post_type($post->ID) == 'wpas_slide' ) {
            wp_enqueue_style( 'wpas-animate-css', plugins_url( '../../assets/css/jquery.animateSlider.css', __FILE__) );
            wp_enqueue_style( 'wpas-normalize-css', plugins_url( '../../assets/css/normalize.css', __FILE__) );
            wp_enqueue_style( 'wpas-slidermain-css', plugins_url( '../../assets/css/slider-main.css', __FILE__) );

            wp_enqueue_script( 'wpas-modernizr', plugins_url( '../../assets/js/modernizr.js', __FILE__), array( 'jquery') );
            wp_enqueue_script( 'wpas-js', plugins_url( '../../assets/js/jquery.animateSlider.js', __FILE__), array( 'jquery', 'wpas-modernizr' ) );
        }
    }

    public static function init() {
        new WPAS_Slide_Meta_Box();
    }
}

WPAS_Slide_Meta_Box::init();