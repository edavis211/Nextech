<?php
/**
 * Resource Filter AJAX Handler
 * Handles AJAX requests for filtering resources based on taxonomies and ACF fields
 */

class Resource_Filter_Ajax {
    
    public function __construct() {
        add_action( 'wp_ajax_filter_resources', array( $this, 'filter_resources' ) );
        add_action( 'wp_ajax_nopriv_filter_resources', array( $this, 'filter_resources' ) );
    }
    
    /**
     * Main filter resources AJAX handler
     */
    public function filter_resources() {
        // Verify nonce for security
        if ( ! wp_verify_nonce( $_POST['nonce'], 'resource_filter_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }
        
        // Get filter parameters
        $filters = $this->sanitize_filters( $_POST );
        
        // Build query arguments
        $query_args = $this->build_query_args( $filters );
        
        // Execute query
        $query = new WP_Query( $query_args );
        
        // Calculate displayed count for current page
        $posts_per_page = $filters['posts_per_page'];
        $current_page = $filters['page'];
        $total_found = $query->found_posts;
        $current_showing = min($posts_per_page * $current_page, $total_found);
        
        // Generate response
        $response = array(
            'html' => $this->render_resources( $query ),
            'count' => $total_found,
            'showing' => $current_showing,
            'pagination' => $this->get_pagination_data( $query ),
            'filters_applied' => $filters
        );
        
        wp_send_json_success( $response );
    }
    
    /**
     * Sanitize filter data
     */
    private function sanitize_filters() {
        return [
            'search' => sanitize_text_field($_POST['search'] ?? ''),
            'subject_matter' => array_map('sanitize_text_field', $_POST['subject_matter'] ?? []),
            'resource_type' => array_map('sanitize_text_field', $_POST['resource_type'] ?? []),
            'academic_standard' => array_map('sanitize_text_field', $_POST['academic_standard'] ?? []),
            'grade_level_min' => intval($_POST['grade_level_min'] ?? -1),
            'grade_level_max' => intval($_POST['grade_level_max'] ?? 12),
            'sort_by' => sanitize_text_field($_POST['sort_by'] ?? 'newest'),
            'posts_per_page' => intval($_POST['posts_per_page'] ?? 12),
            'page' => intval($_POST['page'] ?? 1),
        ];
    }
    
    /**
     * Build WP_Query arguments based on filters
     */
    private function build_query_args( $filters ) {
        $args = array(
            'post_type' => 'resource', // Adjust to your resource post type
            'post_status' => 'publish',
            'posts_per_page' => $filters['posts_per_page'],
            'paged' => $filters['page'],
            'meta_query' => array(),
            'tax_query' => array()
        );
        
        // Search query
        if ( ! empty( $filters['search'] ) ) {
            $args['s'] = $filters['search'];
        }
        
        // Taxonomy queries
        $this->add_taxonomy_queries( $args, $filters );
        
        // Grade range query (ACF fields)
        $this->add_grade_range_query( $args, $filters );
        
        // Add sorting
        $this->add_sorting( $args, $filters );
        
        // Set relation for multiple queries
        if ( count( $args['tax_query'] ) > 1 ) {
            $args['tax_query']['relation'] = 'AND';
        }
        
        if ( count( $args['meta_query'] ) > 1 ) {
            $args['meta_query']['relation'] = 'AND';
        }
        
        return $args;
    }
    
    /**
     * Add taxonomy queries to WP_Query args
     */
    private function add_taxonomy_queries( &$args, $filters ) {
        $taxonomy_mapping = array(
            'subject_matter' => 'subject-matter',
            'resource_type' => 'resource-type',
            'academic_standard' => 'academic-standard'
        );
        
        foreach ( $taxonomy_mapping as $filter_key => $taxonomy ) {
            if ( ! empty( $filters[ $filter_key ] ) ) {
                $args['tax_query'][] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $filters[ $filter_key ],
                    'operator' => 'IN'
                );
            }
        }
    }
    
    /**
     * Add grade range query using ACF fields
     */
    private function add_grade_range_query( &$args, $filters ) {
        $grade_min = $filters['grade_level_min'];
        $grade_max = $filters['grade_level_max'];
        
        if ( empty( $grade_min ) && empty( $grade_max ) ) {
            return;
        }
        
        // Convert grade slugs to numeric values for comparison
        $min_numeric = $this->grade_slug_to_numeric( $grade_min );
        $max_numeric = $this->grade_slug_to_numeric( $grade_max );
        
        $grade_meta_query = array( 'relation' => 'AND' );
        
        // Resources where minimum_grade <= selected_max AND maximum_grade >= selected_min
        if ( $min_numeric !== null ) {
            $grade_meta_query[] = array(
                'key' => 'grade_range_maximum_grade',
                'value' => $min_numeric,
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }
        
        if ( $max_numeric !== null ) {
            $grade_meta_query[] = array(
                'key' => 'grade_range_minimum_grade',
                'value' => $max_numeric,
                'compare' => '<=',
                'type' => 'NUMERIC'
            );
        }
        
        if ( count( $grade_meta_query ) > 1 ) {
            $args['meta_query'][] = $grade_meta_query;
        }
    }
    
    /**
     * Convert grade slug to numeric value for ACF field comparison
     */
    private function grade_slug_to_numeric( $slug ) {
        if ( empty( $slug ) ) {
            return null;
        }
        
        switch ( strtolower( $slug ) ) {
            case 'pk':
                return -1;
            case 'k':
                return 0;
            default:
                if ( is_numeric( $slug ) ) {
                    return intval( $slug );
                }
                return null;
        }
    }
    
    /**
     * Add sorting to query arguments
     */
    private function add_sorting( &$args, $filters ) {
        $sort_by = $filters['sort_by'] ?? 'newest';
        
        switch ( $sort_by ) {
            case 'oldest':
                $args['orderby'] = 'date';
                $args['order'] = 'ASC';
                break;
            case 'a-z':
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
                break;
            case 'z-a':
                $args['orderby'] = 'title';
                $args['order'] = 'DESC';
                break;
            case 'newest':
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
        }
    }

    /**
     * Render resources HTML
     */
    private function render_resources( $query ) {
        if ( ! $query->have_posts() ) {
            return '<div class="no-resources-found">
                        <p>No resources found matching your criteria.</p>
                        <button type="button" class="clear-filters-btn" aria-label="Clear all filters">Clear Filters</button>
                    </div>';
        }
        
        ob_start();
        
        
        while ( $query->have_posts() ) {
            $query->the_post();
            
            // Use your existing resource card template
            get_template_part( 'template-parts/cards/resource-card-detail' );
        }
        
        
        // Reset post data
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Get pagination data
     */
    private function get_pagination_data( $query ) {
        return array(
            'current_page' => $query->query['paged'] ?? 1,
            'total_pages' => $query->max_num_pages,
            'total_posts' => $query->found_posts,
            'posts_per_page' => $query->query['posts_per_page'] ?? 12
        );
    }
}

// Initialize the class
new Resource_Filter_Ajax();
