<?php

namespace App\Controller;

use App\Lib\Config;

$hostAPI = Config::get('CONNECTION')['API']['host'];

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact</title>

  <?php include_once __DIR__ . '/../Partials/styles.html' ?>

  <style>
    .container {
      max-width: 960px;
      margin-bottom: 50px;
    }
  </style>
</head>

<body>
  <?php include_once __DIR__ . '/../Partials/loading.html' ?>
  <?php include_once __DIR__ . '/../Partials/toast.html' ?>
  <?php include_once __DIR__ . '/../Partials/navbar.html' ?>

  <main id="content" class="container">
    <div class="py-5 text-center">
      <h2><?= $title ?></h2>
      <p class="lead">Complete the fields of the form below.</p>
    </div>

    <div class="row">
      <div class="col-md-7 col-lg-8 mx-auto">
        <form class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-sm-6">
              <label for="firstname" class="form-label">First Name</label>
              <input type="text" class="form-control" id="firstname" placeholder="" value="" required>
              <div class="invalid-feedback">
                Valid first name is required.
              </div>
            </div>

            <div class="col-sm-6">
              <label for="lastname" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="lastname" placeholder="" value="" required>
              <div class="invalid-feedback">
                Valid last name is required.
              </div>
            </div>

            <div class="col-sm-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
              <div class="invalid-feedback">
                Valid email address is required.
              </div>
            </div>

            <div class="col-sm-6">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="phone" placeholder="+1 555 555" required>
              <div class="invalid-feedback">
                Valid phone number is required.
              </div>
            </div>
          </div>

          <hr class="my-4">

          <div class="row">
            <div class="col-sm-6 mx-auto">
              <button class="w-100 btn btn-primary btn-lg" type="submit">Save</button>
            </div>
            <div class="col-sm-6 mx-auto">
              <a class="w-100 btn btn-light btn-lg" role="button" href="/">Go back</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>

  <?php include_once __DIR__ . '/../Partials/scripts.html' ?>

  <script>
    $(document).ready(function() {
      setActiveLink('last')

      $('#content').show()

      <?php
      if ($action === 'edit') { ?>
        getContact(<?= $id ?>)
      <?php } ?>

      $('#content form').submit(function(event) {
        validateForm()

        <?php
        if ($action === 'new') { ?>
          addContact(event);
        <?php } else { ?>
          updateContact(event, <?= $id ?>);
        <?php } ?>
      });
    })
  </script>

</body>

</html>
