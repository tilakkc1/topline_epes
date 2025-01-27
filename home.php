<?php include('db_connect.php') ?>
<?php
$twhere ="";
if($_SESSION['login_type'] != 1)
  $twhere = "  ";
function eng_to_nepali_num_convert($eng_num){
  $default_val = array(
    0 => '०',
    1 => '१',
    2 => '२',
    3 => '३',
    4 => '४',
    5 => '५',
    6 => '६',
    7 => '७',
    8 => '८',
    9 => '९',
  );
  $nepali_vals = '';
  $eng_num_arr = str_split($eng_num);
  $cnt = strlen($eng_num);
  for($i=0;$i<$cnt;$i++)
  {
    if(!isset($default_val[$eng_num_arr[$i]]))
    {
      $nepali_vals .= $eng_num_arr[$i];
    }
    else
      $nepali_vals .= $default_val[$eng_num_arr[$i]];
  }
  return $nepali_vals;

}
?>
<!-- Info boxes -->
<?php if($_SESSION['login_type'] == 2): ?>
  <div class="row">
    <div class="col-12 col-sm-6 col-md-4">
      <div class="small-box bg-light shadow-sm border">
        <div class="inner">
          <h3><?php echo eng_to_nepali_num_convert($conn->query("SELECT * FROM department_list ")->num_rows); ?></h3>
          <p>जम्मा शाखाहरु</p>
        </div>
        <div class="icon">
          <i class="fa fa-th-list"></i>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="small-box bg-light shadow-sm border">
        <div class="inner">
          <h3><?php echo eng_to_nepali_num_convert($conn->query("SELECT * FROM designation_list")->num_rows); ?></h3>
          <p>जम्मा पदहरु</p>
        </div>
        <div class="icon">
          <i class="fa fa-list-alt"></i>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="small-box bg-light shadow-sm border">
        <div class="inner">
          <h3><?php echo eng_to_nepali_num_convert($conn->query("SELECT * FROM users")->num_rows); ?></h3>

          <p>जम्मा प्रयोगकर्ता</p>
        </div>
        <div class="icon">
          <i class="fa fa-users"></i>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="small-box bg-light shadow-sm border">
        <div class="inner">
          <h3><?php echo eng_to_nepali_num_convert($conn->query("SELECT * FROM employee_list")->num_rows); ?></h3>

          <p>जम्मा कर्मचारी</p>
        </div>
        <div class="icon">
          <i class="fa fa-user-friends"></i>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="small-box bg-light shadow-sm border">
        <div class="inner">
          <h3><?php echo eng_to_nepali_num_convert($conn->query("SELECT * FROM evaluator_list")->num_rows); ?></h3>

          <p>जम्मा मुल्याङ्कनकर्ता</p>
        </div>
        <div class="icon">
          <i class="fa fa-user-secret"></i>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="small-box bg-light shadow-sm border">
        <div class="inner">
          <h3><?php echo eng_to_nepali_num_convert($conn->query("SELECT * FROM task_list")->num_rows); ?></h3>

          <p>जम्मा काम</p>
        </div>
        <div class="icon">
          <i class="fa fa-tasks"></i>
        </div>
      </div>
    </div>
  </div>

<?php else: ?>
 <div class="col-12">
  <div class="card">
    <div class="card-body">
      Welcome <?php echo $_SESSION['login_name'] ?>!
    </div>
  </div>
</div>

<?php endif; ?>
