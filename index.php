<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1.0">
    <title>Woogla</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="public/css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Roboto:300,400,500" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

  <nav>
        <div class="container-fluid">
          <a class="navbarBrand" href="index.php"><img src="public/img/logo.png" alt=""></a>
          <a class="navbarMenu" href="#"><i class="fa fa-th" aria-hidden="true"></i></a>
          <div class="navMenu">
            <a href="#">A</a>
            <a href="#">B</a>
            <a href="#">C</a>
          </div>
        </div>
      </nav>

      <main>

        <div class="container">

          <div class="searchWrapper">
            <form class="searchForm" action="">
              <input class="searchInput" id="search" type="text" name="searchInput" autofocus>
              <label class="searchLabel" for="search">What you are looking for?</label>
              <a class="searchVoice" href="javascript:void(0)"><i class="fa fa-microphone" aria-hidden="true"></i></a>
              <span class="bar"></span>
              <button class="searchBtn" type="submit">Search</button>
            </form>
          </div>

        </div>
        
      </main>

      <footer>
        <div class="container-fluid">
          <a class="footerAbout" href="#">About</a>
          <a class="footerSettings" href="#">Settings</a>
        </div>
      </footer>


  <script src="public/js/bundle.js"></script>
</body>
</html>
