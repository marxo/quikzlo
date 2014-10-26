<?php
ob_start();
define('QZ_VER', "0.0.4");
session_start();

if (isset($_GET['zl']))
{
  $_SESSION['zl-lang'] = $_GET['zl'];
}

if (!isset($_SESSION['qz-file']))
{
  $_SESSION['qz-file'] = NULL;
}

$target_dir = "tmp/";

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <title>QuikZLO <?php echo QZ_VER ?></title>

    <style>
        html {
            box-sizing: border-box;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        label {
            display: block;
        }
    </style>
</head>
<body>

<?php
$_GET['qz-page'] = isset($_GET['qz-page']) ? $_GET['qz-page'] : NULL; ?>
<h1>QuikZLO <?php echo QZ_VER ?></h1>
<?php

if (isset($_SESSION['qz-id'])): ?>
<p>Your session code is: <code> <?php echo qz_code_perm(); ?> </code> <a href="?qz-page=dl">download current file</a></p>

<?php
endif; ?>
<a href="?qz-page=home">Home</a>
<a href="?qz-page=create">Create a new translation</a>
<a href="?qz-page=add">Add translation text</a>
<a href="?qz-page=edit">Edit translation</a>
<?php

if ($_GET['qz-page'] === "create"): ?>

<h2>Create new translation</h2>
<form method="POST" name="np" id="np">
    <label for="np-var">Language code (ZLID)</label>
    <input type="text" list="jez" name="np-var" id="np-var" required>
    <datalist id="jez">
    <label>Or pick a language</label>
      <select>
        <option value="srp">српски језик (Serbian)</option>
        <option value="srp_RS@Latn">srpski jezik (Serbian)</option>
        <option value="eng">English</option>
      </select>
    </datalist>
    <label for="np-prv">Translator</label>
    <input type="text" name="np-prv" id="np-prv">
    <label for="np-mail">Translation e-mail</label>
    <input type="email" name="np-mail" id="np-mail">
    <label for="np-www">Website</label>
    <input type="url" name="np-www" id="np-www" value="http://">
    <label for="np-ver">Version</label>
    <input type="text" name="np-ver" id="np-ver">
    <label for="np-enc">Charset</label>
    <select name="np-enc" id="np-enc">
        <option value="UTF-8" selected readonly>UTF-8</option>
        <option value="iso-8859-1">iso-8859-1</option>
    </select>
    <label for="np-dir">Direction of text</label>
    <select name="np-dir" id="np-dir">
        <option value="ltr">Left to right</option>
        <option value="rtl">Right to left</option>
    </select>
    <button type="submit" name="np-submit">Save</button>
</form>


<?php
  if (isset($_POST['np-submit']))
  {
    $_SESSION['qz-id'] = uniqid('qz_');
    $_SESSION['qz-file'] = $target_dir . $_SESSION['qz-id'] . ".zl";
    if (!file_exists($_SESSION['qz-file']))
    {
        mkdir($target_dir, 0777, true);
    }
    fopen($_SESSION['qz-file'], "w");
    $header = "ZLO\n" . "VAR " . $_POST['np-var'] . "\n" . "VER " . $_POST['np-ver'] . "\n" . "REV " . date("c") . "\n" . "PRV " . $_POST['np-prv'] . "\n" . "PRE " . $_POST['np-mail'] . "\n" . "PRU " . $_POST['np-www'] . "\n" . "CHR " . $_POST['np-enc'] . "\n" . "BDO " . $_POST['np-dir'] . "\n" . str_repeat("@@@\n", 6) . "GEN QuikZLO " . QZ_VER . "\n" . "\n";
    file_put_contents($_SESSION['qz-file'], $header);
    header("Location: ?qz-page=add");
  } ?>



<?php
elseif ($_GET['qz-page'] === "add"): ?>

<?php

    if (file_exists($_SESSION['qz-file']) && file_get_contents($_SESSION['qz-file'])[0])
    {
          $cf = file($_SESSION['qz-file']);
    } elseif (!file_exists($_SESSION['qz-file'])) { ?>
    <h2>You don't have a header in your file or it is malformed</h2>
    <h3>Create a new translation or check you file for errors in a text editor</h3>
    <?php } ?>
<h2>Add translations</h2>

<form method="POST">
    <label for="pr-izvor">Source text</label>
    <input dir="<?php echo @trim(substr($cf[8], 4)) ?>" type="text" name="pr-izvor" id="pr-izvor">
    <label for="pr-prevod">Translation text</label>
    <input dir="<?php echo @trim(substr($cf[8], 4)) ?>" type="text" name="pr-prevod" id="pr-prevod">
    <label for="pr-izvor-pl">Source plural</label>
    <input dir="<?php echo @trim(substr($cf[8], 4)) ?>" type="text" name="pr-izvor-pl" id="pr-izvor-pl">
    <label for="pr-prevod-2">Translation plural 2</label>
    <input dir="<?php echo @trim(substr($cf[8], 4)) ?>" type="text" name="pr-prevod-2" id="pr-prevod-2">
    <label for="pr-prevod-3">Translation plural 3</label>
    <input dir="<?php echo @trim(substr($cf[8], 4)) ?>" type="text" name="pr-prevod-3" id="pr-prevod-3">
    <button type="submit" name="pr-submit">Add</button>
</form>
<?php

  if (isset($_POST['pr-submit']))
  {
    $line = "\n";
    $line .= "!i " . trim($_POST['pr-izvor']) . "\n" . "!m " . trim($_POST['pr-prevod']) . "\n" . "!p " . trim($_POST['pr-izvor-pl']) . "\n" . "!2 " . trim($_POST['pr-prevod-2']) . "\n" . "!3 " . trim($_POST['pr-prevod-3']) . "\n";
    file_put_contents($_SESSION['qz-file'], $line, FILE_APPEND);
  }

  if (isset($_SESSION['qz-file']) && file_exists($_SESSION['qz-file']))
  {
    echo "<pre>", file_get_contents($_SESSION['qz-file']) , "</pre>";
  }

?>

<?php
elseif ($_GET['qz-page'] === "edit"): ?>


<!-- EDIT FORM -->
<form method="POST">
<?php
  if (file_exists($_SESSION['qz-file']))
  {
    $cf = file($_SESSION['qz-file']);
    for ($i = 16; $i < count($cf); $i++)
    {
      if (strlen($cf[$i]) > 4 && $cf[$i][1] === "i")
      { ?>

    <label for="<?php echo $i; ?>"><h3><?php echo @substr($cf[$i], 3); ?></h3></label>
    <textarea dir="<?php echo @trim(substr($cf[8], 4)) ?>" name="<?php echo $i; ?>" id="<?php echo $i; ?>" cols="30" rows="3"><?php echo @substr($cf[$i + 1], 3); ?></textarea>


<?php
      }
    }

    if (isset($_POST['ed-submit']))
    {
      $cf[3] = "REV " . date('c') . "\n";
      for ($i = 16; $i < count($cf); $i++)
      {
        if (!empty($_POST[$i]))
        {
          $cf[$i + 1] = "!m " . trim($_POST[$i]) . "\n";
        }
      }

      file_put_contents($_SESSION['qz-file'], $cf);
      header("Location: #");
    }

?>
 <button type="submit" name="ed-submit">Save changes</button>
</form>
<?php
  }
  else
  { ?>
<h2>No file loaded to edit</h2>
<?php
  } ?>

<?php
else: ?>

    <h2>Statis</h2>

    <h3>Upload a new file</h3>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
        <input type="file" name="qz-data-upload" id="qz-data-upload">
        <button type="submit" name="ul-submit">Upload file</button>
    </form>

    <h3>Or enter your code</h3>

    <form method="post">
        <input type="text" name="qz-file-code">
        <button type="submit">Confirm code</button>
    </form>

    <?php
  if (isset($_POST['ul-submit']))
  {
    $_SESSION['qz-id'] = uniqid('qz_');
    $_SESSION['qz-file'] = $target_dir . $_SESSION['qz-id'] . ".zl";
    $target = $_SESSION['qz-file'];
    move_uploaded_file($_FILES["qz-data-upload"]["tmp_name"], $target);
    header("Location: #");
  }
  elseif (isset($_POST['qz-file-code']))
  {
    if (file_exists($target_dir . "qz_" . $_POST['qz-file-code'] . ".zl"))
    {
      $_SESSION['qz-id'] = "qz_" . $_POST['qz-file-code'];
      $_SESSION['qz-file'] = $target_dir . "qz_" . $_POST['qz-file-code'] . ".zl";
      header("Location: #");
    }
    else
    {
      echo "Code you've entered is bad! Naughty, naughty boy.";
    }
  }

?>


<?php
endif; ?>


<?php

function qz_code_perm()
{
  return explode("_", $_SESSION['qz-id'])[1];
}

?>

</body>
</html>

<?php

if ($_GET['qz-page'] === "dl"): ?>
<?php
  if (file_exists($_SESSION['qz-file']))
  {
    $cf = file($_SESSION['qz-file']);
    $cf[3] = "REV " . date('c') . "\n";
    $rf = implode('', $cf);
    file_put_contents($_SESSION['qz-file'], $rf);
    flush();
    ob_clean();
    header('Content-Disposition: attachment; filename="' . trim(substr($cf[1], 3)) . '.zl"');
    readfile($_SESSION['qz-file']);
  }

?>
<?php
endif; ?>
