<?
$formClassInvalid = (count($formFieldsErrors) !== 0) ? " form--invalid" : "";
?>

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

  <form class="form container <?= $formClassInvalid ?>"
        action="signUp.php"
        method="post"
        enctype="multipart/form-data"
  > <!-- form--invalid -->

    <h2>Регистрация нового аккаунта</h2>

    <div class="form__item
    <?= array_key_exists("email", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >

      <label for="email">E-mail*</label>
      <input id="email"
             type="text"
             name="email"
             required
             placeholder="Введите e-mail"
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

    <div class="form__item
    <?= array_key_exists("password", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label for="password">Пароль*</label>
      <input id="password"
             type="text"
             name="password"
             required
             placeholder="Введите пароль"
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

    <div class="form__item
    <?= array_key_exists("name", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label for="name">Имя*</label>
      <input id="name"
             type="text"
             name="name"
             required
             placeholder="Введите имя"
             value=
             <?= array_key_exists("name", $savedFormUserInput)
                 ?
                 "\"" . $savedFormUserInput["name"] . "\""
                 :
                 "";
             ?>
      >
      <span class="form__error">
        <?= array_key_exists("name", $formFieldsErrors)
            ?
            $formFieldsErrors["name"]
            :
            "";
        ?>
      </span>
    </div>

    <div class="form__item
    <?= array_key_exists("message", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label for="message">Контактные данные*</label>
      <textarea id="message"
                name="message"
                placeholder="Напишите как с вами связаться"
                required
      ><?=
        array_key_exists("message", $savedFormUserInput)
            ?
            $savedFormUserInput["message"]
            :
            "";
        ?></textarea>
      <span class="form__error">
        <?= array_key_exists("message", $formFieldsErrors)
            ?
            $formFieldsErrors["message"]
            :
            "";
        ?>
      </span>
    </div>

    <div class="form__item form__item--file form__item--last
    <?= array_key_exists("userAvatar", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label>Аватар</label>
      <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
          <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
        </div>
      </div>
      <div class="form__input-file">
        <input class="visually-hidden"
               type="file"
               id="photo2"
               name="userAvatar"
               value="">
        <label for="photo2">
          <span>+ Добавить</span>
        </label>
      </div>
      <span class="form__error">
        <?= array_key_exists("userAvatar", $formFieldsErrors)
            ?
            $formFieldsErrors["userAvatar"]
            :
            "";
        ?>
      </span>
    </div>

    <span
        class="<?= count($formFieldsErrors) !== 0
            ?
            ""
            :
            "form__error form__error--bottom";
        ?>"
    >
      Пожалуйста, исправьте ошибки в форме.
    </span>

    <button type="submit" class="button">
      Зарегистрироваться
    </button>

    <a class="text-link" href="login.php">
      Уже есть аккаунт
    </a>

  </form>

</main>

