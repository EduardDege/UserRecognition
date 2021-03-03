<?php 
defined('ABSPATH') || exit;

/**
 * Shortcode class
 */
class QDBU_QuickDownloadShortCode {

   public $a, $pid, $attachment_id, $url, $download_pid;
   
    
    public function __construct()
    {
        add_shortcode('quick_download_button', array($this, 'quick_download_button_shortcode'));
    }
    
    /**
     * @usage Add shortcode
     *
     * @param  mixed $attr
     * @return void
     */
    public function quick_download_button_shortcode($attr) {

        $this->a = shortcode_atts(array(
            'title' => 'Download',
            'file_size' => '',
            'url' => '',
            'extension' => '',
            'extension_text' => '0',
            'url_external' => '',
        ), $attr);

        global $post;
        $this->pid = $post->ID;
        $this->download_pid = (int)get_option('qdbu_quick_download_button_page_id');
    
    
        $this->attachment_id = attachment_url_to_postid($this->a['url']);  //get attachment id from URL
    
        $quick_download_button_url = qdbu_default_url() . '?aid=' . $this->attachment_id; //pass attachment id to plugin download file
    
        $this->url = !empty($this->a['url']) ? $quick_download_button_url : $this->a['url_external']; //if url value is empty set url to external url (url_external)

        return $this->generate_button();

    }
    
    /**
     * @Output download button html
     *
     * @return void
     */
    function generate_button() { 

    ob_start();
    ?>
    
        <div class="button--download" style="margin: 6rem auto;width: 200px;">
            <div class="download-button-inner">
                <button class="g-btn f-l position-relative shadow" type="button"
                    title="<?php  echo esc_attr($this->a['title']); ?>"
                    <?php if(empty($this->a['url_external'])) : ?>
                    data-attachment-id="<?php echo $this->addIDs($this->attachment_id, $this->download_pid); ?>"
                    data-page-id="<?php echo intval($this->download_pid); ?>"
                    data-post-id="<?php echo intval($this->pid); ?>" 
                    <?php else : ?>
                    data-external-url="<?php echo esc_url( $this->a['url_external'] ) ?>"
                    <?php endif; ?>
                   ><?php echo esc_attr($this->a['title']); ?></button></div>
    
            <?php if ('0' != $this->a['extension']) : ?>
                <p class="up"><i class="<?php echo $this->qdbu_extension('icon'); ?>"></i>
                    <?php if ('1' == $this->a['extension_text']) echo '<span>' . $this->qdbu_extension('ext') . '</span>'; ?></p>
            <?php endif;  ?>
    
            <?php if ('1' === $this->a['file_size']) :
                $file_url = filesize($this->qdb_convert_url_to_path($this->a['url']));
                $file_size = $this->qdb_formatSizeUnits($file_url);
                if ('0 bytes' != $file_size) {
                    $file_blob_size = $this->qdb_formatSizeUnits($file_url);
                    $blob_number = explode(' ', $file_blob_size);
                    $blob_number = $blob_number[0];
    
                    $blob_measure = explode(' ', $file_blob_size);
                    $blob_measure = $blob_measure[1];
                }
                /* translators: %1$s is a filesize %2$s is the measurement */
                printf(__('<p class="down"><i class="fi-folder-o"></i> %1$s %2$s</p>'), esc_attr($blob_number), esc_attr($blob_measure));
    
            elseif ('' !== $this->a['file_size']) :
                /* translators: %1$s is a filesize */
                printf(__('<p class="down"><i class="fi-folder-o"></i> %1$s </p>'), esc_attr($this->a['file_size']));
    
                //else :
            ?>
    
            <?php endif; ?>
    
        </div>
    
    <?php
        return ob_get_clean();
    }

    /**
     * @usage Add file extension text, add icon
     * @param  mixed $value
     * @return void
     */
    function qdbu_extension($value='') {
        $ex = array(
            'ext' => '',
            'icon' => '',
        );


        $extension_array = ['pdf', 'mp3', 'mov', 'zip', 'txt', 'doc', 'xml', 'mp4', 'ppt'];

            $extension_path = explode('.', $this->a['url']);
            
            $ex['ext']  = end($extension_path);
            $ex['ext'] = $ex['ext'];
          
            if (in_array(strtolower($ex['ext'] ), $extension_array)) {
                $ex['icon'] = 'fi fi-' . strtolower($ex['ext'] );
            } else {
               
                $image_array = ['jpg', 'jpeg', 'tiff', 'png', 'bmp', 'gif'];
                if (in_array(strtolower($ex['ext'] ), $image_array)) {
                    $ex['icon'] = 'fi fi-image';
                } else {
                    $ex['icon'] = 'fi fi-file';
                }
            }

        if($value == 'icon') {
            $value = $ex['icon'];
        } 
        if($value == 'ext') {
            $value = $ex['ext'];
        }

        return $value;
    }

    
 
    /**
     * @usage Convert URL to path   
     * @param  mixed $url
     * @return void
     */
    function qdb_convert_url_to_path($url)
    {
        return str_replace(
            wp_get_upload_dir()['baseurl'],
            wp_get_upload_dir()['basedir'],
            $url
        );
    }


   
    /**
     * @usage Get file size 
     *
     * @param  mixed $bytes
     * @return void
     */
    function qdb_formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

        
    /**
     * @usage add attachment ID + download page id, use this to disguise real attachment ID when view from browser inspect
     * @param  mixed $num1
     * @param  mixed $num2
     * @return int
     */
    public static function addIDs($num1, $num2) {
        return intval($num1 + $num2);
    }
   

}