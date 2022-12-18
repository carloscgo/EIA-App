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
  <title>My Contacts</title>

  <?php include_once __DIR__ . '/../Partials/styles.html' ?>
</head>

<body>
  <?php include_once __DIR__ . '/../Partials/loading.html' ?>
  <?php include_once __DIR__ . '/../Partials/toast.html' ?>
  <?php include_once __DIR__ . '/../Partials/modal.html' ?>
  <?php include_once __DIR__ . '/../Partials/navbar.html' ?>

  <main id="content" class="container">
    <div class="bg-light p-5 rounded">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <caption>List of contacts</caption>

          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">First name</th>
              <th scope="col">Last name</th>
              <th scope="col">Email</th>
              <th scope="col">Phone Number</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>

          <tbody class="table-group-divider">
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php include_once __DIR__ . '/../Partials/scripts.html' ?>

  <script>
    $(document).ready(function() {
      loadContacts()
    })
  </script>
</body>

</html>
