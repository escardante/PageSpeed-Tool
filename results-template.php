<script>
    window.GOOGLE_API_KEY = '<?php echo $escardante_psoptions['api_key'];?>';
    window.URL_TO_GET_RESULTS_FOR = '<?php echo $url_pst;?>';
    <?php if(!$url_pst){
        echo 'var redirectTo = "' . get_permalink_by_slug( $escardante_psoptions['form_page'], 'page' )  . '";';
        echo 'location.href = redirectTo;';
    }
    ?>
</script>

<div class="pagespeed-results">
    <h2>Results for <?php echo $url_pst;?></h2>
    <img class="loading-spinner" src="<?php echo plugins_url( 'spinner.svg', __FILE__ ); ?>">
    <div class="tabs">

    </div>
    <ul id="template-list" style="display:none;">
        <li class="red"> = Severe problem, must fix immediately</li>
        <li class="yellow"> = Moderated issue</li>
        <li class="green"> = Passed test</li>
    </ul>
</div>