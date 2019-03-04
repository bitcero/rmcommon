<script type="text/javascript">
$(document).ready(function(){
    $("#<?php echo $this->name; ?>").uploadify({
        <?php
        $rtn = '';
        foreach ($this->settings() as $name => $value):

        if ('' == $value) {
            continue;
        }
        if (!is_array($value) && 'function' == mb_substr($value, 0, 8)) {
            $value = $value;
        } elseif (is_string($value)) {
            $value = "'$value'";
        } elseif (is_array($value)) {
            $tmp = '';
            foreach ($value as $k => $val) {
                $tmp .= '' == $tmp ? "'$k':'$val'" : ",'$k':'$val'";
            }
            $value = '{' . $tmp . '}';
        }
        $rtn .= '' == $rtn ? "'$name': $value" : ",\n'$name': $value";

        endforeach;
        echo $rtn;
        ?>
    });
});
</script>
