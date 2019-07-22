              <li class="nav-item">
                <a class="nav-link<?php echo ($_SERVER['PHP_SELF']=='/user.php')?' active" href="#">':'" href="user.php">';?>
                  <span data-feather="user"></span>
                  <span class="d-none d-md-inline">Profile</span><?php if($_SERVER['PHP_SELF']=='/user.php')echo ' <span class="sr-only">(current)</span>';?>
                </a>
              </li>
              <?php
              if($_SESSION['UM_DATA']['perm']['admin']){
              ?>
              <li class="nav-item">
                <a class="nav-link<?php echo ($_SERVER['PHP_SELF']=='/admin.php')?' active" href="#">':'" href="admin.php">';?>
                  <span data-feather="star"></span>
                  <span class="d-none d-md-inline">Admin</span><?php if($_SERVER['PHP_SELF']=='/admin.php')echo ' <span class="sr-only">(current)</span>';?>
                </a>
              </li>
              <?php
              }
              ?>
              <li class="nav-item">
                <a class="nav-link<?php echo ($_SERVER['PHP_SELF']=='/dashboard_example.php')?' active" href="#">':'" href="dashboard_example.php">';?>
                  <span data-feather="file"></span>
                  <span class="d-none d-md-inline">Example</span><?php if($_SERVER['PHP_SELF']=='/dashboard_example.php')echo ' <span class="sr-only">(current)</span>';?>
                </a>
              </li>
              <!--
                 to add new links find a suitable icon here(https://feathericons.com/) and then replace its name with "shopping-cart" below. and make a file.php in root folder and change file.php in below to filename you've created
              <li class="nav-item">
                <a class="nav-link<?php echo ($_SERVER['PHP_SELF']=='/file.php')?' active" href="#">':'" href="file.php">';?>
                  <span data-feather="shopping-cart"></span>
                  <span class="d-none d-md-inline">Products</span><?php if($_SERVER['PHP_SELF']=='/file.php')echo ' <span class="sr-only">(current)</span>';?>
                </a>
              </li>
               -->
