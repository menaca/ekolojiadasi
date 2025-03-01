<?php

require 'netting/connect.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$profile_img = '';

if ($user_id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $profile_img  = $user['photo_url'];
}
?>


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="keywords" content="Ekoloji Adası, Ekoblog, Ekoforum, ekoloji, doğa, ada, ekoloji forum, ekoloji blog, blog, ekoloji sosyal medya">
    <meta name="description" content="Ekoloji Adası, doğa severlerin buluştuğu sosyal medya platformu. Ekolojik içerikler paylaşın, sorular sorun, blogları keşfedin. Ekolojik yaşamı öğrenin ve paylaşın. Ekoloji Adası MeNaCa tarafından kodlandı.">
    <meta name="theme-color" content="#cfb02c">
    <meta property="og:description" content="Ekoloji Adası, doğa severlerin buluştuğu sosyal medya platformu. Ekolojik içerikler paylaşın, sorular sorun, blogları keşfedin. Ekolojik yaşamı öğrenin ve paylaşın. Ekoloji Adası MeNaCa tarafından kodlandı.">
    <meta property="og:image" content="/images/logo.png">
    <meta property="og:url" content="https://ekolojiadasi.online">
    <link rel="canonical" href="https://ekolojiadasi.online">
    <meta property="og:title" content="Ekoloji Adası">
    <meta property="og:type" content="website">
    <meta data-intl-tel-input-cdn-path="intlTelInput/">
    <title>Ekoloji Adası</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico?v=2">
    <link rel="stylesheet" href="/main.css" media="screen">
    <script class="u-script" type="text/javascript" src="/jquery-1.9.1.min.js" defer=""></script>
    <script class="u-script" type="text/javascript" src="/main.js" defer=""></script>
  
    <link id="u-theme-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i|Inter:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i">
    
   <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Anasayfa",
          "item": "https://ekolojiadasi.online/index.php"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Eko-Forum",
          "item": "https://ekolojiadasi.online/forum/index.php"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "Eko-Blog",
          "item": "https://ekolojiadasi.online/blogs.php"
        }
      ]
    }
  </script>
  
</head>

