<?php
include "db_connection.php";
?>

<div class="container">

    <form method='post' action='download.php'>
        <input type='submit' value='Export' name='Export'>

        <table border='1' style='border-collapse:collapse;'>
            <tr>
                <th>ip_address</th>
                <th>session_id</th>
                <th>login_attempt</th>
                <th>attempt_date</th>
                <th>countrycode</th>
                <th>state</th>
                <th>browser</th>
                <th>browser_version</th>
                <th>user_agent</th>
                <th>user_agent</th>
                <th>platform</th>
                <th>login_attempt</th>
                <th>login_attempt</th>
                <th>login_date</th>
                <th>logout_date</th>
                <th>duration</th>
                <th>loginstatus</th>
                <th>subpage</th>
                <th>subpage</th>
            </tr>
            <?php
            $query = "SELECT * FROM users ORDER BY id asc";
            // $query = "SELECT wp_ifiS_02session.session_id AS session_id, wp_ifiS_02session.ip_address AS ip_address,
            // wp_ifiS_02session.login_attempt AS login_attempt, wp_ifiS_02session.attempt_date AS attempt_date,
            // wp_ifiS_02session.countrycode AS countrycode, wp_ifiS_02session.state AS state,
            // wp_ifiS_02user_recognition.browser AS browser, wp_ifiS_02user_recognition.browser_version AS browser_version,
            // wp_ifiS_02user_recognition.IP AS IP, wp_ifiS_02user_recognition.user_agent AS user_agent,
            // wp_ifiS_02user_recognition.platform AS platform, wp_ifiS_02user_recognition.login_attempt AS login_attempt,
            // wp_ifiS_02user_recognition.login_date AS login_date, wp_ifiS_02user_recognition.logout_date AS logout_date,
            //wp_ifiS_02user_recognition.duration AS duration, wp_ifiS_02user_recognition.loginstatus AS loginstatus,
            // wp_ifiS_02user_recognition.subpage AS subpage FROM  wp_ifiS_02session, wp_ifiS_02user_recognition
            // WHERE wp_ifiS_02session.user_id = wp_ifiS_02user_recognition.user_id
            // AND wp_ifiS_02session.login_attempt = wp_ifiS_02user_recognition.login_attempt;"
            $result = mysqli_query($conn,$query);
            $user_arr = array();
            while($row = mysqli_fetch_array($result)){
                $id = $row['id'];
                $uname = $row['username'];
                $name = $row['name'];
                $gender = $row['gender'];
                $email = $row['email'];
                $user_arr[] = array($id,$uname,$name,$gender,$email);
                ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $uname; ?></td>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $gender; ?></td>
                    <td><?php echo $email; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
        $serialize_user_arr = serialize($user_arr);
        ?>
        <textarea name='export_data' style='display: none;'><?php echo $serialize_user_arr; ?></textarea>
    </form>
</div>