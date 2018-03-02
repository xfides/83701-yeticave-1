<?
$formClassInvalid = (count($formFieldsErrors) !== 0) ? " form--invalid" : "";
?>

<form class="form form--add-lot container <?= $formClassInvalid ?>>"
      action="add.php"
      enctype="multipart/form-data"
      method="post">
  <!-- form--invalid -->

  <h2>Добавление лота</h2>

  <div class="form__container-two">

    <div class=" form__item
    <?= array_key_exists("lot-name", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >

      <label for="lot-name">Наименование</label>
      <input id="lot-name"
             type="text"
             name="lot-name"
             required
             placeholder="Введите наименование лота"
             value=
             <?= array_key_exists("lot-name", $savedFormUserInput)
                 ?
                 "\"" . $savedFormUserInput["lot-name"] . "\""
                 :
                 "";
             ?>
      >
      <span class="form__error">
        <?= array_key_exists("lot-name", $formFieldsErrors)
            ?
            $formFieldsErrors["lot-name"]
            :
            "";
        ?>
      </span>
    </div>

    <div class=" form__item
    <?= array_key_exists("category", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label for="category">Категория</label>
      <select id="category"
              name="category"
              required>
        <option
            <?= array_key_exists("category", $savedFormUserInput)
                ?
                ""
                :
                "selected='selected'";
            ?>
        >
          Выберите категорию
        </option>
        <option
            <?=
            array_key_exists("category", $savedFormUserInput)
            &&
            $savedFormUserInput["category"] === "Доски и лыжи"
                ?
                "selected='selected'"
                :
                "";
            ?>
        >
          Доски и лыжи
        </option>
        <option
            <?=
            array_key_exists("category", $savedFormUserInput)
            &&
            $savedFormUserInput["category"] === "Крепления"
                ?
                "selected='selected'"
                :
                "";
            ?>
        >
          Крепления
        </option>
        <option
            <?=
            array_key_exists("category", $savedFormUserInput)
            &&
            $savedFormUserInput["category"] === "Ботинки"
                ?
                "selected='selected'"
                :
                "";
            ?>
        >
          Ботинки
        </option>
        <option
            <?=
            array_key_exists("category", $savedFormUserInput)
            &&
            $savedFormUserInput["category"] === "Одежда"
                ?
                "selected='selected'"
                :
                "";
            ?>
        >
          Одежда
        </option>
        <option
            <?=
            array_key_exists("category", $savedFormUserInput)
            &&
            $savedFormUserInput["category"] === "Инструменты"
                ?
                "selected='selected'"
                :
                "";
            ?>
        >
          Инструменты
        </option>
        <option
            <?=
            array_key_exists("category", $savedFormUserInput)
            &&
            $savedFormUserInput["category"] === "Разное"
                ?
                "selected='selected'"
                :
                "";
            ?>
        >
          Разное
        </option>
      </select>
      <span class="form__error">
         <?= array_key_exists("category", $formFieldsErrors)
             ?
             $formFieldsErrors["category"]
             :
             "";
         ?>
      </span>
    </div>

  </div>

  <div class="form__item form__item--wide
  <?= array_key_exists("message", $formFieldsErrors)
      ?
      " form__item--invalid"
      :
      "";
  ?>"
  >
    <label for="message">Описание</label>
    <textarea id="message"
              name="message"
              placeholder="Напишите описание лота"
              required><?=
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

  <div class="form__item form__item--file
  <?= array_key_exists("lot-image", $formFieldsErrors)
      ?
      " form__item--invalid"
      :
      "";
  ?>"
  >
    <!-- form__item--uploaded -->

    <label>Изображение</label>

    <div class="preview">
      <button class="preview__remove" type="button">x</button>
      <div class="preview__img">
        <img src="img/avatar.jpg" width="113" height="113"
             alt="Изображение лота">
      </div>
    </div>

    <div class="form__input-file">
      <input class="visually-hidden"
             type="file"
             id="photo2"
             name="lot-image"
             value="">
      <label for="photo2">
        <span>+ Добавить</span>
      </label>
      <span class="form__error">
        <?= array_key_exists("lot-image", $formFieldsErrors)
            ?
            $formFieldsErrors["lot-image"]
            :
            "";
        ?>
      </span>
    </div>


  </div>

  <div class="form__container-three">

    <div class=" form__item form__item--small
    <?= array_key_exists("lot-rate", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label for="lot-rate">Начальная цена</label>
      <input
          id="lot-rate"
          type="number"
          name="lot-rate"
          placeholder="0" required
          value=
          <?= array_key_exists("lot-rate", $savedFormUserInput)
              ?
              "\"" . $savedFormUserInput["lot-rate"] . "\""
              :
              "";
          ?>
      >
      <span class="form__error">
        <?= array_key_exists("lot-rate", $formFieldsErrors)
            ?
            $formFieldsErrors["lot-rate"]
            :
            "";
        ?>
      </span>
    </div>

    <div class="form__item form__item--small
    <?= array_key_exists("lot-step", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label for="lot-step">Шаг ставки</label>
      <input
          id="lot-step"
          type="number"
          name="lot-step"
          placeholder="0"
          required
          value=
          <?= array_key_exists("lot-step", $savedFormUserInput)
              ?
              "\"" . $savedFormUserInput["lot-step"] . "\""
              :
              "";
          ?>
      >
      <span class="form__error">
        <?= array_key_exists("lot-step", $formFieldsErrors)
            ?
            $formFieldsErrors["lot-step"]
            :
            "";
        ?>
      </span>
    </div>

    <div class="form__item
    <?= array_key_exists("lot-date", $formFieldsErrors)
        ?
        " form__item--invalid"
        :
        "";
    ?>"
    >
      <label for="lot-date">Дата окончания торгов</label>
      <input
          class="form__input-date"
          id="lot-date"
          type="date"
          name="lot-date"
          required
          value=
          <?= array_key_exists("lot-date", $savedFormUserInput)
              ?
              "\"" . $savedFormUserInput["lot-date"] . "\""
              :
              "";
          ?>
      >
      <span class="form__error">
        <?= array_key_exists("lot-date", $formFieldsErrors)
            ?
            $formFieldsErrors["lot-date"]
            :
            "";
        ?>
      </span>
    </div>

  </div>

  <span
      class=
      "<?= count($formFieldsErrors) !== 0
          ?
          ""
          :
          "form__error form__error--bottom";
      ?>"
  >
      Пожалуйста, исправьте ошибки в форме.
    </span>

  <button type="submit" class="button">Добавить лот</button>

</form>



