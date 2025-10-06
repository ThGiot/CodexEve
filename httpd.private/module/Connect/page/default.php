      
       <?php
       $page_titre ='Connect';
       $page_sous_titre = 'default';
       if (isset($page_titre)){
              echo '<h2 class="mb-2 lh-sm">'.$page_titre.'</h2>';
          }
          if (isset($page_sous_titre)){
              echo ' <p class="text-700 lead mb-2">'.$page_sous_titre.'</p>';
          }
      
        echo $data['page'];
      print_r($_SESSION['module_client_liste']);

       ?>
        <div class="col-lg-3 col-md-4 col-sm-6"><span class="icon-list-item d-none">fas fa-voicemail</span>
                  <div class="border border-300 rounded-2 p-3 mb-4 text-center bg-white dark__bg-1000 shadow-sm"><span class=" fas fa-voicemail"></span>
                    <input class="form-control form-control-sm mt-3 text-center w-100 text-dark bg-200 dark__bg-1100 border-300" type="text" readonly="readonly" value="fas fa-voicemail" />
                  </div>
                </div>
                <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="fas fa-voicemail"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Par import Excell</span></span></div>
                                                                                   <span class="nav-link-icon"><span class="fas fa-voicemail"></span></span></span><span class="nav-link-text">test</span>   

                    