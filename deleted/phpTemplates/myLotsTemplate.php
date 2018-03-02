<main>

  <nav class="nav">
    <ul class="nav__list container">
      <? foreach ($categories as $oneCategory): ?>

        <li class="nav__item">
          <a href="all-lots.html"><?= $oneCategory ?></a>
        </li>

      <? endforeach; ?>
    </ul>
  </nav>

  <section class="rates container">
    <h2>Мои ставки</h2>

    <table class="rates__list">

      <? foreach ($userRatesInfo as $indexUserRate => $userRate): ?>

        <tr class="rates__item
        <?= !is_null($userRate["id_userWinner"]) ? " rates__item--win " : ""; ?>
        <?= ($indexUserRate > 2) ? " rates__item--end " : ""; ?>"
        >
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?= $userRate['lot_urlImage'] ?>"
                   width="54"
                   height="40"
                   alt="Сноуборд">
            </div>
            <div>
              <h3 class="rates__title">
                <a href="<?= "lot.php?id=" . $userRate["lot_id"] ?>">
                  <?= filterUserString($userRate['lot_name']) ?>
                </a>
              </h3>

              <? if (!is_null($userRate["id_userWinner"])): ?>
                <p>
                  <?= $userRate["user_contacts"] ?>
                </p>
              <? endif; ?>

            </div>
          </td>
          <td class="rates__category">
            <?= $userRate['category_name'] ?>
          </td>
          <td class="rates__timer">
            <div class="timer timer--finishing <?
            if (
                ($userRate["lot_dateEnd"] === "время истекло")
                ||
                !is_null($userRate["id_userWinner"])
            ) {
              echo " timer--win ";
            }
            ?>"
            >
              <?
              if (($userRate["lot_dateEnd"] === "время истекло")) {
                echo "время истекло";
              } else if(!is_null($userRate["id_userWinner"])){
                echo "ставка уже выиграла";
              } else {
                echo $userRate["lot_dateEnd"];
              }
              ?>

            </div>
          </td>
          <td class="rates__price">
            <?= $userRate['rate_price'] ?> р.
          </td>
          <td class="rates__time">
            <?= $userRate["rate_createdAt"] ?>
          </td>
        </tr>

      <? endforeach; ?>

    </table>

  </section>

</main>
