<?php
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    include 'user.php';
    include 'header.php';
    session::checksession();

    $pageType = 'general';
    include 'individualSessionCheck.php';
?>

<?php
    $conn = mysqli_connect('localhost', 'root', '', 'db_lr');
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "SELECT id, name FROM tabel_user WHERE type = 'recommendingOfficer' and verification_status = 1 and admin_verification_status = 1";
    $recommendingOfficerArray =  $conn->query($sql);
?>

<?php
    $loginmgs = session::get("loginmgs");
    if (isset($loginmgs)) {
        echo $loginmgs;
    }
    session::set("loginmgs",NULL);
?>
<?php
    if (isset($_GET['action']) && $_GET['action'] == "logout") {
        session::distroy(); 
    }
?>
<?php
    $db = mysqli_connect("localhost","root","","db_lr");

    if (isset($_POST['submit'])) {
        $budget_type = $_POST['budget_type'];
        $budgetType = $_POST['budgetType'];
        $comment = $_POST['comment'];
        $user_id = session::get("id");
        $recommending_officer_id = $_POST['recommending'];
        $total = $_POST['total'];
        $need = $_POST['need'];
        $advanceAmount = (double) $_POST['advanceAmount'];
        $date = date("d-m-Y");
        $stage = 2;
        $status = "unseen";

        if($total < $advanceAmount && $need == "yes")
        {
            $msg =  "<div class='alert alert-danger'><strong>অগ্রীম চাহিদা মোট চাহিদার তুলনায় বেশি</strong></div>";
        }
        else
        {
            $msg =  "<div class='alert alert-success'><strong>আপনার বাজেট আবেদনটি সম্পন্ন হয়েছে</strong></div>";
            
            $query = "INSERT INTO demand (budget_type, budgetType, comment, user_id, recommending_officer_id, total, need, advanceAmount, date, stage, status) VALUES ('$budget_type', '$budgetType', '$comment', $user_id, $recommending_officer_id, '$total', '$need', '$advanceAmount', '$date', $stage, '$status')";
            $run = mysqli_query($db, $query);
            
            $budget_id = mysqli_insert_id($db);

            for($i=0; $i<sizeof($_POST['item_name']); $i++){
                $item = $_POST['item_name'][$i];
                $qty = $_POST['qty'][$i];
                $price = $_POST['price'][$i];
                $item_total = $_POST['subtotal'][$i];
                $total += $item_total;
                $query1 = "SELECT * FROM demand_chart WHERE budget_id='$budget_id' and item='$item' and qty='$qty' and price='$price' and item_total='$item_total'";
                $run1 = mysqli_query($db, $query1);
                if(mysqli_num_rows($run1) == 0){
                  $query = "INSERT INTO demand_chart (budget_id, item, qty, price, item_total) VALUES ('$budget_id','$item', '$qty', '$price', '$item_total')";
                  $run = mysqli_query($db, $query);
                }
              }

            session::set("loginmgs", $msg);
            $_SESSION['status'] = "Data Inserted";
            $msg =  "<div class='alert alert-success'><strong>আপনার বাজেট আবেদন সম্পন্ন হয়েছে</strong></div>";
            header("Location: index.php");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>চাহিদার বিবারণ</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="css/descriptionOfDemand.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="dist/jautocalc.js"></script>
        <script type="text/javascript">
        
            $(document).ready(function() {

                function autoCalcSetup() {
                    $('form[name=cart]').jAutoCalc('destroy');
                    $('form[name=cart] tr[name=line_items]').jAutoCalc({keyEventsFire: true, decimalPlaces: 2, emptyAsZero: true});
                    $('form[name=cart]').jAutoCalc({decimalPlaces: 2});
                }
                autoCalcSetup();


                $('i[name=remove]').click(function(e) {
                    e.preventDefault();

                    var form = $(this).parents('form')
                    $(this).parents('tr').remove();
                    autoCalcSetup();

                });

                $('i[name=add]').click(function(e) {
                    e.preventDefault();

                    var $table = $(this).parents('table');
                    var $top = $table.find('tr[name=line_items]').first();
                    var $new = $top.clone(true);

                    $new.jAutoCalc('destroy');
                    $new.insertBefore($top);
                    $new.find('input[type=text]').val('');
                    autoCalcSetup();

                });

            });

        </script>
</head>
<body>
    <?php
        include 'navbar.php';
    ?>
    <?php
        if (isset($msg)) {
            echo $msg;
        }
    ?>
    <div style="max-width: 1000px; margin: auto">
        <div class="accordion container mt-3" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        কাজ/সেবা/মালামাল ক্রয়ের প্রশাসনিক ও আর্থিক অনুমোদন
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <?php
                            include 'applyForNoMoney.php';
                        ?>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    কাজ/সেবা/মালামাল ক্রয়ের প্রশাসনিক/আর্থিক অনুমোদন এবং অগ্রিম প্রদান প্রসঙ্গে
                </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div class="accordion-body" id="background">
                        <?php
                            include 'applyForMoney.php';
                        ?>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    কাজ/সেবা/মালামাল সংগ্রহের জন্য কমিটি অনুমোদন
                </button>
                </h2>
                <?php
                    include 'committee.php';
                ?>
            </div>
        </div>
    </div>
    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>