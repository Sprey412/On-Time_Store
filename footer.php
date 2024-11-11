<!-- Футер сайта -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <!-- Логотип и краткое описание -->
                <div class="col-md-4 col-sm-12">
                    <img src="assets/images/ontime_logo_calibri.png" alt="лого сайта" class="footer-logo">
                    <p class="footer-description">Магазин часов On-Time предлагает широкий выбор брендовых часов от ведущих производителей мира.</p>
                    <div class="footer-item">
                        <?php if (isset($_SESSION['employee_id'])): ?>
                            <a href="dashboard.php" class="lk_btn btn-light">Личный кабинет</a>
                        <?php else: ?>
                            <a href="login.php" class="lk_btn btn-light">Личный кабинет</a>
                        <?php endif; ?>
                    </div>
                  </div>

                <!-- Контактная информация -->
                <div class="col-md-4 col-sm-12">
                    <h4 class="footer-title">Контакты</h4>
                    <ul class="footer-contact">
                        <li><i class="fas fa-map-marker-alt"></i> Адрес: г. Чехов, Симферопольское шоссе, 1</li>
                        <li><i class="fas fa-phone"></i> Телефон: +7 (985) 722-01-98</li>
                        <li><i class="fas fa-envelope"></i> Email: yuliyamajor@yandex.ru</li>
                    </ul>
                </div>

                <!-- Социальные сети и ссылки -->
                <div class="col-md-4 col-sm-12">
                    <h4 class="footer-title">Мы в соцсетях</h4>
                    <ul class="footer-socials">
                        <li><a href="#"><i class="fab fa-vk"></i>coming soon</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <p class="footer-copyright">© 2024 On-Time. Все права защищены.</p>
                </div>
            </div>
        </div>
    </footer>