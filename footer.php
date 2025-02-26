<div class="footer container-fluid">
  <div class="footer-content container">
    <div class="row">
      <div class="footer-section about col-md-4 col-12">
        <h3 class="login text">MuSeek</h3>
        <p>
        В современном мире музыки, где творческие идеи могут быть реализованы мгновенно, веб-сервисы для загрузки и распространения музыки стали неотъемлемой частью музыкальной индустрии. Один из таких сервисов предлагает уникальные возможности для музыкантов, композиторов и любителей музыки.
        </p>
        <div class="contact">
          <span>8 (910) 546 93-12</span>
          <span><svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round">
            <path d="M22 12l-10 6L2 12l10-6 10 6z" />
            <path d="M2 12v8a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-8" />
        </svg>vvvdubovoy@gmail.com</span>
        </div>
      </div>

      <div class="footer-section links col-md-4 col-12">
        <h3>Быстрые ссылки</h3>
        <br>
        <ul>
          <a href="index.php">
            <li>Главная</li>
          </a>
          <a href="#">
            <li>О нас</li>
          </a>
          <a href="all.php">
            <li>Вся музыка</li>
          </a>
        </ul>
      </div>

      

      <div class="footer-section contact-form col-md-4 col-12">
        <?php include 'submit_feedback.php'?>
        <h3>Контакты</h3>
        <br>
        <form action="" method="post">
          <input type="email" name="email" class="text-input contact-input" placeholder="Ваш Email">
          <textarea rows="4" name="message" class="text-input contact-input" placeholder="Ваше сообщение"></textarea>
          <button type="submit" class="btn btn-big contact-btn">
            Отправить
          </button>
        </form>

        <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p> 
        <?php endif; ?>
      </div>
    
    </div>

    <div class="footer-bottom">
      MuSeek.com |
    </div>

</div>
