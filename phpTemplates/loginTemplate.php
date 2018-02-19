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


  <form class="form container"
        action="./login.php"
        method="post"
  > <!-- form--invalid -->

    <h2>Вход</h2>

    <div class="form__item
    <?= array_key_exists("email", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label for="email">E-mail*</label>
      <input
          id="email"
          type="text"
          name="email"
          placeholder="Введите e-mail"
          required
          value=
          <?= array_key_exists("email", $savedFormUserInput)
              ?
              "\"" . $savedFormUserInput["email"] . "\""
              :
              "";
          ?>
      >
      <span class="form__error">
        <?= array_key_exists("email", $formFieldsErrors)
            ?
            $formFieldsErrors["email"]
            :
            "";
        ?>
      </span>
    </div>

    <div class="form__item form__item--last
    <?= array_key_exists("password", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label for="password">Пароль*</label>
      <input
          id="password"
          type="text"
          name="password"
          placeholder="Введите пароль"
          required
          value=
          <?= array_key_exists("password", $savedFormUserInput)
              ?
              "\"" . $savedFormUserInput["password"] . "\""
              :
              "";
          ?>
      >
      <span class="form__error">
        <?= array_key_exists("password", $formFieldsErrors)
            ?
            $formFieldsErrors["password"]
            :
            "";
        ?>
      </span>
    </div>

    <button type="submit" class="button">Войти</button>
  </form>


</main>