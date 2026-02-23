<?php
global $post; 

$condition = $ix= 1;
if(isset($_GET['active_tab'])){
    $condition = $_GET['active_tab']; 
}
$tabs=tabLinks($condition,$ix);
echo do_shortcode('
[tab_nav type="seven-up"]
'.$tabs['tabs_link'].'
[/tab_nav]
[tabs]

'.$tabs['tabs_content'].'

[/tabs]');


function tabLinks($condition,$ix){
    global $post; 
    if( have_rows('tabs') ){
        
        $a='';
        $l='';
        
        
        while( have_rows('tabs') ): the_row();
    		// vars
    		$title = get_sub_field('tab_title');
            $content  = get_sub_field('tab_content');
            $display_default_image = "<img src='/wp-content/uploads/2021/10/Coming_soon_image.png'>";
            $print_tab_data = '<ul class="pdfs_list">';
            $print_tab_display= get_sub_field('is_this_printable_tab');
            if($print_tab_display){
                
                if( have_rows('pdfs') ){
                    
                    while( have_rows('pdfs') ): the_row(); 
                               
                      

                        $document_url = get_sub_field('pdf_file');
                        $document_name = get_sub_field('pdf_name'); 

                         $pdf_id = attachment_url_to_postid($document_url);    
                        $thumbnail_url = wp_get_attachment_image_url($pdf_id, 'large');
                        
                        $thumb_img = $thumbnail_url 
                        ? "<img src='{$thumbnail_url}' alt='{$document_name}' class='pdf-thumbnail' />"
                        : "<img src='/wp-content/uploads/2021/10/pdf.png' alt='PDF Icon' class='pdf-thumbnail' />";

                        $print_tab_data .= "<li class='documents-list'>
                            <a href='{$document_url}' class='document-items' target='_blank'>{$thumb_img} </a>
                            <a href='{$document_url}' class='document-items' target='_blank'>{$document_name}</a>
                        </li>";
        
                    
                    endwhile; 
                    
                    
                
                }
                
            }
            $print_tab_data.='</ul>';
            
            
            $a.='[tab_nav_item title="'.$title.'" '.($ix==$condition ? 'active="true"' : '').']';
            
            $l.='[tab '.($ix==$condition ? 'active="true"' : '').']';
            
            if (( $post->post_type == 'songs' ) && has_term( 'free-stuff', 'topics' )){
                 if(have_rows('pdfs') || $content) {
                $l.= ($content);
                $l.= ($print_tab_data);
                }else{
                    $l.= ($display_default_image);
                }
            }
            
            if (is_user_logged_in() && !(( $post->post_type == 'songs' ) && has_term( 'free-stuff', 'topics' )) ) { 
            if(have_rows('pdfs') || $content) {
                $l.= ($content);
                $l.= ($print_tab_data);
            }else{
                $l.= ($display_default_image);
            }
            }else{
                if (!(( $post->post_type == 'songs' ) && has_term( 'free-stuff', 'topics' ))){
                    $l.=  do_shortcode('[mepr-unauthorized-message]');
                    $l.= do_shortcode('[cs_gb id=4652]');
                }
            }
            
            
            $l.='[/tab]';
            
            $ix++;
        endwhile;
        return array('tabs_link'=>$a,'tabs_content'=>$l);
    }
   
}

?>

<?php if($_GET['active_tab']):?>
        <script> var offerTabScroll=true;</script>
<?php endif;?>