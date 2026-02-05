<?
include_once "../common.php";
echo $mode = $_POST['mode'];
$mode2 = $_GET['mode2'];

if ($mode == 'write') {


} else if ($mode == 'modify') {

} else if ($mode2 == 'del') {

    $apply_no = $_GET['no'];
    $mysqli = new mysqli("localhost","unrealengine","Good121930!@","unrealengine");


    $sql2 = "DELETE FROM cb_unreal_2025_speaker_apply WHERE id = '$apply_no'";
    $result = $mysqli -> query($sql2);


    $mysqli -> close();
    echo '<script type="text/javascript">';
    echo "location.replace('/v3/adm/2025_event_speaker.php');";
    echo '</script>';
    exit();

}

?>