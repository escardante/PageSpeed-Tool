<div class="escardante-pagespeed-formcontainer">
    <div class="escardante-pagespeed-cell">
        <?php echo $escardante_psoptions['form_welcome_text']; ?>
    </div>
    <div class="escardante-pagespeed-cell form">
        <form id="escardante-pagespeed-form" class="formoid-metro-cyan" style="background-color:#FFFFFF;font-size:14px;color:#666666; min-width:150px" method="post">
            <div class="element-name">
                <label class="title">Name</label>
                <span class="nameFirst">
				<input  type="text" size="8" name="name" />
				<label class="subtitle">First *</label>
			</span><span class="nameLast">
				<input  type="text" size="14" name="last" />
				<label class="subtitle">Last</label>
			</span>
            </div>
            <div class="element-email">
                <label class="title">Email *</label>
                <input class="large" type="email" name="email" value="" />
            </div>
            <div class="element-url">
                <label class="title">Website URL *</label>
                <input class="large" type="url" name="url" value="http://" />
            </div>
            <div class="element-email">
                <input type="submit" value="<?php echo $escardante_psoptions['button_text']; ?>"/>
            </div>

        </form>
        <img class="loading-spinner" src="<?php echo plugins_url( 'spinner.svg', __FILE__ ); ?>">
    </div>
</div>

