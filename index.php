<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MuSeek</title>
    <link rel="icon" href="images/waves.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
  </head>
  <body>

<?php
  session_start(); 

if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']); 
}
?>

<?php include 'enter.php';?>
<?php include 'header.php';?>
  
<div class="container">
    <div class="row">
      <h2 class="slider-title">Музыка для любого случая!</h2>
    </div>
    <div id="carouselExampleCaptions" class="carousel slide">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="images/image_1.jpeg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-flex justify-content-center align-items-center">
              <h5><a href="all.php">Здесь собрана разная музыка</a></h5>
            </div>
          </div>
          <div class="carousel-item">
            <img src="images/image_2.jpeg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-flex justify-content-center align-items-center">
              <h5><a href="all.php">Находите для себя что-то новое</a></h5>
            </div>
          </div>
          <div class="carousel-item">
            <img src="images/image_3.jpeg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-flex justify-content-center align-items-center">
              <h5><a href="add.php">Делитесь своей музыкой с другими!</a></h5>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
</div>

<div class="container">
  <div class="content row">
    <div class="main-content col-md-9 col-12">
      <h2>Все песни</h2>

      <div class="container mt-4">
    <div id="songs-list">
        <?php include 'upload.php'; ?>
    </div>
</div>
    </div>

    
      
    <div class="sidebar col-md-3 col-12">
      
      <div class="section search">
      <?php include 'search.php'; ?>
        <h3>Поиск</h3>
        <form action="/" method="post" class="search-form">
          <input type="text" name="search-term" class="text-input" placeholder="Введите название...">
          <button type="submit" class="search-button" aria-label="Поиск">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="2 2 20 20">
          <circle cx="10" cy="10" r="6" />
          <path d="M15.5 15.5l4.5 4.5" />
          </svg>
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<?php include 'footer.php'?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    
  </body>
</html>