<header class="u-clearfix u-header u-header" id="header">
    <div class="u-clearfix u-sheet u-valign-middle-md u-valign-middle-sm u-sheet-1">
        <a href="/index.php" class="u-image u-logo u-image-1" data-image-width="500" data-image-height="500">
            <img src="/images/logo.png" class="u-logo-image u-logo-image-1">
        </a>
        <nav class="u-menu u-menu-one-level u-offcanvas u-menu-1">
            <div class="menu-collapse" style="font-size: 1rem; letter-spacing: 0px; font-weight: 700;">
                <a class="u-button-style u-custom-left-right-menu-spacing u-custom-padding-bottom u-custom-text-active-color u-custom-text-color u-custom-text-hover-color u-custom-top-bottom-menu-spacing u-hamburger-link u-nav-link u-text-active-custom-color-1 u-text-custom-color-1 u-text-hover-custom-color-1 u-hamburger-link-1"
                    href="#">
                    <svg class="u-svg-link" viewBox="0 0 24 24">
                        <use xlink:href="#menu-hamburger"></use>
                    </svg>
                    <svg class="u-svg-content" version="1.1" id="menu-hamburger" viewBox="0 0 16 16" x="0px" y="0px"
                        xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <rect y="1" width="16" height="2"></rect>
                            <rect y="7" width="16" height="2"></rect>
                            <rect y="13" width="16" height="2"></rect>
                        </g>
                    </svg>
                </a>
            </div>
            <div class="u-custom-menu u-nav-container">
                <ul class="u-custom-font u-heading-font u-nav u-unstyled u-nav-1">
                    <li class="u-nav-item"><a
                            class="u-button-style u-nav-link u-text-active-custom-color-1 u-text-grey-50 u-text-hover-custom-color-2"
                            href="/index.php" style="padding: 10px 20px;">Ana Sayfa</a>
                    </li>
                    <li class="u-nav-item"><a
                            class="u-button-style u-nav-link u-text-active-custom-color-1 u-text-grey-50 u-text-hover-custom-color-2"
                            href="/blogs.php" style="padding: 10px 20px;">Blog</a>
                    </li>
                    <li class="u-nav-item"><a
                            class="u-button-style u-nav-link u-text-active-custom-color-1 u-text-grey-50 u-text-hover-custom-color-2"
                            href="/forum/" style="padding: 10px 20px;">Forumlar</a>
                    </li>
                    <li class="u-nav-item"><a
                            class="u-button-style u-nav-link u-text-active-custom-color-1 u-text-grey-50 u-text-hover-custom-color-2"
                            href="index.php#ranks" style="padding: 10px 3px 10px 20px;">Sıralama</a>
                    </li>
                </ul>
            </div>
            <div class="u-custom-menu u-nav-container-collapse">
                <div class="u-black u-container-style u-inner-container-layout u-opacity u-opacity-95 u-sidenav">
                    <div class="u-inner-container-layout u-sidenav-overflow">
                        <div class="u-menu-close"></div>
                        <ul class="u-align-center u-nav u-popupmenu-items u-unstyled u-nav-2">
                            <li class="u-nav-item"><a class="u-button-style u-nav-link" href="/index.php">Ana Sayfa</a>
                            </li>
                            <li class="u-nav-item"><a class="u-button-style u-nav-link" href="/blogs.php">Blog</a>
                            </li>
                            <li class="u-nav-item"><a class="u-button-style u-nav-link" href="/forum/">Forumlar</a>
                            </li>
                            <li class="u-nav-item"><a class="u-button-style u-nav-link">Sıralama</a>
                            </li>

                             <?php if ($user_id): ?>
                                <li class="u-nav-item">
                                    <a class="u-button-style u-nav-link" href="/profile.php">
                                        <img src="/uploads/users_profile/<?php echo htmlspecialchars($profile_img); ?>" alt="Profil Fotoğrafı" style="width: 30px; height: 30px; border-radius: 50%;"> Profil
                                    </a>
                                </li>
                                <li class="u-nav-item"><a class="u-button-style u-nav-link" href="logout.php">Çıkış Yap</a></li>
                            <?php else: ?>
                                <li class="u-nav-item"><a href="/login.php" class="u-button-style u-nav-link" >Giriş Yap</a></li>
                                <li class="u-nav-item"><a href="/register.php" class="u-button-style u-nav-link">Kayıt Ol</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="u-black u-menu-overlay u-opacity u-opacity-70"></div>
            </div>
        </nav>
        <nav class="u-hidden-xs u-menu u-menu-one-level u-offcanvas u-menu-2" data-responsive-from="XS">
            <div class="menu-collapse" style="font-size: 1rem; letter-spacing: 0px; font-weight: 700;">
                <a class="u-button-style u-custom-left-right-menu-spacing u-custom-padding-bottom u-custom-text-active-color u-custom-text-color u-custom-text-hover-color u-custom-top-bottom-menu-spacing u-hamburger-link u-nav-link u-text-active-palette-1-base u-text-hover-palette-2-base"
                    href="#">
                    <svg class="u-svg-link" viewBox="0 0 24 24">
                        <use xlink:href="#svg-1359"></use>
                    </svg>
                    <svg class="u-svg-content" version="1.1" id="svg-1359" viewBox="0 0 16 16" x="0px" y="0px"
                        xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <rect y="1" width="16" height="2"></rect>
                            <rect y="7" width="16" height="2"></rect>
                            <rect y="13" width="16" height="2"></rect>
                        </g>
                    </svg>
                </a>
            </div>
            <div class="u-custom-menu u-nav-container">
                <ul class="u-custom-font u-heading-font u-nav u-unstyled u-nav-3">
                <?php if ($user_id): ?>
                                <li class="u-nav-item">
                                    <a class="u-button-style u-nav-link" href="/profile.php">
                                        <img src="/uploads/users_profile/<?php echo htmlspecialchars($profile_img); ?>" alt="Profil Fotoğrafı" style="width: 40px; height: 40px; border-radius: 50%;">
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="u-nav-item"><a href="/login.php" class="u-button-style u-nav-link">Giriş Yap</a></li>
                                <li class="u-nav-item"><a href="/register.php" class="u-button-style u-nav-link">Kayıt Ol</a></li>
                            <?php endif; ?>
                </ul>
            </div>
            <div class="u-custom-menu u-nav-container-collapse">
                <div class="u-black u-container-style u-inner-container-layout u-opacity u-opacity-95 u-sidenav">
                    <div class="u-inner-container-layout u-sidenav-overflow">
                        <div class="u-menu-close"></div>
                        <ul class="u-align-center u-nav u-popupmenu-items u-unstyled u-nav-2">
                             <?php if ($user_id): ?>
                                <li class="u-nav-item">
                                    <a class="u-button-style u-nav-link" href="/profile.php">
                                        <img src="/uploads/users_profile/<?php echo htmlspecialchars($profile_img); ?>" alt="Profil Fotoğrafı" style="width: 30px; height: 30px; border-radius: 50%;"> Profil
                                    </a>
                                </li>
                                <li class="u-nav-item"><a class="u-button-style u-nav-link" href="logout.php">Çıkış Yap</a></li>
                            <?php else: ?>
                                <li class="u-nav-item"><a href="/login.php" class="u-button-style u-nav-link">Giriş Yap</a></li>
                                <li class="u-nav-item"><a href="/register.php" class="u-button-style u-nav-link">Kayıt Ol</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="u-black u-menu-overlay u-opacity u-opacity-70"></div>
            </div>
            <style class="menu-style">
                @media (max-width: 539px) {
                    [data-responsive-from="XS"] .u-nav-container {
                        display: none;
                    }

                    [data-responsive-from="XS"] .menu-collapse {
                        display: block;
                    }
                }
            </style>
        </nav>
    </div>
</header>
