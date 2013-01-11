<?php
    foreach ($error as $message)
    {
        echo '<div class="alert alert-error"><strong>'.__('error.error_title').'</strong> '.$message.'</div>';
    }

    foreach ($success as $message)
    {
        echo '<div class="alert alert-success"><strong>'.__('success.success_title').'</strong> '.$message.'</div>';
    }
?>