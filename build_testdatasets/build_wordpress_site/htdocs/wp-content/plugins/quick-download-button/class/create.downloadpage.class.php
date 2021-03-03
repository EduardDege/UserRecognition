<?php 

defined( 'ABSPATH' ) || exit; // Exit if accessed directly
    
    /**
     * QDBU_CreateDownloadPage
     * @usage For creating page
     */
    class QDBU_CreateDownloadPage {
        private $current_user, $page, $pid, $title;

        public function quickDownloadPage() {

            if ( ! current_user_can( 'activate_plugins' ) ) return;

            global $wpdb;
            $this->title = 'Quick Download Button';
  
            if( null == get_page_by_title( $this->title, 'OBJECT', 'post' ) & $this->post_check_duplicates($this->title)!==1) { 
                
                $this->current_user = wp_get_current_user();

                // create post object
                $this->page = array(
                    'post_title'  => __( 'Quick Download Button' ),
                    'post_status' => 'publish',
                    'post_author' => $this->current_user->ID,
                    'post_type'   => 'page',
                );
                
                // Insert the post into the database
                if(current_user_can( 'administrator' ))  $this->pid = wp_insert_post( $this->page );
               
                // Store the page ID 
                if (FALSE === get_option('qdbu_quick_download_button_page_id') && FALSE === update_option('qdbu_quick_download_button_page_id',FALSE)) add_option('qdbu_quick_download_button_page_id', $this->pid);

                //Update page ID after re-activation
                if ( $this->pid !== get_option('qdbu_quick_download_button_page_id') ) update_option( 'qdbu_quick_download_button_page_id', $this->pid);
            }

        }

        
        /**
         * @usage Check if page already exists using page title 
         *
         * @param $title
         * @return void
         */
        public function post_exists( $title ) {
            global $wpdb;

            $post_title   = wp_unslash( sanitize_post_field( 'post_title', $title, 0, 'db' ) );
            
            $query = "SELECT ID FROM $wpdb->posts WHERE 1=1";
            $args  = array();

            
            if ( ! empty( $title ) ) {
                $query .= ' AND post_title = %s';
                $args[] = $post_title;
            }

            if ( ! empty( $args ) ) {
                return (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) );
            }

            return 0;
        }
        
        /**
         * @usage Check duplicate title
         *
         * @param $title
         * @return void
         */
        public function post_check_duplicates($title) { 
                
            $post_id = post_exists( $title );
            if (!$post_id) {
                return 0;
            } else {
                return 1;
            }
            
        }
        
        /**
         * @usage return ID of duplicate post
         *
         * @param $title
         * @return int
         */
        public function getid_duplicates($title) { 
                
            $post_id = post_exists( $title );
            if ($post_id) {
                return (int) $post_id;
            } 	
        }

}


