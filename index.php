<?php
/**
* Plugin Name: NC Thumbnails for Recent Posts
* Plugin URI: https://www.nealchester.com/downloads/nc-thumbnails-for-recents-posts/
* Description: Creates a widget that shows a thumbnail aside each entry the latest posts count and post type of your choice. You can extend the plugin by using the action hooks "nc_thumbnail_recent_posts_widget_before" and "nc_thumbnail_recent_posts_widget_after."
* Version: 1.0
* Author: Neal Chester
* Author URI: https://www.nealchester.com
* License: GNU General Public License v2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Adds widget: NC Thumbnails for Recent Posts
class Ncthumbnailsforrecen_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'ncthumbnailsforrecen_widget',
			esc_html__( 'NC Thumbnails for Recent Posts', 'textdomain' )
		);
	}

	private $widget_fields = array(
		array(
			'label' => 'Number of posts',
			'id' => 'numberofposts_number',
			'default' => '3',
			'type' => 'number',
		),
		array(
			'label' => 'Post type',
			'id' => 'posttype_text',
			'default' => 'post',
			'type' => 'text',
		),
		array(
			'label' => 'Show Date',
			'id' => 'showdate_select',
			'default' => 'No',
			'type' => 'select',
			'options' => array(
				'Yes',
				'No',
			),
		),
		array(
			'label' => 'Show Thumbnail',
			'id' => 'showthumbnail_select',
			'default' => 'Yes',
			'type' => 'select',
			'options' => array(
				'Yes',
				'No',
			),
		),		
	);

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// Output generated fields
		/*
		echo '<p>'.$instance['numberofposts_number'].'</p>';
		echo '<p>'.$instance['posttype_text'].'</p>';
		echo '<p>'.$instance['showdate_select'].'</p>';
		echo '<p>'.$instance['showthumbnail_select'].'</p>';
		*/
		?>

		<?php
		global $post;
		
		$myposts = get_posts( array(
			'posts_per_page' => $instance['numberofposts_number'],
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post_type'      => $instance['posttype_text']
		) );
	
		if ( $myposts ) {
			foreach ( $myposts as $post ) : 
				setup_postdata( $post ); 

				if(array_key_exists('showdate_select', $_GET)){
				//it exists
				echo $_GET['showdate_select'];
				} else{
				//it does not exist
				}

				if(array_key_exists('showthumbnail_select', $_GET)){
				//it exists
				echo $_GET['showthumbnail_select'];
				} else{
				//it does not exist
				}	

				$showdate = $instance['showdate_select'];
				$showthumb = $instance['showthumbnail_select']; ?>

		<div class="nctrpsw">
			<a class="nctrpsw_link" href="<?php the_permalink(); ?>">
				<div class="nctrpsw_container<?php if($showthumb == 'No'){ echo' nctrpsw_noimage';} ?>">
					<?php if($showthumb == 'Yes'):?>
					<div class="nctrpsw_image"><?php the_post_thumbnail('thumbnail', array( 'class' => 'nctrpsw_img', 'alt' => 'thumbnail' )); ?></div>
					<?php elseif($showthumb == 'No'):?>
					<?php else: ?>
					<div class="nctrpsw_image"><?php the_post_thumbnail('thumbnail', array( 'class' => 'nctrpsw_img', 'alt' => 'thumbnail' )); ?></div>
					<?php endif;?>
					<div class="nctrpsw_text">
						<?php do_action('nc_thumbnail_recent_posts_widget_before');?>
						<div class="nctrpsw_title"><?php the_title(); ?></div>
						<?php if($showdate == 'Yes') { echo'<div class="nctrpsw_date post-date">'.get_the_time(get_option("date_format")).'</div>';} ?>
						<?php do_action('nc_thumbnail_recent_posts_widget_after');?>
					</div>
				</div>
			</a>
		</div>

			<?php
			endforeach;
			wp_reset_postdata(); echo'</ul>';
		}
		?>
		<?php
		
		echo $args['after_widget'];
	}

	public function field_generator( $instance ) {
		$output = '';
		foreach ( $this->widget_fields as $widget_field ) {
			$default = '';
			if ( isset($widget_field['default']) ) {
				$default = $widget_field['default'];
			}
			$widget_value = ! empty( $instance[$widget_field['id']] ) ? $instance[$widget_field['id']] : esc_html__( $default, 'textdomain' );
			switch ( $widget_field['type'] ) {
				case 'select':
					$output .= '<p>';
					$output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'textdomain' ).':</label> ';
					$output .= '<select id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'">';
					foreach ($widget_field['options'] as $option) {
						if ($widget_value == $option) {
							$output .= '<option value="'.$option.'" selected>'.$option.'</option>';
						} else {
							$output .= '<option value="'.$option.'">'.$option.'</option>';
						}
					}
					$output .= '</select>';
					$output .= '</p>';
					break;
				default:
					$output .= '<p>';
					$output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'textdomain' ).':</label> ';
					$output .= '<input class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" type="'.$widget_field['type'].'" value="'.esc_attr( $widget_value ).'">';
					$output .= '</p>';
			}
		}
		echo $output;
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'textdomain' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'textdomain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
		$this->field_generator( $instance );
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		foreach ( $this->widget_fields as $widget_field ) {
			switch ( $widget_field['type'] ) {
				default:
					$instance[$widget_field['id']] = ( ! empty( $new_instance[$widget_field['id']] ) ) ? strip_tags( $new_instance[$widget_field['id']] ) : '';
			}
		}
		return $instance;
	}
}

// Register Widget

function register_ncthumbnailsforrecen_widget() {
	register_widget( 'Ncthumbnailsforrecen_Widget' );
}
add_action( 'widgets_init', 'register_ncthumbnailsforrecen_widget' );



// Load CSS

function ncthumbnailsforrecen_widget_css(){
	wp_register_style('ncthumbnailsforrecen_widget_css', plugin_dir_url( __FILE__ ).'nctrpsw.css', '', '1', 'screen');
	wp_enqueue_style('ncthumbnailsforrecen_widget_css');
}

add_action('wp_enqueue_scripts', 'ncthumbnailsforrecen_widget_css');