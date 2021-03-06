<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package _bem
 */

if ( ! function_exists( '_bem_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @param string $modifier Accepts modifier string.
	 */
    function _bem_posted_on( $modifier = '' ) {
 		$modifier_time = '';
 		$modifier_time_updated = '';
 		$modifier_posted_on = '';
 		$modifier_author = '';
 		$modifier_author_link = '';
 		$modifier_date_wrapper = '';
 		$modifier_author_wrapper = '';
 
 		if ( is_string( $modifier ) ) {
 			$modifier_time = " _content__date--$modifier";
 			$modifier_time_updated = " _content__date-update--$modifier";
 			$modifier_posted_on = " _content__date-link--$modifier";
 			$modifier_author = " _content__author--$modifier";
 			$modifier_author_link = " _content__author-link--$modifier";
 			$modifier_date_wrapper = " _content__date-wrapper--$modifier";
 			$modifier_update_wrapper = " _content__update-wrapper--$modifier";
 			$modifier_author_wrapper = " _content__author-wrapper--$modifier";
 		}
 
     	$time_string = esc_html( get_the_date( ) );
 
     	$posted_on = sprintf(
     		esc_html_x( 'Posted on %s', 'post date', '_bem' ),
     		$time_string
     	);
 
     	$byline = sprintf(
     		esc_html_x( 'Author %s', 'post author', '_bem' ),
     		'<a class="url fn n _content__author-link'.$modifier_author_link.'" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author( ) ) . '</a>'
     	);
 
     	echo '<span class="posted-on _content__meta-time _content__date-wrapper'.$modifier_date_wrapper.'">' . $posted_on . '</span>';
 
     	if ( get_the_modified_date() && get_the_modified_date() !== get_the_date() ) {
     		$updated_on = esc_html__( 'Updated on', '_bem' );
 
     		echo '<span class="byline _content__meta-time _content__update-wrapper'.$modifier_update_wrapper.'">' . $updated_on . ' ' . get_the_modified_date() . '</span>';
     	}
 
     	// echo '<span class="author vcard _content__author'.$modifier_author.'">'.$byline.'</span>';
 	}
endif;

if ( ! function_exists( '_bem_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 *
	 * @param string $modifier Accepts modifier string.
	 */
	function _bem_entry_footer( $modifier = '' ) {

		if ( is_string( $modifier ) ) {
			$category_links = " _content__category-links--$modifier";
			$tag_links = " _content__tag-links--$modifier";
			$comments_links = " _content__comments-link--$modifier";
			$edit_links = " _content__edit-item--$modifier";
		}

		// Hide category and tag text for pages.
		if ( 'post' === get_post_type( ) ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', '_bem' ) );

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html__( ', ', '_bem' ), '' );
			if ( $tags_list ) {
				printf( '<div class="tags-links _content__tag-links' . $tag_links . '">' . esc_html__( '%1$s ', '_bem' ) . '</div>', $tags_list ); // WPCS: XSS OK.
			}
		}

		if ( ! is_single( ) && ! post_password_required( ) && ( comments_open( ) || get_comments_number( ) ) ) {
			echo '<span class="comments-link _content__comments-link' . esc_html( $comments_links ) . '">';
			comments_popup_link( esc_html__( 'Leave a comment', '_bem' ), esc_html__( '1 Comment', '_bem' ), esc_html__( '% Comments', '_bem' ) );
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				esc_html__( 'Edit %s', '_bem' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			),
			'<div class="edit-link _content__edit-item' . $edit_links . '">',
			'</div>'
		);
	}
endif;

/**
 * Adds class for tags
 *
 * @param string $html Accepts html string.
 */
function add_class_the_tags( $html ) {
		$postid = get_the_ID( );
		$html = str_replace( '<a','<a class="_content__tag-link"',$html );
		return $html;
}

add_filter( 'the_tags','add_class_the_tags',10,1 );

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function _bem_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( '_bem_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'		 => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'		 => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( '_bem_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so _bem_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so _bem_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in _bem_categorized_blog.
 */
function _bem_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( '_bem_categories' );
}

add_action( 'edit_category', '_bem_category_transient_flusher' );
add_action( 'save_post',		 '_bem_category_transient_flusher' );
