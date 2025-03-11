<header class="container-fluid"> 
    <div class="container"> 
        <div class="row"> 
            <div class="col-4"> 
                <h1> 
                    <a href="index.php">
                        MuSeek
                        <img src="images/waves.png" alt="Логотип MuSeek" class="logo">
                    </a> 
                </h1> 
            </div> 
            <div class="col-8">
                <div class="burger" id="burger">
                    &#9776;
                </div>
                <nav id="nav"> 
                    <ul> 
                        <li><a href="index.php">Главная</a></li> 
                        <li><a href="all.php">Вся музыка</a></li> 
                        <?php if (isset($_SESSION['id'])): ?> <!-- Проверка, авторизован ли пользователь -->
                            <li><a href="add.php">Добавить песню</a></li> 
                            <li>
                                <a href="profile.php">  
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30"> 
                                        <path d="M18,19v-2c0.45-0.223,1.737-1.755,1.872-2.952c0.354-0.027,0.91-0.352,1.074-1.635c0.088-0.689-0.262-1.076-0.474-1.198 c0,0,0.528-1.003,0.528-2.214c0-2.428-0.953-4.5-3-4.5c0,0-0.711-1.5-3-1.5c-4.242,0-6,2.721-6,6c0,1.104,0.528,2.214,0.528,2.214 c-0.212,0.122-0.562,0.51-0.474,1.198c0.164,1.283,0.72,1.608,1.074,1.635C10.263,15.245,11.55,16.777,12,17v2c-1,3-9,1-9,8h24 C27,20,19,22,18,19z"></path> 
                                    </svg> 
                                    Мой профиль
                                </a>
                            </li>
                            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === 1): ?>
                                <li><a href="admin.php">Админ панель</a></li>
                            <?php endif; ?>
                            <li><a href="logout.php">Выход</a></li>
                        <?php else: ?>
                            <li><a href="register.php">Регистрация</a></li> 
                            <li><a href="login.php">Вход</a></li>
                        <?php endif; ?>
                    </ul> 
                </nav>
            </div>
        </div> 
    </div> 
</header>
<script src="js/script.js"></script>