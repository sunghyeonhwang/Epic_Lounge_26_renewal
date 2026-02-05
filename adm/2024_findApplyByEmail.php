<?
include_once "../common.php";
echo $mode = $_POST['mode'];
$mode2 = $_GET['mode2'];

if ($mode == 'write') {


} else if ($mode == 'modify') {

} else if ($mode2 == 'del') {

    $apply_no = $_GET['no'];
    $mysqli = new mysqli("localhost","unrealengine","Good121930!@","unrealengine");


    $sql = "select * from cb_unreal_2024_speaker_apply where apply_no = '$apply_no' limit 1";

    $result = $mysqli -> query($sql);
    $obj = $result -> fetch_array();
    $mail =  'del:'.$obj['apply_user_email'];
    $apply_user_phone =  'del:'.$obj['apply_user_phone'];
    $apply_ci =  'del:'.$obj['apply_ci'];
    $apply_di =  'del:'.$obj['apply_di'];

    $sql2 = "UPDATE cb_unreal_2024_speaker_apply SET
  apply_user_email = '$mail',
  apply_user_phone = '$apply_user_phone',
  apply_ci = '$apply_ci',
  apply_di = '$apply_di'
  WHERE apply_no = '$apply_no'";


    $sql2 = "DELETE FROM cb_unreal_2024_speaker_apply WHERE apply_no = '$apply_no'";
    $result = $mysqli -> query($sql2);


    $mysqli -> close();
    echo '<script type="text/javascript">';
    echo "location.replace('/v3/adm/2024_event_list.php');";
    echo '</script>';
    exit();

}

?>