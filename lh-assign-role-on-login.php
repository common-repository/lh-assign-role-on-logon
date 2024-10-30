<?php
/*
Plugin Name: LH assign role on login
Version: 1.04
Plugin URI: https://lhero.org/portfolio/lh-assign-role-on-logon/
Description: Assigns a role automatically to a user when they log in to a child of a multisite installation
Author: Peter Shaw
Network: true
Author URI: https://shawfactor.com
*/



class LH_assign_role_on_login_plugin {

var $default_role_name = "lh-assign_role_on_login-default_role";
var $namespace = 'lh_assign_role_on_login';
var $plugin_website = 'https://lhero.org/portfolio/lh-assign-role-on-logon/';

private function force_last_activity_meta(){

global $wpdb;
	
	if( !is_super_admin() ){
		return;
	

	} else {	$sql = "SELECT ID FROM {$wpdb->users}";

		$fua_user_ids = $wpdb->get_col($sql);

//print_r($fua_user_ids);
	
		foreach($fua_user_ids as  $fua_user_id){

echo $fua_user_id;

$update=get_usermeta( $fua_user_id, 'bp_latest_update' );

echo $update;
	

	
			if( $update == "" ) {
				bp_update_user_last_activity($fua_user_id,  date('Y-m-d H:i:s') );
				// Lets try and get a different time stamp for each entry

			} 
		} // close foreach
	} // close action

}


/**
 * Based on the deprecated WPMU get_blog_list function. 
 * 
 * Except this function gets all blogs, even if they are marked as mature and private.
 */
private function get_blog_list( $start = 0, $num = 10 ) {
	global $wpdb;

	$blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND archived = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC", $wpdb->siteid ), ARRAY_A );

	foreach ( (array) $blogs as $details ) {
		$blog_list[ $details[ 'blog_id' ] ] = $details;
		$blog_list[ $details[ 'blog_id' ] ]['postcount'] = $wpdb->get_var( "SELECT COUNT(ID) FROM " . $wpdb->get_blog_prefix( $details['blog_id'] ). "posts WHERE post_status='publish' AND post_type='post'" );
	}
	unset( $blogs );
	$blogs = $blog_list;

	if ( false == is_array( $blogs ) )
		return array();

	if ( $num == 'all' )
		return array_slice( $blogs, $start, count( $blogs ) );
	else
		return array_slice( $blogs, $start, $num );
}



private function role_exists( $role ) {

  if( ! empty( $role ) ) {
    return $GLOBALS['wp_roles']->is_role( $role );
  }
  
  return false;
}


private function check_if_user_has_logged_in_before($id){

global $wpdb;

$blog_id = get_current_blog_id();

$meta_key = $wpdb->prefix."has_logged_in";

$metaval = get_user_meta($id, $meta_key, true);


if ($metaval){

return true;
 
} else {


update_user_meta( $id, $meta_key, "1");

return false;

}


}

public function wp_login($user_login, $user) {

if (!$this->check_if_user_has_logged_in_before($user->ID)){


$roles = array_filter($user->roles);


if ($this->role_exists( $this->default_role )) {

if (empty($roles)) {

wp_update_user(array(
    'ID' => $user->ID,
    'role' => $this->default_role
));


//upgrades the user if they have been created as a subscriber but the default is another role (does nothing if the default is no role

} elseif (($user->roles[0] == 'subscriber') and !$user->roles[1] and ($this->default_role != 'none')) {

wp_update_user(array(
    'ID' => $user->ID,
    'role' => $this->default_role
));

}



}

}

}



/**
 * Outputs the role selection boxes on the 'Network Admin | Settings' page. 
 */

public function wpmu_options(){

	$blogs = $this->get_blog_list( 0, 'all' );
	echo '<h3>' . __( 'User Management', $this->namespace). '</h3>';

	if( empty( $blogs ) ) {
		echo '<p><b>' . __( 'No sites available.', $this->namespace ) . '</b></p>';
	} else {
		echo '<p>' . __( 'Select the default role for each of your sites.', $this->namespace ) . '</p>';
		echo '<p>' . __( 'New users without assigned roles will receive these roles when activating their account.', 'lh_assign_role_on_login' ) . '</p>';
		echo '<table class="form-table">';
		foreach( $blogs as $key => $blog ) { 

			switch_to_blog( $blog[ 'blog_id' ] );
			?>
			<tr valign="top">
				<th scope="row"><?php echo get_bloginfo( 'name' ); ?></th>
				<td>
					<select name="lharol_default_user_role[<?php echo $blog[ 'blog_id' ]; ?>]" id="lharol_default_user_role[<?php echo $blog[ 'blog_id' ]; ?>]">
						<option value="none"><?php _e( '-- None --', 'lh_assign_role_on_login' )?></option>
						<?php wp_dropdown_roles( get_option( $this->default_role_name ) ); ?>
					</select>
				</td> 
			</tr>
		<?php restore_current_blog();
		}
		echo '</table>';
	}
		echo '<p>' . __( '<b>Note:</b> only public, non-mature and non-dashboard sites appear here. Set the default role for the dashboard site above under <b>Dashboard Settings</b>.', $this->namespace ) . '</p>';


$this->force_last_activity_meta();


}




/**
 * Update Default Roles on submission of the multisite options page.
 */
public function update_wpmu_options(){

	if( !isset( $_POST[ 'lharol_default_user_role' ] ) || !is_array( $_POST[ 'lharol_default_user_role' ] ) )
		return;

	foreach( $_POST[ 'lharol_default_user_role' ] as $blog_id => $new_role ) { 
		switch_to_blog( $blog_id );
		$old_role = get_option( $this->default_role_name, 'none' ); // default to none

		if( $old_role == $new_role ) {
			restore_current_blog();
			continue;
		}


		update_option( $this->default_role_name, $new_role );

		restore_current_blog();
	}
}



public function __construct() {

$this->default_role = get_option($this->default_role_name);

add_action('wp_login', array($this,"wp_login"),10,2);
add_action( 'wpmu_options', array($this,"wpmu_options"));
add_action( 'update_wpmu_options', array($this,"update_wpmu_options"));

}

}

$lh_assign_role_on_login_instance = new LH_assign_role_on_login_plugin();



?>