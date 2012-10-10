<?php
/*
Plugin Name: JAM Recent Posts
Plugin URI: http://joshmccarty.com
Description: Adds a widget that displays recent posts including a featured image thumbnail and publish date.
Version: 0.1
Author: Josh McCarty
Author URI: http://joshmccarty.com
License: GPL v2
*/

/*  Copyright 2012 Josh McCarty  (email : josh@joshmccarty.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Enable internationalization
load_plugin_textdomain( 'jam-recent-posts', false, basename( dirname( __FILE__ ) ) . '/languages' );

/**
 * Adds JAM_Recent_Posts widget.
 */
class JAM_Recent_Posts extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'jam_recent_posts', // Base ID
			'JAM Recent Posts', // Name
			array( 'description' => __( 'Displays recent posts including a featured image thumbnail and publish date.', 'jam-recent-posts' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$count = $instance['count'];
		$size = $instance['size'];

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title; ?>
		<ul id="<?php echo $args['widget_id']; ?>" class="jam-recent-posts">
		<?php
		$args = array(
			'numberposts' => $count,
			'post_status' => 'publish' );
		$recent_posts = wp_get_recent_posts( $args );
		foreach( $recent_posts as $recent ) {
			echo '<li>';
			echo '<a href="' . get_permalink( $recent['ID'] ) . '" title="' . __( 'Permalink to ', 'jam-recent-posts' ) . esc_attr( $recent['post_title'] ) . '">' . get_the_post_thumbnail( $recent['ID'], array( $size, $size ), array( 'class' => 'jam-recent-posts-thumbnail' ) ) . '<span class="jam-recent-posts-title">' . $recent['post_title'] . '</span></a><span class="jam-recent-posts-meta">' . get_post_time( get_option( 'date_format' ), false, $recent['ID'], true ) . '</span></li>';
		}
		?>
		</ul>
		<?php
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = absint( $new_instance['count'] );
		$instance['size'] = absint( $new_instance['size'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Recent Posts', 'jam-recent-posts' );
		}
		if ( isset( $instance['count'] ) ) {
			$count = $instance['count'];
		}
		else {
			$count = 3;
		}
		if ( isset( $instance['size'] ) ) {
			$size = $instance['size'];
		}
		else {
			$size = 60;
		}
		?>
		<p>
			<label for="<?php echo $this -> get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'jam-recent-posts' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this -> get_field_id( 'count' ); ?>"><?php _e( 'Number of Posts:', 'jam-recent-posts' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this -> get_field_id( 'size' ); ?>"><?php _e( 'Size of Thumbnail:', 'jam-recent-posts' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" type="number" value="<?php echo esc_attr( $size ); ?>" />
		</p>
		<?php
	}

} // class JAM_Recent_Posts

// register JAM_Recent_Posts widget
add_action( 'widgets_init', create_function( '', 'register_widget( "jam_recent_posts" );' ) );

// Embed CSS for the widget
function jam_recent_posts_styles() {
    ?><style type="text/css">
.jam-recent-posts {
	list-style: none;
}
.jam-recent-posts li {
	clear: both;
	margin: 1em 0;
	overflow: hidden;
	position: relative;
}
.jam-recent-posts-thumbnail {
	display: block;
	float: left;
	margin-right: 10px;
}
.jam-recent-posts-meta {
	display: block;
}
</style><?php
}
add_action( 'wp_head', 'jam_recent_posts_styles' );

?>