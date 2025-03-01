<?php

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_name']) || !isset($_SESSION['admin_role'])) {
    header("Location: login.php?status=yetkisiz-giris"); 
    exit();
}

?>


<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ekoloji Adası Yönetim Paneli</title>

    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/basic.css" rel="stylesheet" />
    <link href="assets/css/custom.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>

<nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                
                <a class="navbar-brand" href="index.php">Ekoloji Adası</a>
            </div>

            <div class="header-right">

                <a href="edit_profile.php"class="btn btn-info"><i class="fa fa-user"></i><b> <?php echo $_SESSION['admin_name']?> </b></a>
                <a href="logout.php" class="btn btn-danger" title="Logout"><i class="fa fa-sign-out "></i></a>

            </div>
</nav>