<?php
$product_id = $id = get_the_ID();
$user_id = get_current_user_id();
$meta = get_user_meta( $user_id, 'liked_product', true);
$status = 'no-like';
if( is_array( $meta ) && in_array( $product_id, $meta ) ){
    $status = 'liked';
}
?>
<div class="wpt-favorite-button favorite-button-<?php echo esc_attr($id); ?> favorite-button-<?php echo esc_attr( $status ); ?>" 
       name="favorite" value="Favorite" 
       data-product_id="<?php echo esc_attr( $id ); ?>" 
       data-user_id="<?php echo esc_attr(get_current_user_id()); ?>" 
       data-quantity="5" 
       data-status="<?php echo esc_attr( $status ); ?>">
    <span class="icon-favorite"></span>
</div>
<div class="message-favorite-button message-favorite-button-<?php echo esc_attr($id); ?>"></div>
