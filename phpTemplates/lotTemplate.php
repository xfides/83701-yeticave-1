<main>

  <nav class="nav">
    <ul class="nav__list container">

      <? foreach ($categories as $oneCategory):?>

        <li class="nav__item">
          <a href="all-lots.html"><?=$oneCategory?></a>
        </li>

      <?endforeach; ?>

    </ul>
  </nav>

  <section class="lot-item container">

    <h2><?= $lotInfo['name'] ?></h2>

    <div class="lot-item__content">
      <div class="lot-item__left">
        <div class="lot-item__image">
          <img src="<?= $lotInfo['urlImg'] ?>" width="730" height="548"
               alt="Сноуборд">
        </div>
        <p class="lot-item__category">
          Категория:
          <span><?= $lotInfo['category'] ?></span>
        </p>
        <p class="lot-item__description">
          <?= $lotInfo["text"]?>
        </p>
      </div>
      <div class="lot-item__right">


        <div class="lot-item__state
        <?=
        isset($_SESSION["userName"])  && $showRateForm
            ?:
            " visually-hidden";
        ?>"
        >
          <div class="lot-item__timer timer">
            <?= $lotInfo["dateEnd"]?>
          </div>
          <div class="lot-item__cost-state">
            <div class="lot-item__rate">
              <span class="lot-item__amount">Текущая цена</span>
              <span class="lot-item__cost"><?= $lotInfo['price'] ?></span>
            </div>
            <div class="lot-item__min-cost">
              Мин. ставка
              <span>
                <?=$lotInfo['minRate']?>
              </span>
            </div>
          </div>

          <form class="lot-item__form" action="lot.php"
                method="post">

            <p class="lot-item__form-item
            <?= array_key_exists("cost", $formFieldsErrors)
                ?
                " form__item--invalid"
                :
                "";
            ?>"
            >
              <label for="cost">Ваша ставка</label>
              <input id="cost"
                     type="number"
                     name="cost"
                     placeholder="12 000"
                     value=
                     <?= array_key_exists("cost", $savedFormUserInput)
                         ?
                         "\"" . $savedFormUserInput["cost"] . "\""
                         :
                         "";
                     ?>
              >
              <input class="visually-hidden"
                     name="idLot"
                     type="number"
                     value="<?= $idLot ?>"
              />

              <span class="form__error">
                <?= array_key_exists("cost", $formFieldsErrors)
                    ?
                    $formFieldsErrors["cost"]
                    :
                    "";
                ?>
              </span>
            </p>

            <button type="submit" class="button">Сделать ставку</button>

          </form>

        </div>


        <div class="history">
          <h3>История ставок
              (<span><?= (count($bets) > 0) ? count($bets): 0; ?></span>)
          </h3>
          <!-- заполните эту таблицу данными из массива $bets-->
          <table class="history__list">

            <? foreach ($bets as  $bet): ?>


              <tr class="history__item">
                <td class="history__name"><?= $bet['name'] ?></td>
                <td class="history__price"><?= $bet['price'] ?> р</td>
                <td class="history__time">
                  <?= $bet['ts'] ?>
                </td>
              </tr>


            <? endforeach; ?>


          </table>
        </div>
      </div>
    </div>

  </section>

</main>

