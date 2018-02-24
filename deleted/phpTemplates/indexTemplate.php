<main class="container">

  <section class="promo">

    <h2 class="promo__title">Нужен стафф для катки?</h2>

    <p class="promo__text">
      На нашем интернет-аукционе ты найдёшь самое
      эксклюзивное сноубордическое и горнолыжное
      снаряжение.
    </p>

    <ul class="promo__list">

      <? foreach ($goodsCategories as $indexCategory => $category): ?>

        <li class="promo__item <?= $classesForCategories[$indexCategory] ?>">
          <a class="promo__link" href="all-lots.html">
            <?= $category ?>
          </a>
        </li>

      <? endforeach; ?>

    </ul>

  </section>

  <section class="lots">

    <div class="lots__header">

      <h2>Открытые лоты</h2>

    </div>

    <ul class="lots__list">

      <? foreach ($goodsAds as $ad): ?>

        <li class="lots__item lot">

          <div class="lot__image">

            <img src="<?= $ad['urlImg'] ?>"
                 width="350"
                 height="260"
                 alt="Сноуборд"/>

          </div>

          <div class="lot__info">

            <span class="lot__category">
              <?= $ad['category'] ?>
            </span>

            <h3 class="lot__title">
              <a class="text-link"
                 href="<?= "lot.php?id=" . $ad["id"] ?>">
                <?= filterUserString($ad['name']) ?>
              </a>
            </h3>

            <div class="lot__state">

              <div class="lot__rate">
                <span class="lot__amount">Стартовая цена</span>
                <span class="lot__cost">
                  <?= $ad['price'] ?>
                  <b class="rub">р</b>
                </span>
              </div>

              <div class="lot__timer timer">
                <?= $ad["dateEnd"]; ?>
              </div>

            </div>

          </div>

        </li>

      <? endforeach; ?>

    </ul>

  </section>

</main>