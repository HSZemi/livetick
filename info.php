<?php
    $datum = getdate();
    echo "Heute ist der ".$datum['mday'].".".$datum['mon'].".".$datum['year'].". Es ist ".$datum['hours'].":".$datum['minutes'].":".$datum['seconds']." Uhr. ";
?>
