public function generate_page() {
        $url                   = $this->input->get_post('url');
        $need_banner           = $this->input->get_post('need_banner');
        $need_callback_form    = $this->input->get_post('need_callback_form');
        $need_upcoming_batches = $this->input->get_post('need_upcoming_batches');
        $need_downloads        = $this->input->get_post('need_downloads');
        $need_exit_splash      = $this->input->get_post('need_exit_splash');
        $need_content_1        = $this->input->get_post('need_content_1');
        $need_tabs_section     = $this->input->get_post('need_tabs_section');
        $need_content_2        = $this->input->get_post('need_content_2');
        
        
        if($url != '') {
            
            $content = '';
            $base_directory = explode('register', dirname(__FILE__));
            $base_directory = $base_directory[0];
            
            $host_url = explode('register', base_url());
            $host_url = $host_url[0];
            
            $html_blocks = ['#@@@upcoming_batches_header@@@#' => '', '#@@@for_upcoming_batch_hyd@@@#' => '', '#@@@for_upcoming_batch_blr@@@#' => '',
                            '#@@@for_need_downloads@@@#' => '', '#@@@for_call_back_form@@@#' => '', '#@@@for_exit_splash_trigger@@@#' => '',
                            '#@@@for_exit_splash_popup@@@#' => '', '#@@@host_url@@@#' => $host_url];
            
            
            $file_name = $base_directory.'temporary/'.$url.'.html';
            $handle = fopen($file_name, 'w') or die('Cannot open file:  '.$file_name);
            
            $up_to_header = fopen($base_directory."includes_for_dynamic_pages/upto_header.html", "r") or exit('Unable to open file');
            $content .= fread($up_to_header, filesize($base_directory."includes_for_dynamic_pages/upto_header.html"));
            
            if($need_banner == 1) {
                $for_banner = fopen($base_directory."includes_for_dynamic_pages/for_banner.html", "r") or exit('Unable to open file');
                $content .= fread($for_banner, filesize($base_directory."includes_for_dynamic_pages/for_banner.html"));
            }
            
            if(count($need_upcoming_batches) > 0 || count($need_downloads) > 0 || $need_callback_form != '') {
                
                $for_call_back_section = fopen($base_directory."includes_for_dynamic_pages/for_call_back_section.html", "r") or exit('Unable to open file');
                $content .= fread($for_call_back_section, filesize($base_directory."includes_for_dynamic_pages/for_call_back_section.html"));
                
                if(count($need_upcoming_batches) > 0) {
                    $html_blocks['#@@@upcoming_batches_header@@@#'] = '<header><h2 >Upcoming Batches</h2></header>';
                    
                    if($need_upcoming_batches['HYDERABAD'] == 1) {
                        $for_upcoming_batch_hyd = fopen($base_directory."includes_for_dynamic_pages/for_upcoming_batch_hyd.html", "r") or exit('Unable to open file');
                        $html_blocks['#@@@for_upcoming_batch_hyd@@@#'] = fread($for_upcoming_batch_hyd, filesize($base_directory."includes_for_dynamic_pages/for_upcoming_batch_hyd.html"));
                    }
                    
                    if($need_upcoming_batches['BENGULURU'] == 1) {
                        $for_upcoming_batch_blr = fopen($base_directory."includes_for_dynamic_pages/for_upcoming_batch_blr.html", "r") or exit('Unable to open file');
                        $html_blocks['#@@@for_upcoming_batch_blr@@@#'] = fread($for_upcoming_batch_blr, filesize($base_directory."includes_for_dynamic_pages/for_upcoming_batch_blr.html"));
                    }
                }
                
                if($need_downloads['PGP_BROCHURE'] == 1) {
                    $for_need_downloads = fopen($base_directory."includes_for_dynamic_pages/for_need_downloads.html", "r") or exit('Unable to open file');
                    $html_blocks['#@@@for_need_downloads@@@#'] = fread($for_need_downloads, filesize($base_directory."includes_for_dynamic_pages/for_need_downloads.html"));
                }
                
                if($need_callback_form == 1) {
                    $for_call_back_form = fopen($base_directory."includes_for_dynamic_pages/for_call_back_form.html", "r") or exit('Unable to open file');
                    $html_blocks['#@@@for_call_back_form@@@#'] = fread($for_call_back_form, filesize($base_directory."includes_for_dynamic_pages/for_call_back_form.html"));
                }
            }
            
            if($need_content_1 == 1) {
                $content .= '<section class="callback_quickinfo_container container"><div class="row"><div class="col-12">'.$this->input->get_post('content_1').'</div></div></section>';
            }
            
            if($need_tabs_section == 1) {
                $content .= '<section class="container"><div class="row"><div class="col-12"><div class="accordion_content" ng-controller="pgpController"><div class="accordion">';
                foreach($this->input->get_post('tabs') as $key => $value) {
                    $tab_name    = $value['name'];
                    $tab_content = $value['content'];
                    if($tab_name != '') {
                        $id = md5($tab_name.'-'. microtime());
                        $content .= '<div class="card">';
                        $content .= '<div class="card-header" id="'.$id.'">
                                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse_'.$id.'" aria-expanded="false" aria-controls="collapse_'.$id.'">
                                                    <span class="pull-left">'.$tab_name.'</span>
                                                    <i class="fa fa-chevron-down pull-right"></i>
                                            </button>
                                    </div>
                                    <div id="collapse_'.$id.'" class="collapse" aria-labelledby="'.$id.'" data-parent="">
                                        <div class="card-body">'.$tab_content.'</div>
                                    </div>';
                        $content .= '</div>';
                    }
                }
                $content .= '</div></div></div></div></section>';
            }
            
            if($need_content_2 == 1) {
                if($need_tabs_section == 1) {
                    $content .= '<div class="callback_quickinfo_container container">&nbsp;</div>';
                }
                $content .= '<section class="callback_quickinfo_container container"><div class="row"><div class="col-12">'.$this->input->get_post('content_2').'</div></div></section>';
            }
            
            if($need_exit_splash == 1) {
                $for_exit_splash_trigger = fopen($base_directory."includes_for_dynamic_pages/for_exit_splash_trigger.html", "r") or exit('Unable to open file');
                $html_blocks['#@@@for_exit_splash_trigger@@@#'] = fread($for_exit_splash_trigger, filesize($base_directory."includes_for_dynamic_pages/for_exit_splash_trigger.html"));
                $html_blocks['#@@@for_exit_splash_trigger@@@#'] = str_replace('#@@@exit_splash_title_text@@@#', $this->input->get_post('exit_splash_title_text'), $html_blocks['#@@@for_exit_splash_trigger@@@#']);
                
                $for_exit_splash_popup = fopen($base_directory."includes_for_dynamic_pages/for_exit_splash_popup.html", "r") or exit('Unable to open file');
                $html_blocks['#@@@for_exit_splash_popup@@@#'] = fread($for_exit_splash_popup, filesize($base_directory."includes_for_dynamic_pages/for_exit_splash_popup.html"));
                $html_blocks['#@@@for_exit_splash_popup@@@#'] = str_replace('#@@@exit_splash_title_text@@@#', $this->input->get_post('exit_splash_title_text'), $html_blocks['#@@@for_exit_splash_popup@@@#']);
                $html_blocks['#@@@for_exit_splash_popup@@@#'] = str_replace('#@@@exit_splash_sub_title_text@@@#', $this->input->get_post('exit_splash_sub_title_text'), $html_blocks['#@@@for_exit_splash_popup@@@#']);
            }
            
            $for_footer = fopen($base_directory."includes_for_dynamic_pages/for_footer.html", "r") or exit('Unable to open file');
            $content .= fread($for_footer, filesize($base_directory."includes_for_dynamic_pages/for_footer.html"));
            
            foreach($html_blocks as $key => $value) {
                $content = str_replace($key, $value, $content);
            }
            
            fwrite($handle, $content);
            fclose($handle);
        }
    }
