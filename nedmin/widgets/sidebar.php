<?php 


require '../netting/connect.php';



if(isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];  
    
   
    $query = "SELECT profile_picture,name,username,role FROM admins WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);  
    $stmt->execute();
    $stmt->bind_result($profile_picture, $name, $username, $role);
    $stmt->fetch();

    $stmt->close();
    $conn->close();
} else {
    header("Location: login.php?status=yetkisiz-giris"); 
    exit();
}

?>


<nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li>
                        <div class="user-img-div">
                            <img src="../uploads/users_profile/<?php echo $profile_picture?>" style="object-fit: cover;height:70px;width:70px;"class="img-thumbnail" />
                            <div class="inner-text">
                            <b><?php echo $username?></b><br/>
                            <small><?php echo $name?></small><br/>
                            <small><?php echo $role?></small>

                            </div>
                        </div>

                    </li>


                    <li>
                        <a href="index.php"><i class="fa fa-dashboard "></i>Ana Sayfa</a>
                    </li> 
                    
                    <li>
                        <a href="edit_profile.php"><i class="fa fa-user"></i>Hesabınız</a>
                    </li>   
                    
                    <li>
                        <a href="blog_list.php"><i class="fa fa-anchor"></i>Bloglar</a>
                    </li>
            


            <?php if($_SESSION['admin_role']  == 'admin'):?>
                    <li>
                        <a href="admin_list.php"><i class="fa fa-flash "></i>Yönetim Kadrosu</a>
                    </li>
                   

                     <li>
                        <a href="forum.php"><i class="fa fa-bug "></i>Forum</a>
                    </li>
                    <li>
                        <a href="visitor_upload.php"><i class="fa fa-sign-in "></i>Ziyaretçilerden Gelenler</a>
                    </li>

                    <li>
                        <a href="users_list.php"><i class="fa fa-users "></i>Kullanıclar</a>
                    </li>
            <?php endif;?>


                </ul>

            </div>

        </nav>