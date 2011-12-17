<?php

/**
 * @package Administration
 */
class P2P_Field_Create implements P2P_Field {

	function get_title() {
		// Not needed
		return '';
	}

	function render( $key, $p2p_id, $post_id ) {
		$data = array(
			'post_id' => $post_id,
			'title' => __( 'Create connection', P2P_TEXTDOMAIN )
		);

		return P2P_Mustache::render( 'column-create', $data );
	}
}

/**
 * @package Administration
 */
class P2P_Field_Delete implements P2P_Field {

	function get_title() {
		$data = array(
			'title' => __( 'Delete all connections', P2P_TEXTDOMAIN )
		);

		return P2P_Mustache::render( 'column-delete-all', $data );
	}

	function render( $key, $p2p_id, $post_id ) {
		$data = array(
			'p2p_id' => $p2p_id,
			'title' => __( 'Delete connection', P2P_TEXTDOMAIN )
		);

		return P2P_Mustache::render( 'column-delete', $data );
	}
}

/**
 * @package Administration
 */
class P2P_Field_Order implements P2P_Field {

	protected $sort_key;

	function __construct( $sort_key ) {
		$this->sort_key = $sort_key;
	}

	function get_title() {
		return '';
	}

	function render( $key, $p2p_id, $post_id ) {
		return html( 'input', array(
			'type' => 'hidden',
			'name' => "p2p_order[$this->sort_key][]",
			'value' => $p2p_id
		) );
	}
}

/**
 * @package Administration
 */
class P2P_Field_Generic implements P2P_Field {

	protected $data;

	function __construct( $data ) {
		$this->data = $data;
	}

	function get_title() {
		return $this->data['title'];
	}

	function render( $key, $p2p_id, $post_id ) {
		$args = array(
			'name' => $key,
			'type' => $this->data['type']
		);

		if ( isset( $this->data['values'] ) )
			$args['value'] = $this->data['values'];

		$single_value = ( 'checkbox' != $args['type'] );

		$form = new scbForm(
			array( $key => p2p_get_meta( $p2p_id, $key, $single_value ) ),
			array( 'p2p_meta', $p2p_id )
		);

		return $form->input( $args );
	}
}

/**
 * @package Administration
 */
class P2P_Field_Title_Post implements P2P_Field {

	protected $title;

	function __construct( $title = '' ) {
		$this->title = $title;
	}

	function get_title() {
		return $this->title;
	}

	function render( $key, $p2p_id, $post_id ) {
		$data = array(
			'title-attr' => get_permalink( $post_id ),
			'title' => get_post_field( 'post_title', $post_id ),
			'url' => get_edit_post_link( $post_id ),
		);

		$post_status = get_post_status( $post_id );

		if ( 'publish' != $post_status ) {
			$status_obj = get_post_status_object( $post_status );
			if ( $status_obj ) {
				$data['status']['text'] = $status_obj->label;
			}
		}

		return P2P_Mustache::render( 'column-title', $data );
	}
}


class P2P_Field_Title_User extends P2P_Field_Title_Post {

	function render( $key, $p2p_id, $user_id ) {
		$user = get_user_by( 'id', $user_id );

		$data = array(
			'title-attr' => '',
			'title' => $user->display_name,
			'url' => $this->get_edit_url( $user_id ),
		);

		return P2P_Mustache::render( 'column-title', $data );
	}

	private function get_edit_url( $user_id ) {
		if ( get_current_user_id() == $user_id ) {
			$edit_link = 'profile.php';
		} else {
			$edit_link = "user-edit.php?user_id=$user_id";
		}

		return admin_url( $edit_link );
	}
}

