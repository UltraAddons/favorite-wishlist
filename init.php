<?php
/**
 * Plugin Name: Favorite Plugin
 * Plugin URI: https://ultraaddons.com
 * Plugin Descriptions: This is a test plugin
 */


define('FAVORITE__FILE__', __FILE__);
define('FAVORITE_VERSION', '1.0.0');
define('FAVORITE_PATH', plugin_dir_path(FAVORITE__FILE__));
define('FAVORITE_URL', plugins_url(DIRECTORY_SEPARATOR, FAVORITE__FILE__));

if( !function_exists( 'favorite_admin_menu' ) ){
    function favorite_admin_menu(){
//        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug);
        add_submenu_page('edit.php?post_type=wpt_product_table', 'My Favorite Products', 'My Favorite Products', 'manage_woocommerce', 'wpt-favorite-products', 'wpt_favorite_products_callback' );
    }
}
add_action( 'admin_menu', 'favorite_admin_menu' );

if( !function_exists( 'wpt_favorite_products_callback' ) ){
    function wpt_favorite_products_callback(){
        $value = filter_input(INPUT_POST, 'favorite_products_table_id');
        if($value){
            update_option('favorite_table_id', $value);
        }
        $c_value = get_option('favorite_table_id');
        ?>
<style type="text/css">
    .fav-container {
        background: #fff;
        padding: 20px;
        border: 1px solid #ddd;
    }
    .fav-container h3 {
        margin: 0 0 20px;
        font-size: 2em;
    }
    .fav-contents table th {
        min-width: 200px;
        text-align: left;
        vertical-align: top;
    }
</style>
<div class="wrap">
    <div class="fav-container">
        <div class="fav-header">
            <h3>Favorite Products Settings</h3>
        </div>
        <div class="fav-contents">
            <form action="" method="POST">
                <table>
                    <tr>
                        <th><label for="favorite_products_table_id">Table ID</label></th>
                        <td>
                            <input type="number" name="favorite_products_table_id" id="favorite_products_table_id" value="<?php echo $c_value; ?>" placeholder="e.g. 110">
                            <p class="hint">Enter the table ID which you have created for favorite product list.</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="SAVE SETTINGS" class="button primary"/></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php
    }
}

if( !function_exists( 'add_favorite_column' )){
    function add_favorite_column( $column_array ){

        $column_array['favorite'] = "Favorite";
        return $column_array;
    }
}

add_filter( 'wpto_default_column_arr', 'add_favorite_column' );

function favorite_column_settings($column_settings){
    ?>
    <input type="submit" value='Favorite' onclick="return false;">
<?php 
}
//add_action( 'wpto_column_setting_form_favorite', 'favorite_column_settings' );

if( !function_exists( 'favorite_column_template' ) ){
    function favorite_column_template( $file ){
        $file = __DIR__ . '/include/template.php';
        return $file;
    }
}
add_filter( 'wpto_template_loc_item_favorite', 'favorite_column_template', 10 );

if( !function_exists( 'load_favorite_script' ) ){
    function load_favorite_script(){
        wp_register_style('favorite-style', FAVORITE_URL. 'assets/style.css');
        wp_enqueue_style('favorite-style');
        wp_register_script('favorite-script',FAVORITE_URL . 'assets/script.js', array('jquery'), null, true);
        wp_localize_script( 'favorite-script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
        wp_enqueue_script('favorite-script');
    }
}
add_action('wp_enqueue_scripts', 'load_favorite_script');

if( !function_exists( 'favorite_user_meta_update' ) ){
    function favorite_user_meta_update(){
        $user_id = get_current_user_id();
        if(!$user_id){
            echo ' <a class="favorite_login_button" href="' . wp_login_url() . '"> Log in</a>';
            die();
            //return;
        }
        $product_id = $_POST['product_id'];
        $status = $_POST['status'];

        if( empty( $status ) ){
            return false;
        }

        $current_liked = get_user_meta($user_id, 'liked_product',true);
        if(!is_array($current_liked)){
            $current_liked = array();
        }

        if( $status == 'liked' ){
            unset($current_liked[$product_id]);
        }else{
            $current_liked[$product_id] = $product_id;
        }

        update_user_meta($user_id,'liked_product', $current_liked);
        echo 'success';
        die();
    }
}
add_action( 'wp_ajax_favorite_user_meta_update', 'favorite_user_meta_update' );
add_action( 'wp_ajax_nopriv_favorite_user_meta_update', 'favorite_user_meta_update' );

//update_option( 'favorite_table_id', 358 );

if( !function_exists( 'qrr_arg' ) ){
    function qrr_arg($args, $table_ID){
        $selected_table_id = get_option( 'favorite_table_id' );
        if( $table_ID == $selected_table_id ){
            $user_id = get_current_user_id();
            $liked_product_list = get_user_meta($user_id, 'liked_product',true);
            //var_dump($liked_product_list);
            $liked_product_list = !empty( $liked_product_list ) ? $liked_product_list : array(0);
            $args['post__in'] = $liked_product_list;
        }
        return $args;
    }
}
add_filter('wpto_table_query_args','qrr_arg', 10, 2);

//wpto_action_start_table Filter: wpto_table_show

if( !function_exists( 'fav_table_show_hide' ) ){
    function fav_table_show_hide( $bool, $table_ID){
       $user_id = get_current_user_id();
        if(!$user_id && $table_ID == get_option( 'favorite_table_id' )){
            return false;
            //return;
        }
        return true;
    }
}
add_filter('wpto_table_show','fav_table_show_hide', 10, 2);


$WPT_Module =  WP_PLUGIN_DIR . 'woo-product-table/woo-product-table.php';// Our Main Plugin'woo-product-table/woo-product-table.php';
if( file_exists( $WPT_Module ) ){
   include_once $WPT_Module;
}
if( !function_exists( 'fav_table_content_change' ) ){
    function fav_table_content_change( $table_ID){
       $user_id = get_current_user_id();
        if(!$user_id && $table_ID == get_option( 'favorite_table_id' )){
            echo ' <a class="button favorite_login_button" href="' . wp_login_url() . '"> Log in</a>';
        }
        
    }
}
add_action('wpto_action_start_table','fav_table_content_change', 99, 2);


